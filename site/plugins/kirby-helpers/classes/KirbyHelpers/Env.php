<?php

namespace KirbyHelpers;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;

class Env
{
    protected static RepositoryInterface $repository;
    protected static bool $loaded = false;

    /**
     * Load the environment file from a given directory
     */
    public static function load(string|null $path = null, string $filename = '.env'): array|null
    {
        $path ??= option('kirby-helpers.env.path', kirby()->root('base'));
        $filename = option('kirby-helpers.env.filename', $filename);
        $repository = static::getRepository();

        static::$loaded = true;
        return Dotenv::create($repository, $path, $filename)->load();
    }

    /**
     * Get the state of `Dotenv` initialization
     */
    public static function isLoaded(): bool
    {
        return static::$loaded;
    }

    /**
     * Get the environment repository instance
     */
    public static function getRepository(): \Dotenv\Repository\RepositoryInterface
    {
        if (!isset(static::$repository)) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();
            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Get the value of an environment variable
     */
    public static function get(string $key, $default = null)
    {
        return Option::fromValue(static::getRepository()->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return;
                }

                if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                    return $matches[2];
                }

                return $value;
            })
            ->getOrCall(fn () => value($default));
    }
}
