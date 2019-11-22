<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @method static strings(array $values)
 * @method static child(\NeoVg\Struct\Test\ChildStruct $value)
 * @method static childs(\NeoVg\Struct\Test\ChildStruct[] $values);
 *
 * @property string[]                         $strings
 * @property \NeoVg\Struct\Test\ChildStruct   $child
 * @property \NeoVg\Struct\Test\ChildStruct[] $childs
 */
class ParentStruct extends StructAbstract
{
}
