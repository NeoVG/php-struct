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
                'value' => 'foobar'
            ]
        ]);
        $this->assertIsObject($instance->child);
        $this->assertInstanceOf(ChildStruct::class, $instance->child);
        $this->assertEquals('foobar', $instance->child->value);
    }

    public function testDirtyFlag()
    {
        $instance = ParentStruct::createFromArray([
            'child' => [
                'value' => 'foobar'
            ]
        ])->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());

        $instance->child->value = 'blafasel';
        $this->assertTrue($instance->isDirty());
        $this->assertTrue($instance->child->isDirty());

        $instance->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());

        $instance->child->value = 'foobar';
        $this->assertTrue($instance->isDirty());
        $this->assertTrue($instance->child->isDirty());

        $instance->child->clean();
        $this->assertFalse($instance->isDirty());
        $this->assertFalse($instance->child->isDirty());
    }
}
