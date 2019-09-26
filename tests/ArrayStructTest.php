<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

/**
 * Class ArrayStructTest
 */
class ArrayStructTest extends TestCase
{
    /**
     *
     */
    public function testArrayCreateFromArray()
    {
        $struct = ParentStruct::createFromArray([
            'strings' => ['foo', 'bar'],
        ]);
        $this->assertIsArray($struct->strings);
        $this->assertEquals('foo', $struct->strings[0]);
        $this->assertEquals('bar', $struct->strings[1]);
    }

    /**
     *
     */
    public function testWrongType()
    {
        $this->expectException(\TypeError::class);
        $struct = ParentStruct::createFromArray([
            'strings' => [true],
        ]);
    }

    /**
     *
     */
    public function testChildArrayCreateFromArray()
    {
        $struct = ParentStruct::createFromArray([
            'childs' => [
                [
                    'value1' => 'foo',
                ],
                ChildStruct::createFromArray([
                    'value1' => 'bar',
                ]),
            ],
        ]);
        $this->assertEquals('foo', $struct->childs[0]->value1);
        $this->assertEquals('bar', $struct->childs[1]->value1);
    }

    /**
     *
     */
    public function testChildArrayErrors()
    {
        $this->expectException(\TypeError::class);
        $struct = ParentStruct::createFromArray([
            'childs' => [
                new ParentStruct(),
            ],
        ]);
    }

    /**
     *
     */
    public function testChildArraySuccesses()
    {
        $struct = new ParentStruct();

        $struct->childs = [];
        $this->assertEquals([], $struct->childs);

        $struct->childs = [
            ChildStruct::createFromArray(['value1' => 'foo']),
        ];
        $this->assertInstanceOf(ChildStruct::class, $struct->childs[0]);
        $this->assertEquals('foo', $struct->childs[0]->value1);

        $struct->childs = [
            (new ChildStruct())->value1('bar'),
        ];
        $this->assertInstanceOf(ChildStruct::class, $struct->childs[0]);
        $this->assertEquals('bar', $struct->childs[0]->value1);
    }
}
