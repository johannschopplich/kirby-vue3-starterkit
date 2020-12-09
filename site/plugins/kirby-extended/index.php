<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Field;
use KirbyExtended\EnvAdapter;

Kirby::plugin('johannschopplich/kirby-extended', [
    'pageMethods' => [
        'env' => function ($value, $default = null) {
            if (!EnvAdapter::isLoaded()) {
                EnvAdapter::load();
            }

            return env($value, $default);
        },
        'metaTags' => function ($groups = null) {
            return metaTags($this)->render($groups);
        }
    ]
]);
