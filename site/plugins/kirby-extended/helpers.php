<?php

use Kirby\Cms\Page;
use KirbyExtended\Env;
use KirbyExtended\HigherOrderTapProxy;
use KirbyExtended\MetaTagsAdapter;
use Spatie\SchemaOrg\Schema;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value
     *
     * @param mixed $value
     * @param callable|null $callback
     * @return mixed
     */
    function tap($value, ?callable $callback = null)
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
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('schema')) {
    /**
     * Fluent builder for Schema.org types and ld+json generator
     *
     * @param string $type
     * @return mixed
     */
    function schema(string $type)
    {
        return Schema::{$type}();
    }
}

if (!function_exists('metaTags')) {
    /**
     * Generate meta tags for a given Kirby page
     *
     * @param \Kirby\Cms\Page $page
     * @return \KirbyExtended\MetaTagsAdapter
     */
    function metaTags(Page $page): MetaTagsAdapter
    {
        return MetaTagsAdapter::instance($page);
    }
}
