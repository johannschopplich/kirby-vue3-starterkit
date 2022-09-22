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

    public static function load(string $path, string $filename = '.env'): array|null
    {
        $repository = static::getRepository();

        static::$loaded = true;
        return Dotenv::create($repository, $path, $filename)->load();
    }

    public static function isLoaded(): bool
    {
        return static::$loaded;
    }

    public static function getRepository(): \Dotenv\Repository\RepositoryInterface
    {
        if (!isset(static::$repository)) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();
            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

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
