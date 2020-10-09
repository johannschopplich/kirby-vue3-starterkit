<?php

$base = dirname(__DIR__, 2);
\KirbyExtended\EnvAdapter::load($base);

return [

    'debug' => env('KIRBY_DEBUG', false),

    'panel' => [
        'install' => env('KIRBY_PANEL_INSTALL', false),
        'slug' => env('KIRBY_PANEL_SLUG', 'panel')
    ],

    'api' => [
        'slug' => env('KIRBY_API_SLUG', 'api')
    ],

    'routes' => require __DIR__ . '/routes.php',
    'hooks' => require __DIR__ . '/hooks.php',

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => function ($page) {
                if (kirby()->user() !== null) return true;
                $options = $page->blueprint()->options();
                return isset($options['cache']) ? !$options['cache'] : false;
            }
        ],
        'templates' => true
    ],

    'cre8ivclick.sitemapper.title' => 'Sitemap'

];
