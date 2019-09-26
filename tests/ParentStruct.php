<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @method strings(array $values)
 * @method child(\NeoVg\Struct\Test\ChildStruct $value)
 * @method childs(array $values);
 *
 * @property string[]                         $strings
 * @property \NeoVg\Struct\Test\ChildStruct   $child
 * @property \NeoVg\Struct\Test\ChildStruct[] $childs
 */
class ParentStruct extends StructAbstract
{
}
