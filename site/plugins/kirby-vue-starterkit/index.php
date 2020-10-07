<?php

use Kirby\Cms\App as Kirby;
use Kirby\Toolkit\A;

Kirby::plugin('johannschopplich/kirby-vue-starterkit', [
    'options' => [
        'cache' => true
    ],
    'hooks' => require __DIR__ . '/hooks.php',
    'routes' => array_merge(option('routes', []), require __DIR__ . '/routes.php')
]);
