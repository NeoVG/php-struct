<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * Class TestStruct
 *
 * A simple example Struct used to test all available data types for properties.
 *
 * @method $this bool(bool $value)
 * @method $this int(int $value)
 * @method $this double(double $value)
 * @method $this string(string $value)
 * @method $this array(array $value)
 * @method $this stdClass(\stdClass $value)
 * @method $this default(string $value)
 *
 * @property-read bool      $bool
 * @property-read int       $int
 * @property-read double    $double
 * @property-read string    $string
 * @property-read array     $array
 * @property-read \stdClass $stdClass
 * @property-read string    $default
 */
class TestStruct extends StructAbstract
{
    protected $default = 'default value';
}
