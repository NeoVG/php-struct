<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\Test\Struct\WrongClassNameStruct;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class WrongClassNameAsTypeTest
 */
class WrongClassNameAsTypeTest extends TestCase
{
    /**
     *
     */
    public function testErrorHandling()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot parse definition for NeoVg\Struct\Test\Struct\WrongClassNameStruct::property, \Foo\Bar is no valid internal datatype and no known class name.');
        new WrongClassNameStruct();
    }
}
