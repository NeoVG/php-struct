<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

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
 * @property mixed     $mixed
 */
class SimpleTestStruct extends StructAbstract
{
    protected $default = 'default value';
}
