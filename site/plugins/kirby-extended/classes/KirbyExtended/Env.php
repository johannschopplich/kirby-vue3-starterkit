<?php

namespace KirbyExtended;

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
     *
     * @param string|null $path
     * @param string $filename
     * @return array<string,string|null>
     */
    public static function load(?string $path = null, string $filename = '.env'): ?array
    {
        $path = $path ?? option('kirby-extended.env.path', kirby()->root('base'));
        $filename = option('kirby-extended.env.filename', $filename);
        $repository = static::getRepository();

        static::$loaded = true;
        return Dotenv::create($repository, $path, $filename)->load();
    }

    /**
     * Get the state of `Dotenv` initialization
     *
     * @return bool
     */
    public static function isLoaded(): bool
    {
        return static::$loaded;
    }

    /**
     * Get the environment repository instance
     *
     * @return \Dotenv\Repository\RepositoryInterface
     */
    public static function getRepository()
    {
        if (!isset(static::$repository)) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();
            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Get the value of an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
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
