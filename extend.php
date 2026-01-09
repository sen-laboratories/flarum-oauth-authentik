<?php

namespace SenLabs\OAuth\Authentik;

use Flarum\Extend;
use FoF\OAuth\Extend\RegisterProvider;

return [
    (new Extend\Locales(__DIR__ . '/locale')),

    (new RegisterProvider(AuthentikProvider::class)),
];
