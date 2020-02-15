<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct\Child;

use NeoVg\Struct\StructAbstract;

/**
 * @property string $value1
 * @property string $value2
 *
 * @method $this value1(string $value)
 * @method $this value2(string $value)
 */
class ChildStruct extends StructAbstract
{
}
