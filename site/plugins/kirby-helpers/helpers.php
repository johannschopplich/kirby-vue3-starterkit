<?php

use JohannSchopplich\Helpers\Env;
use JohannSchopplich\Helpers\Vite;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('vite')) {
    /**
     * Returns the Vite singleton class instance
     */
    function vite(): Vite
    {
        return Vite::instance();
    }
}
