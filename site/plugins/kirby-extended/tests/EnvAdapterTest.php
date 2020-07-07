<?php

use KirbyExtended\EnvAdapter;
use PHPUnit\Framework\TestCase;

class EnvAdapterTest extends TestCase
{
    /**
     * Path to `.env` file
     *
     * @var string
     */
    protected string $resources;

    public function setUp(): void
    {
        $this->resources = __DIR__ . '/resources';
    }

    public function testEnvObjectIsSetWithCorrectPath()
    {
        EnvAdapter::load($this->resources);

        $this->assertArrayHasKey('FOO', $_ENV);
        $this->assertArrayHasKey('BAR', $_ENV);
        $this->assertArrayHasKey('DUPLICATE_FOO', $_ENV);
    }

    public function testEnvObjectThrowsExceptionOnInvalidPath()
    {
        $this->expectException(\Dotenv\Exception\InvalidPathException::class);

        EnvAdapter::load(dirname(__DIR__));
    }
}
