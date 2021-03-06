<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\StructProperty\DefaultProperty;
use NeoVg\Struct\Test\Struct\SimpleTestStruct;
use PHPUnit\Framework\TestCase;

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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInstanciation()
    {
        $instance = new SimpleTestStruct();
        $this->assertIsObject($instance);
        $this->assertInstanceOf(StructAbstract::class, $instance);
        $this->assertInstanceOf(SimpleTestStruct::class, $instance);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testProperties()
    {
        $instance = new SimpleTestStruct();
        $properties = $instance->getProperties();

        # Test property names
        $this->assertEquals([
            'bool'     => 'bool',
            'int'      => 'int',
            'float'    => 'float',
            'string'   => 'string',
            'array'    => 'array',
            'stdClass' => 'stdClass',
            'default'  => 'default',
            'mixed'    => 'mixed',
        ], array_map(function (DefaultProperty $property) {
            return $property->getName();
        }, $properties));

        # Test property types
        $this->assertEquals([
            'string'   => 'string',
            'array'    => 'array',
            'default'  => 'string',
            'bool'     => 'boolean',
            'int'      => 'integer',
            'float'    => 'double',
            'stdClass' => '\stdClass',
            'mixed'    => 'mixed',
        ], array_map(function (DefaultProperty $property) {
            return $property->getType();
        }, $properties));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCreateFromArrayWithAdditionalProperties()
    {
        $instance = SimpleTestStruct::createFromArray([
            'foo' => 'foo',
            'bar' => 'bar',
        ]);
        $this->assertEquals(<<<'EOD'
{
    "default": "default value"
}
EOD
            , (string)$instance);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSlashEscaping()
    {
        $instance = SimpleTestStruct::createFromArray([
            'string' => '/',
        ]);
        $this->assertEquals(<<<'EOD'
{
    "string": "/",
    "default": "default value"
}
EOD
            , (string)$instance);
    }

    /**
     *
     */
    public function testMixed()
    {
        $instance = new SimpleTestStruct();
        $instance->mixed = true;
        $this->assertIsBool($instance->mixed);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDefaultValueSomeMore()
    {
        $foo1 = SimpleTestStruct::createFromArray([]);
        $this->assertEquals('default value', $foo1->default);

        $foo2 = SimpleTestStruct::createFromArray([
            'bool' => true,
            'default' => 'foobar'
        ]);
        $this->assertEquals('foobar', $foo2->default);

        $foo3 = SimpleTestStruct::createFromArray([]);
        $foo3->default = 'blafasel';
        $this->assertEquals('blafasel', $foo3->default);
    }
}
