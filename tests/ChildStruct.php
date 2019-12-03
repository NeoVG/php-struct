<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property string $value1
 * @property string $value2
 *
 * @method self value1(string $value)
 * @method self value2(string $value)
 */
class ChildStruct extends StructAbstract
{
}
