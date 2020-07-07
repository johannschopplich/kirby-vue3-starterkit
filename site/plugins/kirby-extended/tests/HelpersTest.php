<?php

use KirbyExtended\EnvAdapter;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function setUp(): void
    {
        EnvAdapter::load(__DIR__ . '/resources');
    }

    public function testEnvHelperIsAvailable()
    {
        $this->assertEquals('BAR', env('FOO'));
    }

    public function testNotDefinedVariableReturnsDefaultValue()
    {
        $this->assertEquals('default', env('VAR', 'default'));
    }

    public function testDefinedVariableIgnoresDefaultValue()
    {
        $this->assertEquals('BAR', env('FOO', 'default'));
    }

    public function testNestedVariableIsSet()
    {
        $this->assertEquals('BAR', env('DUPLICATE_FOO', 'default'));
    }
}
