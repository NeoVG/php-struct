<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @method static value1(string $value)
 * @method static value2(string $value)
 *
 * @property string $value1
 * @property string $value2
 */
class ChildStruct extends StructAbstract
{
}
