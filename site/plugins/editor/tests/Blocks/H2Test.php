<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class H2Test extends TestCase
{
    public function testMarkdown()
    {
        $block = new H2Block([
            'type'    => 'h2',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("## Test\n\n", $block->markdown());
    }
}
