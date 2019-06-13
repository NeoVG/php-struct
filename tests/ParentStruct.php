<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property \NeoVg\Struct\Test\ChildStruct $child
 *
 * @method static $this createFromArray(array $arrayProperties)
 * @method $this clean()
 */
class ParentStruct extends StructAbstract
{
}
