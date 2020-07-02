<?php

namespace Beebmx\Tests;

use PHPUnit\Framework\TestCase;
use Beebmx\KirbyEnv;

class HelperOverloadTest extends TestCase
{
    private $resources;

    public function setUp()
    {
        KirbyEnv::overload(__DIR__ . '/resources');
    }

    /**
    *
    * @test
    */
    public function a_helper_env_is_available()
    {
        $this->assertEquals('BAR', env('FOO'));
    }

    /**
    *
    * @test
    */
    public function an_not_defined_variable_returns_the_default_value()
    {
        $this->assertEquals('default', env('VAR', 'default'));
    }

    /**
    *
    * @test
    */
    public function a_defined_variable_ignore_the_default_value()
    {
        $this->assertEquals('BAR', env('FOO', 'default'));
    }

    /**
    *
    * @test
    */
    public function a_nested_variable_is_set()
    {
        $this->assertEquals('BAR', env('DUPLICATE_FOO', 'default'));
    }
}
