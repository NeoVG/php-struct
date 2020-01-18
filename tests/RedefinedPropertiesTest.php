<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\TestCase;

/**
 * Class A
 */
class A
{
}

/**
 * Class B
 */
class B extends A
{
}

/**
 * @property \NeoVg\Struct\Test\A $foo
 */
class DefinePropertyStruct extends StructAbstract
{
}

/**
 * @property \NeoVg\Struct\Test\B $foo
 */
class RedefinePropertyStruct extends DefinePropertyStruct
{
}

/**
 * Class RedefinedPropertiesTest
 */
class RedefinedPropertiesTest extends TestCase
{
    /**
     *
     */
    public function testRedefineProperties()
    {
        $instance = new RedefinePropertyStruct();
        $this->assertEquals(
            'NeoVg\Struct\Test\RedefinePropertyStruct',
            $instance->getProperty('foo')->getClass()
        );
    }
}
