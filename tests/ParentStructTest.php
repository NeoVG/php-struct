<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\Test\Struct\ParentStruct;
use PHPUnit\Framework\TestCase;

/**
 * Class ParentStructTest
 */
class ParentStructTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInstanciation()
    {
        $struct = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar',
            ],
        ]);
        $this->assertIsObject($struct->child);
        $this->assertInstanceOf(Struct\Child\ChildStruct::class, $struct->child);
        $this->assertEquals('foobar', $struct->child->value1);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDirtyFlag()
    {
        $struct = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar',
            ],
        ])->clean();
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->child->isDirty());

        $struct->child->value1 = 'blafasel';
        $this->assertTrue($struct->isDirty());
        $this->assertTrue($struct->child->isDirty());

        $struct->clean();
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->child->isDirty());

        $struct->child->value1 = 'foobar';
        $this->assertTrue($struct->isDirty());
        $this->assertTrue($struct->child->isDirty());

        $struct->child->clean();
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->child->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDirtyOnly()
    {
        $struct1 = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foo',
                'value2' => 'bar',
            ],
        ])->clean();
        $struct1->child->setDirty('value1');
        $struct2 = $struct1->withDirtyPropertiesOnly();
        $this->assertEquals(<<<'EOD'
{
    "child": {
        "value1": "foo"
    }
}
EOD
            , (string)$struct2);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonSerialize()
    {
        $struct = ParentStruct::createFromArray([
            'childs' => [
                [
                    'value1' => 'foo',
                ],
                [
                    'value1' => 'bar',
                ],
            ],
        ])->clean();

        $array = $struct->toArray();
        $this->assertIsArray($array['childs']);
        $this->assertIsArray($array['childs'][0]);
        $this->assertEquals('foo', $array['childs'][0]['value1']);

        $json = (string)$struct;
        $this->assertEquals(<<<'EOD'
{
    "childs": [
        {
            "value1": "foo"
        },
        {
            "value1": "bar"
        }
    ]
}
EOD
            , $json);
    }
}
