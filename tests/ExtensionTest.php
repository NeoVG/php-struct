<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\TestCase;

/**
 * @property string $super
 */
class SuperStruct extends StructAbstract
{
}

/**
 * @property string $extended
 *
 * @method $this extended(string $value)
 */
class ExtendedStruct extends SuperStruct
{
}

/**
 * Class ExtensionTest
 */
class ExtensionTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testExendedStruct()
    {
        $instance = ExtendedStruct::createFromArray([
            'extended' => 'yyy',
            'super'    => 'xxx',
        ]);
        $this->assertInstanceOf(ExtendedStruct::class, $instance);
        $this->assertInstanceOf(SuperStruct::class, $instance);
        $this->assertEquals('yyy', $instance->extended);
        $this->assertEquals('xxx', $instance->super);
    }
}
