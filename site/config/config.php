<?php

return [

    'debug' => env('KIRBY_MODE') === 'development' || env('KIRBY_DEBUG', false),

    'languages' => env('KIRBY_MULTILANG', false),
    'languages.detect' => env('KIRBY_MULTILANG_DETECT', false),

    'panel' => [
        'install' => env('KIRBY_PANEL_INSTALL', false),
        'slug' => env('KIRBY_PANEL_SLUG', 'panel')
    ],

    'api' => [
        'slug' => env('KIRBY_API_SLUG', 'api')
    ],

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => fn (\Kirby\Cms\Page $page) => kirby()->user() !== null
        ]
    ],

    'johannschopplich' => [
        'kirby-vite' => [
            'entry' => 'main.js'
        ]
    ],

    // See https://github.com/johannschopplich/kirby-extended/blob/main/docs/meta.md
    'kirby-extended' => [
        'meta' => [
            'defaults' => function (\Kirby\Cms\App $kirby, \Kirby\Cms\Site $site, \Kirby\Cms\Page $page) {
                $description = $page->description()->or($site->description())->value();

                return [
                    'opengraph' => [
                        'image' => '/img/android-chrome-512x512.png'
                    ],
                    'twitter' => [
                        'image' => '/img/android-chrome-512x512.png'
                    ],
                    'jsonld' => [
                        'WebSite' => [
                            'url' => $site->url(),
                            'name' => $site->title()->value(),
                            'description' => $description
                        ]
                    ]
                ];
            }
        ]
    ]

];
