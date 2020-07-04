<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testMarkdown()
    {
        $block = new ImageBlock([
            'type'  => 'image',
            'id'    => 'test',
            'attrs' => [
                'src' => '/image.jpg'
            ]
        ]);

        $this->assertEquals("(image: /image.jpg)\n\n", $block->markdown());
    }

    public function testMarkdownWithAlt()
    {
        $block = new ImageBlock([
            'type'  => 'image',
            'id'    => 'test',
            'attrs' => [
                'src' => '/image.jpg',
                'alt' => 'Test'
            ]
        ]);

        $this->assertEquals("(image: /image.jpg alt: Test)\n\n", $block->markdown());
    }

    public function testMarkdownWithCaption()
    {
        $block = new ImageBlock([
            'type'  => 'image',
            'id'    => 'test',
            'attrs' => [
                'src'     => '/image.jpg',
                'caption' => 'Test'
            ]
        ]);

        $this->assertEquals("(image: /image.jpg caption: Test)\n\n", $block->markdown());
    }
}
