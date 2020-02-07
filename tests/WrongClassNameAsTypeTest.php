<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class WrongClassNameStruct
 *
 * @property \Foo\Bar $property
 */
class WrongClassNameStruct extends StructAbstract
{
}

class WrongClassNameAsTypeTest extends TestCase
{
    public function testErrorHandling()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot parse data definition for NeoVg\Struct\Test\WrongClassNameStruct::property, \Foo\Bar is no valid internal datatype and no known class name.');
        $instance = new WrongClassNameStruct();
    }
}
