<?php

namespace SenLabs\OAuth\Authentik;

use FoF\OAuth\Provider;
use Flarum\Forum\Auth\Registration;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericProvider;

class AuthentikProvider extends Provider
{
    public function name(): string { return 'authentik'; }
    public function icon(): string { return 'fas fa-shield-halved'; }
    public function type(): string { return 'openid'; }
    public function link(): string { return 'https://goauthentik.io'; }

    public function fields(): array
    {
        return [
            'client_id'     => ['required'],
            'client_secret' => ['required'],
            'base_url'      => ['required'],
            'internal_url'  => [],
        ];
    }

    public function provider(string $callbackUrl): AbstractProvider
    {
        $externalUrl = rtrim($this->getSetting('base_url'), '/');
        $internalUrl = rtrim($this->getSetting('internal_url') ?: $externalUrl, '/');

        return new GenericProvider([
            'clientId'                => $this->getSetting('client_id'),
            'clientSecret'            => $this->getSetting('client_secret'),
            'redirectUri'             => $callbackUrl,
            'urlAuthorize'            => "$externalUrl/application/o/authorize/",
            'urlAccessToken'          => "$internalUrl/application/o/token/",
            'urlResourceOwnerDetails' => "$internalUrl/application/o/userinfo/",
            'scopes'                  => 'openid profile email',
            'responseResourceOwnerId' => 'sub'
        ]);
    }

    public function suggestions(Registration $registration, $user, string $token): void
    {
        $data = $user->toArray();

        // Sanitize username for Flarum (dots to dashes)
        $rawUsername = $data['username'] ?? $data['preferred_username'] ?? explode('@', $data['email'])[0];
        $username = str_replace('.', '-', $rawUsername);

        $registration
            ->provideTrustedEmail($data['email'])
            ->provide('username', $username)
            ->setPayload($data);

        // Redirect logic: Drops the user on the Tags page after signup
        // Note: This works best if the user is completing registration for the first time
        $registration->setPayload(array_merge($registration->getPayload(), [
            'redirectTo' => $url->to('forum')->path('tags')
        ]));
}
}
