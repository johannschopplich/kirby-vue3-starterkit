<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new VideoBlock([
            'type'  => 'video',
            'id'    => 'test',
            'attrs' => [
                'src' => 'https://youtube.com/watch?v=1234'
            ]
        ]);

        $this->assertEquals("(video: https://youtube.com/watch?v=1234)\n\n", $block->markdown());
    }

    public function testMarkdownWithCaption()
    {
        $block = new VideoBlock([
            'type'  => 'video',
            'id'    => 'test',
            'attrs' => [
                'src'     => 'https://youtube.com/watch?v=1234',
                'caption' => 'Test'
            ]
        ]);

        $this->assertEquals("(video: https://youtube.com/watch?v=1234 caption: Test)\n\n", $block->markdown());
    }
}
