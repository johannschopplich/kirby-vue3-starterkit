<?php

load([
    'JohannSchopplich\\VueKit\\Page' => 'classes/Page.php',
    'JohannSchopplich\\VueKit\\VueKit' => 'classes/VueKit.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/kirby-vue-kit', [
    'options' => [
        'outDir' => 'dist',
        'devServer' => env('KIRBY_DEV_PROTOCOL', 'http') . '://' . env('KIRBY_DEV_HOSTNAME', 'localhost') . ':' . env('VITE_DEV_PORT', '3000')
    ],
    'hooks' => [
        // Explicitly register catch-all routes only when Kirby and all plugins
        // have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend([
                'routes' => require __DIR__ . '/routes.php'
            ], kirby()->plugin('johannschopplich/kirby-vue-kit'));
        }
    ]
]);

if (!function_exists('vueKit')) {
    /**
     * Returns the Vue instance
     */
    function vueKit(): \JohannSchopplich\VueKit\VueKit
    {
        return \JohannSchopplich\VueKit\VueKit::instance();
    }
}
