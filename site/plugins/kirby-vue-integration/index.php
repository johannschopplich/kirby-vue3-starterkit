<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('johannschopplich/kirby-vue-integration', [
    'hooks' => require 'hooks.php',
    'routes' => require 'routes.php'
]);
