<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

class ParentStructTest extends TestCase
{
    public function testInstanciation()
    {
        $instance = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar'
            ]
        ]);
        $this->assertIsObject($instance->child);
        $this->assertInstanceOf(ChildStruct::class, $instance->child);
        $this->assertEquals('foobar', $instance->child->value1);
    }

    public function testDirtyFlag()
    {
        $instance = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar'
            ]
        ])->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());

        $instance->child->value1 = 'blafasel';
        $this->assertTrue($instance->isDirty());
        $this->assertTrue($instance->child->isDirty());

        $instance->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());

        $instance->child->value1 = 'foobar';
        $this->assertTrue($instance->isDirty());
        $this->assertTrue($instance->child->isDirty());

        $instance->child->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());
    }

    public function testDirtyOnly()
    {
        $instance1 = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foo',
                'value2' => 'bar',
            ]
        ])->clean();
        $instance1->child->setDirty('value1', true);
        $instance2 = $instance1->withDirtyPropertiesOnly();
        $this->assertEquals('{"child":{"value1":"foo"}}', json_encode($instance2));
    }
}
