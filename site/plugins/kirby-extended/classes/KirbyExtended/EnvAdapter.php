<?php

namespace KirbyExtended;

use Dotenv\Dotenv;

class EnvAdapter
{
    /**
     * Indicates if `dotenv` has been loaded
     *
     * @var bool
     */
    protected static bool $loaded = false;

    /**
     * Load the environment file from a given directory
     *
     * @param string|null $path
     * @param string|null $filename
     * @return array|null
     */
    public static function load(?string $path = null, ?string $filename = null): ?array
    {
        if ($path === null) {
            $path = option('kirby-extended.env.path', kirby()->root('base'));
        }

        if ($filename === null) {
            $filename = option('kirby-extended.env.filename', '.env');
        }

        static::$loaded = true;

        $repository = Env::getRepository();
        return Dotenv::create($repository, $path, $filename)->load();
    }

    /**
     * Indicates if `dotenv` has been loaded
     *
     * @return bool
     */
    public static function isLoaded(): bool
    {
        return !!static::$loaded;
    }
}
