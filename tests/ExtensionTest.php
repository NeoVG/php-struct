<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\Test\Struct\ExtendedStruct;
use NeoVg\Struct\Test\Struct\SuperStruct;
use PHPUnit\Framework\TestCase;

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
