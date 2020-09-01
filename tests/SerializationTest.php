<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\Test\Enum\NotNullableEnum;
use NeoVg\Struct\Test\Struct\ParentStruct;
use PHPUnit\Framework\TestCase;

class SerializationTest extends TestCase
{
    public function testSerializeStruct(): void
    {
        $struct1 = ParentStruct::createFromArray([
            'child' => [
                'value1' => 'foobar',
            ],
        ]);

        $serialized = serialize($struct1);
        $this->assertEquals(93, strlen($serialized));

        $struct2 = unserialize($serialized);
        $this->assertEquals('foobar', $struct2->child->value1);
    }

    public function testSerializeEnum(): void
    {
        $enum1 = new NotNullableEnum('foo');

        $serialized = serialize($enum1);

        $enum2 = unserialize($serialized);
        $this->assertEquals('foo', $enum2->getValue());
    }
}
