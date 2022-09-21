<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use KirbyHelpers\Env;
use KirbyHelpers\PageMeta;
use KirbyHelpers\Redirects;
use KirbyHelpers\SiteMeta;

Kirby::plugin('johannschopplich/kirby-helpers', [
    'hooks' => [
        'route:after' => function (\Kirby\Http\Route $route, string $path, string $method, $result, bool $final) {
            if ($final && empty($result)) {
                Redirects::go($path, $method);
            }
        }
    ],
    'routes' => [
        [
            'pattern' => 'robots.txt',
            'action' => function () {
                if (option('kirby-helpers.robots.enable', false)) {
                    return SiteMeta::robots();
                }

                $this->next();
            }
        ],
        [
            'pattern' => 'sitemap.xml',
            'action' => function () {
                if (option('kirby-helpers.sitemap.enable', false)) {
                    return SiteMeta::sitemap();
                }

                $this->next();
            }
        ]
    ],
    'siteMethods' => [
        'env' => function ($key, $default = null) {
            if (!Env::isLoaded()) {
                Env::load();
            }

            return Env::get($key, $default);
        }
    ],
    'pageMethods' => [
        'meta' => function () {
            return new PageMeta($this);
        }
    ]
]);
