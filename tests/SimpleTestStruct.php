<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * Class NewTestStruct
 *
 * @property bool      $bool
 * @property int       $int
 * @property float     $float
 * @property string    $string
 * @property array     $array
 * @property \stdClass $stdClass
 * @property string    $default
 */
class SimpleTestStruct extends StructAbstract
{
    protected $default = 'default value';
}
