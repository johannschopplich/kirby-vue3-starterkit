<?php

error_reporting(E_ALL);

ini_set('memory_limit', '512M');
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

$bootstrapper = dirname(__DIR__, 4) . '/kirby/bootstrap.php';

if (is_file($bootstrapper)) {
    require_once $bootstrapper;
}

kirby();
