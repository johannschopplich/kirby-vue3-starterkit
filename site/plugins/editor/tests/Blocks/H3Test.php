<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class H3Test extends TestCase
{
    public function testMarkdown()
    {
        $block = new H3Block([
            'type'    => 'h3',
            'id'      => 'test',
            'content' => 'Test'
        ]);

        $this->assertEquals("### Test\n\n", $block->markdown());
    }
}
