<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property \NeoVg\Struct\Test\ChildStruct $child
 *
 * @method static self createFromArray(array $arrayProperties)
 * @method self clean()
 */
class ParentStruct extends StructAbstract
{
}
