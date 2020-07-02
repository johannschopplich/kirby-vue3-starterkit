<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class ParagraphTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("Test\n\n", $block->markdown());
    }

    public function testMarkdownWithFormats()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => 'This is <strong>bold<strong> <em>italic</em> and regular text'
        ]);

        $this->assertEquals("This is **bold** *italic* and regular text\n\n", $block->markdown());
    }

    public function testMarkdownWithLink()
    {
        $block = new Block([
            'type'    => 'paragraph',
            'id'      => 'test',
            'content' => '<a href="https://getkirby.com">Test</a>'
        ]);

        $this->assertEquals("[Test](https://getkirby.com)\n\n", $block->markdown());
    }
}
