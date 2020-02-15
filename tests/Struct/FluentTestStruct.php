<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test\Struct;

use NeoVg\Struct\StructAbstract;

/**
 * A simple example Struct used to test all available data types for properties.
 *
 * @property bool               $bool
 * @property boolean            $boolean
 * @property int                $int
 * @property integer            $integer
 * @property float              $float
 * @property double             $double
 * @property string             $string
 * @property array              $array
 * @property \stdClass          $stdClass
 * @property \DateTimeInterface $interface
 * @property callable           $callable
 * @property string             $default
 *
 * @method $this bool(bool $value)
 * @method $this boolean(boolean $value)
 * @method $this int(int $value)
 * @method $this integer(integer $value)
 * @method $this float(float $value)
 * @method $this double(double $value)
 * @method $this string(string $value)
 * @method $this array(array $value)
 * @method $this stdClass(\stdClass $value)
 * @method $this interface(\DateTimeInterface $value)
 * @method $this callable(callable $value)
 * @method $this default(string $value)
 */
class FluentTestStruct extends StructAbstract
{
    /**
     * @var string
     */
    protected $default = 'default value';
}
