<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use KirbyExtended\Env;
use KirbyExtended\PageMeta;
use KirbyExtended\Redirects;
use KirbyExtended\SiteMeta;

Kirby::plugin('johannschopplich/kirby-extended', [
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
                if (option('kirby-extended.robots.enable', false)) {
                    return SiteMeta::robots();
                }

                $this->next();
            }
        ],
        [
            'pattern' => 'sitemap.xml',
            'action' => function () {
                if (option('kirby-extended.sitemap.enable', false)) {
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
        'meta' => fn () => new PageMeta($this)
    ]
]);
