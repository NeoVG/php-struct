<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;

/**
 * @property string[]                                      $strings
 * @property \NeoVg\Struct\Test\Struct\Child\ChildStruct   $child
 * @property \NeoVg\Struct\Test\Struct\Child\ChildStruct[] $childs
 * @property Child\ChildStruct   $relativeChild
 *
 * @method $this strings(array $values)
 * @method $this child(\NeoVg\Struct\Test\Struct\Child\ChildStruct $value)
 * @method $this childs(\NeoVg\Struct\Test\Struct\Child\ChildStruct[] $values);
 * @method $this relativeChild(Child\ChildStruct $value)
 */
class ParentStruct extends StructAbstract
{
}
