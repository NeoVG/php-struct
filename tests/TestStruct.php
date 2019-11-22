<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * Class TestStruct
 *
 * A simple example Struct used to test all available data types for properties.
 *
 * @method static bool(bool $value)
 * @method static int(int $value)
 * @method static float(float $value)
 * @method static string(string $value)
 * @method static array(array $value)
 * @method static stdClass(\stdClass $value)
 * @method static callable(callable $value)
 * @method static default(string $value)
 *
 * @property-read bool      $bool
 * @property-read int       $int
 * @property-read double    $double
 * @property-read string    $string
 * @property-read array     $array
 * @property-read \stdClass $stdClass
 * @property-read callable  $callable
 * @property-read string    $default
 */
class TestStruct extends StructAbstract
{
    protected $default = 'default value';
}
