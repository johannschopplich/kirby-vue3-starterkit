<?php

$base = dirname(__DIR__);

require $base . '/vendor/autoload.php';
\KirbyExtended\Env::load($base);

$kirby = new \Kirby\Cms\App([
    'roots' => [
        'index'    => __DIR__,
        'base'     => $base,
        'site'     => $base . '/site',
        'storage'  => $storage = $base . '/storage',
        'content'  => $storage . '/content',
        'accounts' => $storage . '/accounts',
        'cache'    => $storage . '/cache',
        'logs'     => $storage . '/logs',
        'sessions' => $storage . '/sessions',
    ]
]);

echo $kirby->render();
