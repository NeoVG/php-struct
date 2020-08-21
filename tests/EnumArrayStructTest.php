<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructProperty\EnumArrayProperty;
use NeoVg\Struct\Test\Enum\NullableEnum;
use NeoVg\Struct\Test\Struct\EnumArrayStruct;
use PHPUnit\Framework\TestCase;

class EnumArrayStructTest extends TestCase
{
    ####################################################################################################################
    # __construct()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructEnumArrayProperty()
    {
        $struct = new EnumArrayStruct();
        $this->assertInstanceOf(EnumArrayProperty::class, $struct->getProperty('enums'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testArrayCreateFromArray()
    {
        $struct = EnumArrayStruct::createFromArray([
            'enums' => [
                'foo',
                new Enum\NullableEnum(1)
            ]
        ]);
        $this->assertIsArray($struct->enums);
        $this->assertIsObject($struct->enums[0]);
        $this->assertInstanceOf(NullableEnum::class, $struct->enums[0]);
        $this->assertEquals('foo', $struct->enums[0]->getValue());
        $this->assertIsObject($struct->enums[1]);
        $this->assertInstanceOf(NullableEnum::class, $struct->enums[1]);
        $this->assertEquals(1, $struct->enums[1]->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testToArray()
    {
        $array = EnumArrayStruct::createFromArray([
            'enums' => ['foo', 1]
        ])->toArray();
        $this->assertIsArray($array['enums']);
        $this->assertCount(2, $array['enums']);
        $this->assertEquals('foo', $array['enums'][0]);
        $this->assertEquals(1, $array['enums'][1]);
    }
}
