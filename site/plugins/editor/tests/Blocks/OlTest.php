<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class OlTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new OlBlock([
            'type'    => 'ol',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("1. Test\n\n", $block->markdown());
    }
}
