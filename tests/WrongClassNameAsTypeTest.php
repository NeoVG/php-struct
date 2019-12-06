<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\UnknownTypeError;
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
        $this->expectException(UnknownTypeError::class);
        $instance = new WrongClassNameStruct();
    }
}
