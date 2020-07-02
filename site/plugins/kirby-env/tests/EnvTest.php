<?php

namespace Beebmx\Tests;

use PHPUnit\Framework\TestCase;
use Beebmx\KirbyEnv;

class EnvTest extends TestCase
{
    protected $resources;

    public function setUp()
    {
        $this->resources = __DIR__ . '/resources';
    }

    /**
    *
    * @test
    */
    public function an_env_object_is_set_with_correct_path_file()
    {
        KirbyEnv::load($this->resources);

        $this->assertArrayHasKey('FOO', $_ENV);
        $this->assertArrayHasKey('BAR', $_ENV);
        $this->assertArrayHasKey('DUPLICATE_FOO', $_ENV);
    }

    /**
    *
    * @test
    */
    public function an_env_object_trow_exception_error_on_invalid_filePath()
    {
        $this->expectException(\Dotenv\Exception\InvalidPathException::class);

        KirbyEnv::load(dirname(__DIR__));
    }

    /**
    *
    * @test
    */
    public function an_env_object_is_set_with_correct_path_file_with_overload()
    {
        KirbyEnv::overload($this->resources);

        $this->assertArrayHasKey('FOO', $_ENV);
        $this->assertArrayHasKey('BAR', $_ENV);
        $this->assertArrayHasKey('DUPLICATE_FOO', $_ENV);
    }

    /**
    *
    * @test
    */
    public function an_env_object_trow_exception_error_on_invalid_filePath_with_overload()
    {
        $this->expectException(\Dotenv\Exception\InvalidPathException::class);

        KirbyEnv::overload(dirname(__DIR__));
    }
}
