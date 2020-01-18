<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Child;

use NeoVg\Struct\StructAbstract;

/**
 * @property string $value1
 * @property string $value2
 *
 * @method $this value1(string $value)
 * @method $this value2(string $value)
 */
class ChildStruct extends StructAbstract
{
}

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\TestCase;

/**
 * @property string[]                               $strings
 * @property \NeoVg\Struct\Test\Child\ChildStruct   $child
 * @property \NeoVg\Struct\Test\Child\ChildStruct[] $childs
 * @property Child\ChildStruct                      $relativeChild
 *
 * @method $this strings(array $values)
 * @method $this child(\NeoVg\Struct\Test\Child\ChildStruct $value)
 * @method $this childs(\NeoVg\Struct\Test\Child\ChildStruct[] $values);
 * @method $this relativeChild(Child\ChildStruct $value)
 */
class ParentStruct extends StructAbstract
{
}

/**
 * Class ParentStructTest
 */
class ParentStructTest extends TestCase
{
    /**
     *
     */
    public function testInstanciation()
    {
        $struct = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar',
            ],
        ]);
        $this->assertIsObject($struct->child);
        $this->assertInstanceOf(Child\ChildStruct::class, $struct->child);
        $this->assertEquals('foobar', $struct->child->value1);
    }

    /**
     *
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
     *
     */
    public function testDirtyOnly()
    {
        $struct1 = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foo',
                'value2' => 'bar',
            ],
        ])->clean();
        $struct1->child->setDirty('value1', true);
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
     *
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
