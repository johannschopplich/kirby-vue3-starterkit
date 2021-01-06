<?php

use Kirby\Cms\App as Kirby;

load([
    'KirbyExtended\\SpaAdapter' => 'classes/KirbyExtended/SpaAdapter.php'
], __DIR__);

$flushCache = function () {
    kirby()->cache('kirby-extended.spa-adapter')->flush();
};

Kirby::plugin('kirby-extended/spa-adapter', [
    'options' => [
        'cache' => true
    ],
    'hooks' => [
        'page.*:after' => $flushCache,
        'site.*:after' => $flushCache,
        // Explicitly register catch-all routes for SPA only when Kirby and all
        // plugins have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend([
                'routes' => require __DIR__ . '/routes.php'
            ], kirby()->plugin('kirby-extended/spa-adapter'));
        }
    ]
]);
