<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;

/**
 * Class A
 */
class A
{
}

/**
 * Class B
 */
class B extends A
{
}

/**
 * @property A $foo
 */
class DefinePropertyStruct extends StructAbstract
{
}

/**
 * @property B $foo
 */
class RedefinePropertyStruct extends DefinePropertyStruct
{
}
