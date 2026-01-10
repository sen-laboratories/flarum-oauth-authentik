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
            'internal_url'  => ['type' => 'text'],
            'redirect_path' => ['type' => 'text'],
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
            'scopes'                  => ['openid', 'profile', 'email', 'user_metadata'],
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

        // We redirect to the settings value if provided, else default to /tags
        $targetPath = $this->getSetting('redirect_path') ?: 'tags';

        $registration->setPayload(array_merge($data, [
            'redirectTo' => $url->to('forum')->path($targetPath)
        ]));
    }
}
