<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\TestCase;

/**
 * @property string $property1
 * @property string $property2
 *
 * @method $this property1(string $value)
 * @method $this withProperty2(string $value)
 */
class FluentSettersStruct extends StructAbstract
{
}

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
