<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\StructProperty;
use PHPUnit\Framework\TestCase;

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

/**
 * Class TestStructTest
 */
class SimpleTestStructTest extends TestCase
{
    /**
     * @var SimpleTestStruct
     */
    protected $_instance;

    /**
     *
     */
    public function testInstanciation()
    {
        $instance = new SimpleTestStruct();
        $this->assertIsObject($instance);
        $this->assertInstanceOf(StructAbstract::class, $instance);
        $this->assertInstanceOf(SimpleTestStruct::class, $instance);
    }

    /**
     *
     */
    public function testProperties()
    {
        $instance = new SimpleTestStruct();
        $properties = $instance->getProperties();

        # Test property names
        $this->assertEquals([
            'bool',
            'int',
            'float',
            'string',
            'array',
            'stdClass',
            'default',
        ], array_map(function (StructProperty $property) {
            return $property->getName();
        }, $properties));

        # Test property types
        $this->assertEquals([
            'boolean',
            'integer',
            'double',
            'string',
            'array',
            '\stdClass',
            'string',
        ], array_map(function (StructProperty $property) {
            return $property->getType();
        }, $properties));
    }

    /**
     *
     */
    public function testSetterAndGetter()
    {
        $instance = new SimpleTestStruct();

        $instance->bool = true;
        $this->assertIsBool($instance->bool);
        $this->assertTrue($instance->bool);

        $instance->int = 1;
        $this->assertIsInt($instance->int);
        $this->assertEquals(1, $instance->int);

        $instance->float = 1.1;
        $this->assertIsFloat($instance->float);
        $this->assertEquals(1.1, $instance->float);

        $instance->string = 'foobar';
        $this->assertIsString($instance->string);
        $this->assertEquals('foobar', $instance->string);

        $instance->array = [1, 2, 3];
        $this->assertIsArray($instance->array);
        $this->assertEquals([1, 2, 3], $instance->array);

        $instance->stdClass = new \stdClass();
        $this->assertIsObject($instance->stdClass);
        $this->assertInstanceOf(\stdClass::class, $instance->stdClass);
        $this->assertEquals(new \stdClass(), $instance->stdClass);
    }

    /**
     *
     */
    public function testDefault()
    {
        $instance = new SimpleTestStruct();

        $this->assertFalse($instance->getProperty('string')->hasDefaultValue());
        $this->assertNull($instance->getProperty('string')->getDefaultValue());

        $this->assertTrue($instance->getProperty('default')->hasDefaultValue());
        $this->assertEquals('default value', $instance->getProperty('default')->getDefaultValue());

        $this->assertIsString($instance->default);
        $this->assertEquals('default value', $instance->default);
    }

    /**
     * @throws \JsonException
     */
    public function testCreateFromJsonErrors()
    {
        $this->expectException(\JsonException::class);
        SimpleTestStruct::createFromJson(null);

        $instance = SimpleTestStruct::createFromJsonNullOnError(null);
        $this->assertNull($instance);

        $this->expectException(\JsonException::class);
        SimpleTestStruct::createFromJson('');

        $instance = SimpleTestStruct::createFromJsonNullOnError('');
        $this->assertNull($instance);

        $this->expectException(\JsonException::class);
        SimpleTestStruct::createFromJson('null');

        $instance = SimpleTestStruct::createFromJsonNullOnError('null');
        $this->assertNull($instance);
    }

    /**
     *
     */
    public function testCreateFromArrayWithAdditionalProperties()
    {
        $instance = SimpleTestStruct::createFromArray([
            'foo' => 'foo',
            'bar' => 'bar',
        ]);
        $this->assertEquals('{"default":"default value"}', (string)$instance);
    }

    public function testSlashEscaping()
    {
        $instance = (new SimpleTestStruct())
            ->string('/');
        $this->assertEquals('{"string":"/","default":"default value"}', (string)$instance);
    }
}
