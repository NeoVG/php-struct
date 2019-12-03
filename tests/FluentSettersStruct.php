<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property string $property1
 * @property string $property2
 *
 * @method self property1(string $value)
 * @method self withProperty2(string $value)
 */
class FluentSettersStruct extends StructAbstract
{
}
