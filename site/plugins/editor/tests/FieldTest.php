<?php

namespace Kirby\Editor;

use Kirby\Cms\Page;
use Kirby\Data\Json;
use Kirby\Form\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function field(array $args = [])
    {
        $model = new Page([
            'slug' => 'test'
        ]);

        return new Field('editor', array_merge([
            'model' => $model
        ], $args));
    }

    public function testEmptyValue()
    {
        $field = $this->field();

        $this->assertEquals([], $field->value());
    }

    public function testStringValue()
    {
        $field = $this->field([
            'value' => 'Test'
        ]);

        $value = $field->value();

        $this->assertCount(1, $value);
        $this->assertEquals('Test', $value[0]['content']);
        $this->assertEquals('paragraph', $value[0]['type']);
    }

    public function testArrayValue()
    {
        $field = $this->field([
            'value' => [
                [
                    'type'    => 'paragraph',
                    'content' => 'Test',
                    'id'      => 'abc'
                ]
            ]
        ]);

        $value = $field->value();

        $this->assertCount(1, $value);
        $this->assertEquals('Test', $value[0]['content']);
        $this->assertEquals('abc', $value[0]['id']);
        $this->assertEquals('paragraph', $value[0]['type']);
    }

    public function testJsonValue()
    {
        $field = $this->field([
            'value' => Json::encode([
                [
                    'type'    => 'paragraph',
                    'content' => 'Test',
                    'id'      => 'abc'
                ]
            ])
        ]);

        $value = $field->value();

        $this->assertCount(1, $value);
        $this->assertEquals('Test', $value[0]['content']);
        $this->assertEquals('abc', $value[0]['id']);
        $this->assertEquals('paragraph', $value[0]['type']);
    }

    public function testEmptyDefault()
    {
        $field = $this->field();

        $this->assertEquals([], $field->default());
    }
}
