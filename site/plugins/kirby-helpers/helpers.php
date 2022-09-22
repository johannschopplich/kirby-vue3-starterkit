<?php

use KirbyHelpers\Env;
use KirbyHelpers\HigherOrderTapProxy;
use KirbyHelpers\Vite;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value
     */
    function tap($value, callable|null $callback = null)
    {
        if ($callback === null) {
            return new HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
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
