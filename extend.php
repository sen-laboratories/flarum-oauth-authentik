<?php

namespace SenLabs\OAuth\Authentik;

use Flarum\Extend;
use FoF\OAuth\Extend\RegisterProvider;

return [
    (new Extend\Locales(__DIR__ . '/locale')),
    (new Extend\Frontend('forum'))
        ->css(__DIR__.'/resources/less/forum.less'),
    (new RegisterProvider(AuthentikProvider::class)),
];
