<?php

use Beebmx\KirbyEnv;
use Kirby\Cms\App as Kirby;

Kirby::plugin('beebmx/kirby-env', [
    'options' => [
        'file' => '.env',
    ],
    'pageMethods' => [
        'env' => function ($value, $default = '') {
            if (!KirbyEnv::isLoaded()) {
                $path = option('beebmx.kirby-env.path', kirby()->roots()->index());
                $file = option('beebmx.kirby-env.file', '.env');
                KirbyEnv::load($path, $file);
            }

            return env($value, $default);
        }
    ]
]);
