<?php

namespace Kirby\Editor;

use Kirby\Cms\App;
use Kirby\Toolkit\F;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParser()
    {
        $examples = glob(__DIR__ . '/fixtures/ParserTest/*.html');

        foreach ($examples as $example) {
            $input    = F::read($example);
            $expected = require_once dirname($example) . '/' . F::name($example) . '.php';
            $output   = Parser::parse($input, true);

            $this->assertEquals($expected, $output, basename($example));
        }
    }

    public function testParentModel()
    {
        $app = new App([
            'site' => [
                'children' => [
                    [
                        'slug' => 'test',
                        'content' => [
                            'text' => '(image: test.jpg)'
                        ],
                        'files' => [
                            ['filename' => 'test.jpg']
                        ]
                    ]
                ]
            ]
        ]);

        $page  = $app->page('test');
        $block = $page->text()->blocks()->first();

        $this->assertEquals('/pages/test/files/test.jpg', $block->attrs()->guid());
    }
}
