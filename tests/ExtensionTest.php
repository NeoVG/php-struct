<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\TestCase;

/**
 * Class SuperStruct
 *
 * @property string $super
 */
class SuperStruct extends StructAbstract
{
}

/**
 * Class ExtendedStruct
 *
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
     *
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
