<?php

load([
    'KirbyExtended\\Page' => 'classes/KirbyExtended/Page.php',
    'KirbyExtended\\Vite' => 'classes/KirbyExtended/Vite.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/kirby-vite', [
    'options' => [
        'entry' => 'index.js',
        'outDir' => 'dist',
        'devServer' => env('KIRBY_DEV_PROTOCOL', 'http') . '://' . env('KIRBY_DEV_HOSTNAME', 'localhost') . ':' . env('VITE_DEV_PORT', '3000')
    ],
    'hooks' => [
        // Explicitly register catch-all routes only when Kirby and all plugins
        // have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend([
                'routes' => require __DIR__ . '/routes.php'
            ], kirby()->plugin('johannschopplich/kirby-vite'));
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
