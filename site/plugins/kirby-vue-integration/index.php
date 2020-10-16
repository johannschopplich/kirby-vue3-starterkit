<?php

use Kirby\Cms\App as Kirby;

$flush = function () {
    kirby()->cache('kirby-extended.vue-integration')->flush();
};

Kirby::plugin('kirby-extended/vue-integration', [
    'options' => [
        'cache' => true
    ],
    'hooks' => [
        'page.*:after' => $flush,
        'site.*:after' => $flush,
        // (1)
        // Explicitly register catch-all routes for SPA only when Kirby and all
        // plugins have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend([
                'routes' => require __DIR__ . '/routes.php'
            ], kirby()->plugin('kirby-extended/vue-integration'));
        }
    ],
    // (2)
    // The following line will NOT work and overwrite custom routes
    // 'routes' => require __DIR__ . '/routes.php'
]);
