<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;

/**
 * Class TestStruct
 *
 * A simple example Struct used to test all available data types for properties.
 *
 * @method self bool(bool $value)
 * @method self int(int $value)
 * @method self double(double $value)
 * @method self string(string $value)
 * @method self array(array $value)
 * @method self stdClass(\stdClass $value)
 * @method self callable(callable $value)
 * @method self default(string $value)
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
