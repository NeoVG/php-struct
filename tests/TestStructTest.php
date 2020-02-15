<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class TestStruct
 *
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
class TestStruct extends StructAbstract
{
    /**
     * @var string
     */
    protected $default = 'default value';
}

/**
 * Class TestStructTest
 */
class TestStructTest extends TestCase
{
    /**
     * @var TestStruct
     */
    protected $_instance;

    /**
     *
     */
    public function setUp()
    {
        $this->_instance = new TestStruct();
    }

    /**
     *
     */
    private function _mockContent()
    {
        $this->_instance
            ->bool(true)
            ->boolean(false)
            ->int(0)
            ->integer(1337)
            ->float(0.0)
            ->double(13.37)
            ->string('')
            ->array([])
            ->stdClass(new \stdClass())
            ->interface(new \DateTime())
            ->callable(function () {
            });
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testNew()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testNull()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->bool(null));
        $this->assertNull($this->_instance->bool);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testBool()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->bool(true)->boolean(false));
        $this->assertIsBool($this->_instance->bool);
        $this->assertIsBool($this->_instance->boolean);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInt()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->int(0)->integer(1));
        $this->assertIsInt($this->_instance->int);
        $this->assertIsInt($this->_instance->integer);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDouble()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->float(0.0)->double(0.815));
        $this->assertIsFloat($this->_instance->float);
        $this->assertIsFloat($this->_instance->double);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testString()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->string(''));
        $this->assertIsString($this->_instance->string);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testArray()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->array([]));
        $this->assertIsArray($this->_instance->array);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStdClass()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->stdClass(new \stdClass));
        $this->assertIsObject($this->_instance->stdClass);
        $this->assertInstanceOf(\stdClass::class, $this->_instance->stdClass);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInterface()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->interface(new \DateTime()));
        $this->assertIsObject($this->_instance->interface);
        $this->assertInstanceOf(\DateTimeInterface::class, $this->_instance->interface);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCallable()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->callable(function () {
        }));
        $this->assertIsCallable($this->_instance->callable);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDefault()
    {
        $this->assertEquals('default value', $this->_instance->default);
    }

    /**
     *
     */
    public function testFailMissingArgument()
    {
        $this->expectException(Error::class);
        $this->_instance->bool();
    }

    /**
     *
     */
    public function testFailType()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Argument 1 passed to NeoVg\Struct\StructProperty::bool() must be of type boolean, integer given');
        $this->_instance->bool(0);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testToArray()
    {
        $this->_mockContent();

        $value = $this->_instance->toArray();

        $this->assertIsArray($value);

        $this->assertArrayHasKey('bool', $value);
        $this->assertArrayHasKey('int', $value);
        $this->assertArrayHasKey('float', $value);
        $this->assertArrayHasKey('string', $value);
        $this->assertArrayHasKey('array', $value);
        $this->assertArrayHasKey('stdClass', $value);

        $this->assertIsBool($value['bool']);
        $this->assertIsInt($value['int']);
        $this->assertIsFloat($value['float']);
        $this->assertIsString($value['string']);
        $this->assertIsArray($value['array']);
        $this->assertIsObject($value['stdClass']);
        $this->assertInstanceOf(\stdClass::class, $value['stdClass']);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonSerialize()
    {
        $this->_mockContent();

        $value = $this->_instance->jsonSerialize();

        $this->assertInstanceOf(\stdClass::class, $value);

        $this->assertObjectHasAttribute('bool', $value);
        $this->assertObjectHasAttribute('int', $value);
        $this->assertObjectHasAttribute('float', $value);
        $this->assertObjectHasAttribute('string', $value);
        $this->assertObjectHasAttribute('array', $value);
        $this->assertObjectHasAttribute('stdClass', $value);

        $this->assertIsBool($value->bool);
        $this->assertIsInt($value->int);
        $this->assertIsFloat($value->float);
        $this->assertIsString($value->string);
        $this->assertIsArray($value->array);
        $this->assertIsObject($value->stdClass);
        $this->assertInstanceOf(\stdClass::class, $value->stdClass);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function test__toString()
    {
        $this->_mockContent();

        $this->assertJson((string)$this->_instance);

        $value = json_decode((string)$this->_instance);

        $this->assertInstanceOf(\stdClass::class, $value);

        $this->assertObjectHasAttribute('bool', $value);
        $this->assertObjectHasAttribute('int', $value);
        $this->assertObjectHasAttribute('float', $value);
        $this->assertObjectHasAttribute('string', $value);
        $this->assertObjectHasAttribute('array', $value);
        $this->assertObjectHasAttribute('stdClass', $value);

        $this->assertIsBool($value->bool);
        $this->assertIsInt($value->int);
        $this->assertIsFloat($value->double);
        $this->assertIsString($value->string);
        $this->assertIsArray($value->array);
        $this->assertIsObject($value->stdClass);
        $this->assertInstanceOf(\stdClass::class, $value->stdClass);
    }

    /**
     *
     */
    public function testDebugInfo()
    {
        $this->_mockContent();

        $this->assertIsArray($this->_instance->debugInfo());
        foreach ($this->_instance->getProperties() as $property) {
            $this->assertIsArray($property->debugInfo());
        }
    }
}
