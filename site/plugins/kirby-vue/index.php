<?php

load([
    'KirbyVue\\Page' => 'classes/KirbyVue/Page.php',
    'KirbyVue\\Vite' => 'classes/KirbyVue/Vite.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/kirby-vue', [
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
            ], kirby()->plugin('johannschopplich/kirby-vue'));
        }
    ]
]);

/**
 * Returns the Vite instance
 *
 * @return \KirbyVue\Vite
 */
function vite()
{
    return \KirbyVue\Vite::getInstance();
}
