<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property string[]                         $strings
 * @property \NeoVg\Struct\Test\ChildStruct   $child
 * @property \NeoVg\Struct\Test\ChildStruct[] $childs
 *
 * @method $this strings(array $values)
 * @method $this child(\NeoVg\Struct\Test\ChildStruct $value)
 * @method $this childs(\NeoVg\Struct\Test\ChildStruct[] $values);
 */
class ParentStruct extends StructAbstract
{
}
