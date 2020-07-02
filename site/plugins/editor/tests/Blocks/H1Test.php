<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class H1Test extends TestCase
{
    public function testMarkdown()
    {
        $block = new H1Block([
            'type'    => 'h1',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("# Test\n\n", $block->markdown());
    }
}
