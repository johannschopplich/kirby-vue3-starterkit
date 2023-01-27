<?php

@include_once __DIR__ . '/vendor/autoload.php';

use JohannSchopplich\Helpers\Env;
use JohannSchopplich\Helpers\PageMeta;
use JohannSchopplich\Helpers\Redirects;
use JohannSchopplich\Helpers\SiteMeta;
use Kirby\Cms\App as Kirby;

Kirby::plugin('johannschopplich/helpers', [
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
                if (option('johannschopplich.helpers.robots.enable', false)) {
                    return SiteMeta::robots();
                }

                $this->next();
            }
        ],
        [
            'pattern' => 'sitemap.xml',
            'action' => function () {
                if (option('johannschopplich.helpers.sitemap.enable', false)) {
                    return SiteMeta::sitemap();
                }

                $this->next();
            }
        ]
    ],
    'siteMethods' => [
        'env' => function ($key, $default = null) {
            if (!Env::isLoaded()) {
                $kirby = kirby();
                $path = $kirby->option('johannschopplich.helpers.env.path', $kirby->root('base'));
                $file = $kirby->option('johannschopplich.helpers.env.filename', '.env');
                Env::load($path, $file);
            }

            return Env::get($key, $default);
        }
    ],
    'pageMethods' => [
        'meta' => fn () => new PageMeta($this)
    ]
]);
