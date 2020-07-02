<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class BlockquoteTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new BlockquoteBlock([
            'type'    => 'blockquote',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("> Test\n\n", $block->markdown());
    }
}
