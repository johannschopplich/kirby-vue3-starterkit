<?php

namespace Beebmx;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\RepositoryBuilder;

class KirbyEnv
{
    protected static $loaded = false;

    public static function load(string $path = __DIR__, string $file = '.env'): array
    {
        $adapters = [
            new EnvConstAdapter(),
            new ServerConstAdapter(),
        ];

        $repository = RepositoryBuilder::create()
            ->withReaders($adapters)
            ->withWriters($adapters)
            ->immutable()
            ->make();

        static::$loaded = true;

        return Dotenv::create($repository, $path, null)->load();
    }

    /**
     * Load environment file in given directory.
     *
     * @param string $path
     * @param string $file
     * @return array
     */
    public static function overload(string $path = __DIR__, string $file = '.env'): array
    {
        static::$loaded = true;

        return Dotenv::createImmutable($path, $file)->load();
    }

    public static function isLoaded()
    {
        return !!static::$loaded;
    }
}
