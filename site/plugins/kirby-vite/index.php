<?php

load([
    'KirbyExtended\\Vite' => 'classes/KirbyExtended/Vite.php'
], __DIR__);

$flushCache = function () {
    kirby()->cache('kirby-extended.vite')->flush();
};

\Kirby\Cms\App::plugin('kirby-extended/vite', [
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
            ], kirby()->plugin('kirby-extended/vite'));
        }
    ]
]);

/**
 * Returns the Vite instance
 *
 * @return \KirbyExtended\Vite
 */
function vite()
{
    return \KirbyExtended\Vite::getInstance();
}
