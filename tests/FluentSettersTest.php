<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

/**
 * Class FluentSettersTest
 */
class FluentSettersTest extends TestCase
{
    /**
     *
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
