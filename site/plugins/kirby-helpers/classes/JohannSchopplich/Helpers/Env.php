<?php

namespace JohannSchopplich\Helpers;

use Closure;
use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;

class Env
{
    protected static bool $loaded = false;
    protected static RepositoryInterface|null $repository = null;

    public static function getRepository(): RepositoryInterface
    {
        return static::$repository ??= RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();
    }

    public static function isLoaded(): bool
    {
        return static::$loaded;
    }

    public static function load(string $path, string $filename = '.env'): array
    {
        static::$loaded = true;

        return Dotenv::create(
            static::getRepository(),
            $path,
            $filename
        )->load();
    }

    public static function get(string $key, $default = null): mixed
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
            ->getOrCall(fn () => $default instanceof Closure ? $default() : $default);
    }
}
