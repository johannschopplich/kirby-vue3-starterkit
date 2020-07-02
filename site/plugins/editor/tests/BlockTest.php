<?php

namespace Kirby\Editor;

use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function test__construct()
    {
        $block = new Block([
            'type' => 'paragraph',
            'id'   => 'test',
        ]);

        $this->assertEquals('paragraph', $block->type());
        $this->assertEquals('test', $block->id());
        $this->assertEquals(null, $block->parent());
        $this->assertInstanceOf('Kirby\Cms\Content', $block->attrs());
        $this->assertInstanceOf('Kirby\Cms\Field', $block->content());
        $this->assertInstanceOf('Kirby\Editor\Blocks', $block->siblings());
    }

    public function test__toString()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("<p>Test</p>\n", $block->__toString());
    }

    public function testAttrs()
    {
        $block = new Block([
            'type'  => 'paragraph',
            'id'    => 'test',
            'attrs' => [
                'a' => 'A',
                'b' => 'B'
            ]
        ]);

        $this->assertEquals('A', $block->attrs()->a());
        $this->assertEquals('B', $block->attrs()->b());
        $this->assertEquals('', $block->attrs()->c());
    }

    public function testContent()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals('Test', $block->content());
    }

    public function testIs()
    {
        $a = new Block(['type' => 'paragraph', 'id' => 'a']);
        $b = new Block(['type' => 'paragraph', 'id' => 'b']);

        $this->assertFalse($a->is($b));
        $this->assertTrue($a->is($a));
    }

    public function testIsEmpty()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => ''
        ]);

        $this->assertTrue($block->isEmpty());

        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertFalse($block->isEmpty());
    }

    public function testIsNotEmpty()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => ''
        ]);

        $this->assertFalse($block->isNotEmpty());

        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertTrue($block->isNotEmpty());
    }

    public function testParent()
    {
        $block = new Block([
            'type'   => 'paragraph',
            'id'     => 'test',
            'parent' => $page = new Page(['slug' => 'test'])
        ]);

        $this->assertEquals($page, $block->parent());
    }

    public function testToArray()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test',
            'attrs'   => $attrs = [
                'a' => 'A',
                'b' => 'B'
            ]
        ]);

        $expected = [
            'attrs'   => $attrs,
            'content' => 'Test',
            'id'      => 'test',
            'type'    => 'paragraph'
        ];

        $this->assertEquals($expected, $block->toArray());
    }

    public function testToField()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertInstanceOf('Kirby\Cms\Field', $block->toField());
        $this->assertEquals("<p>Test</p>\n", $block->toField());
    }

    public function testToHtml()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("<p>Test</p>\n", $block->toHtml());
    }

    public function testToMarkdown()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("Test\n\n", $block->toMarkdown());
    }
}
