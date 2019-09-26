<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @method $this value1(string $value)
 * @method $this value2(string $value)
 *
 * @property string $value1
 * @property string $value2
 */
class ChildStruct extends StructAbstract
{
}
