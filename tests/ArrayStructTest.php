<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayStructTest
 */
class ArrayStructTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
        $this->expectException(Error::class);
        ParentStruct::createFromArray([
            'strings' => [true],
        ]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testChildArrayCreateFromArray()
    {
        $struct = ParentStruct::createFromArray([
            'childs' => [
                [
                    'value1' => 'foo',
                ],
                Child\ChildStruct::createFromArray([
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
        $this->expectException(Error::class);
        ParentStruct::createFromArray([
            'childs' => [
                new ParentStruct(),
            ],
        ]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testChildArraySuccesses()
    {
        $struct = new ParentStruct();

        $struct->childs = [];
        $this->assertEquals([], $struct->childs);

        $struct->childs = [
            Child\ChildStruct::createFromArray(['value1' => 'foo']),
        ];
        $this->assertInstanceOf(Child\ChildStruct::class, $struct->childs[0]);
        $this->assertEquals('foo', $struct->childs[0]->value1);

        $struct->childs = [
            (new Child\ChildStruct())->value1('bar'),
        ];
        $this->assertInstanceOf(Child\ChildStruct::class, $struct->childs[0]);
        $this->assertEquals('bar', $struct->childs[0]->value1);
    }
}
