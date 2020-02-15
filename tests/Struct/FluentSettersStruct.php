<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;

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
