<?php

namespace KirbyExtended;

use Kirby\Http\Router;
use Throwable;

class Redirects
{
    public static function go($path, $method)
    {
        // Load redirects definitions
        $redirects = option('kirby-extended.redirects', []);

        if (empty($redirects)) {
            return;
        }

        // Turn into routes array
        $routes = array_map(
            fn ($from, $to) => [
                'pattern' => $from,
                'action'  => function (...$parameters) use ($to) {
                    // Resolve callback
                    if (is_callable($to)) {
                        $to = $to(...$parameters);
                    }

                    // Fill placeholders
                    foreach ($parameters as $i => $parameter) {
                        $to = str_replace('$' . ($i + 1), $parameter, $to);
                    }

                    return go($to);
                }
            ],
            array_keys($redirects),
            $redirects
        );

        // Run router on redirects routes
        try {
            $router = new Router($routes);
            return $router->call($path, $method);
        } catch (Throwable $e) {
            return site()->errorPage();
        }
    }
}
