<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class UlTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new UlBlock([
            'type'    => 'ul',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("- Test\n\n", $block->markdown());
    }
}
