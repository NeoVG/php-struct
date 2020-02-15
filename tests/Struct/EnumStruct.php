<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\Test\Enum\NotNullableEnum;

/**
 * @property \NeoVg\Struct\Test\Enum\NullableEnum    $nullable
 * @property \NeoVg\Struct\Test\Enum\NotNullableEnum $notNullable
 *
 * @method $this withNullable(?string $value)
 * @method $this withNotNullable(string $value)
 */
class EnumStruct extends StructAbstract
{
}

/**
 * @property \NeoVg\Struct\Test\Enum\NotNullableEnum $default
 */
class DefaultEnumStruct extends StructAbstract
{
    protected $default = NotNullableEnum::FOO;
}
