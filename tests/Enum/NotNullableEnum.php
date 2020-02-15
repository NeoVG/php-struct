<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Enum;

use NeoVg\Struct\EnumAbstract;

/**
 * Class NotNullableEnum
 */
class NotNullableEnum extends EnumAbstract
{
    const FOO   = 'foo';
    const BAR   = 1;
    const BLA   = true;
    const FASEL = false;
}
