<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

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
        $this->assertInstanceOf(ChildStruct::class, $struct->child);
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
        $this->assertEquals('{"child":{"value1":"foo"}}', json_encode($struct2));
    }
}
