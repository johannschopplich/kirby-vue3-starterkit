<?php

load([
    'KirbyExtended\\Vite' => 'classes/KirbyExtended/Vite.php'
], __DIR__);

\Kirby\Cms\App::plugin('kirby-extended/vite', [
    'options' => [
        'entry' => 'index.js',
        'outDir' => 'dist',
        'devServer' => 'http://localhost:3000'
    ],
    'hooks' => [
        // Explicitly register catch-all routes only when Kirby and all plugins
        // have been loaded to ensure no other routes are overwritten
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
