<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * @property string[]                         $strings
 * @property \NeoVg\Struct\Test\ChildStruct   $child
 * @property \NeoVg\Struct\Test\ChildStruct[] $childs
 *
 * @method self strings(array $values)
 * @method self child(\NeoVg\Struct\Test\ChildStruct $value)
 * @method self childs(\NeoVg\Struct\Test\ChildStruct[] $values);
 */
class ParentStruct extends StructAbstract
{
}
