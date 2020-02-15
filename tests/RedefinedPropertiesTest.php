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
 * @property A $foo
 */
class DefinePropertyStruct extends StructAbstract
{
}

/**
 * @property B $foo
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
