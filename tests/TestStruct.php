<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * Class TestStruct
 *
 * A simple example Struct used to test all available data types for properties.
 *
 * @property bool      $bool
 * @property int       $int
 * @property double    $double
 * @property string    $string
 * @property array     $array
 * @property \stdClass $stdClass
 * @property callable  $callable
 * @property string    $default
 *
 * @method $this bool(bool $value)
 * @method $this int(int $value)
 * @method $this float(float $value)
 * @method $this string(string $value)
 * @method $this array(array $value)
 * @method $this stdClass(\stdClass $value)
 * @method $this callable(callable $value)
 * @method $this default(string $value)
 */
class TestStruct extends StructAbstract
{
    protected $default = 'default value';
}
