<?php

namespace Kirby\Editor;

use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    public function provider()
    {
        return [
            [
                'Hello <div>world</div>',
                'Hello world'
            ],
            [
                'Hello <strong>world</strong>',
                'Hello <strong>world</strong>'
            ],
            [
                'Hello <strong style="color: red">world</strong>',
                'Hello <strong>world</strong>'
            ],
            [
                'Hello <strong style="color: red"><i class="something">world</i></strong>',
                'Hello <strong><em>world</em></strong>'
            ],
            [
                '<a href="https://example.com" style="color: red" onclick="alert(\'yay\')">Link</a>',
                '<a href="https://example.com">Link</a>'
            ],
            [
                '<b>Something bold</b>',
                '<strong>Something bold</strong>'
            ],
            [
                '<i>Something italic</i>',
                '<em>Something italic</em>'
            ]
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testSanitizer($input, $expected)
    {
        $html = Parser::sanitize($input);
        $this->assertEquals($expected, $html);
    }
}
