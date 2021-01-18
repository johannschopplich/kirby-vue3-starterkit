<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Template;
use KirbyExtended\Env;
use KirbyExtended\HtmlMinTemplate;
use KirbyExtended\PageMeta;
use KirbyExtended\Redirects;
use KirbyExtended\SiteMeta;

Kirby::plugin('johannschopplich/kirby-extended', [
    'hooks' => [
        'route:after' => function ($route, $path, $method, $result, $final) {
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
    'pageMethods' => [
        'env' => function ($key, $default = null) {
            if (!Env::isLoaded()) {
                Env::load();
            }

            return Env::get($key, $default);
        },
        'meta' => function () {
            return new PageMeta($this);
        }
    ],
    'components' => [
        'template' => function (Kirby $kirby, string $name, string $type = 'html', string $defaultType = 'html') {
            if ($type === 'html') {
                return new HtmlMinTemplate($name, $type, $defaultType);
            }

            return new Template($name, $type, $defaultType);
        }
    ]
]);
