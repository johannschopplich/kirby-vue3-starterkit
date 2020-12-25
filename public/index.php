<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Kirby\Cms\App as Kirby;

$kirby = new Kirby([
    'roots' => [
        'index'    => __DIR__,
        'base'     => $base = dirname(__DIR__),
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
