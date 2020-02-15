<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\Test\Struct\RedefinePropertyStruct;
use PHPUnit\Framework\TestCase;

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
            'NeoVg\Struct\Test\Struct\RedefinePropertyStruct',
            $instance->getProperty('foo')->getClass()
        );
    }
}
