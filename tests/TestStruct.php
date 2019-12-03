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
 * @method self bool(bool $value)
 * @method self int(int $value)
 * @method self float(float $value)
 * @method self string(string $value)
 * @method self array(array $value)
 * @method self stdClass(\stdClass $value)
 * @method self callable(callable $value)
 * @method self default(string $value)
 */
class TestStruct extends StructAbstract
{
    protected $default = 'default value';
}
