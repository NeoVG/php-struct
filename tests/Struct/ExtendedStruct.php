<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;

/**
 * @property string $super
 */
class SuperStruct extends StructAbstract
{
}

/**
 * @property string $extended
 *
 * @method $this extended(string $value)
 */
class ExtendedStruct extends SuperStruct
{
}
