<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\Test\Struct\FluentSettersStruct;
use PHPUnit\Framework\TestCase;

/**
 * Class FluentSettersTest
 */
class FluentSettersTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFluentSetters()
    {
        $instance = (new FluentSettersStruct())
            ->property1('foo')
            ->withProperty2('bar');

        $this->assertEquals('foo', $instance->property1);
        $this->assertEquals('bar', $instance->property2);
    }
}
