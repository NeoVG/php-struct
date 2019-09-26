<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

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
