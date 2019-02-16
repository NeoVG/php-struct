<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use PHPUnit\Framework\TestCase;

class TestStructTest extends TestCase
{
    /**
     * @var TestStruct
     */
    protected $_instance;

    public function setUp()
    {
        $this->_instance = new TestStruct();
    }

    private function _mockContent()
    {
        $this->_instance
            ->bool(true)
            ->int(0)
            ->double(0.0)
            ->string('')
            ->array([])
            ->stdClass(new \stdClass());
    }

    public function testNew()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance);
    }

    public function testNull()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->bool(null));
        $this->assertInternalType('null', $this->_instance->bool);
    }

    public function testBool()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->bool(true));
        $this->assertInternalType('bool', $this->_instance->bool);
    }

    public function testInt()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->int(0));
        $this->assertInternalType('int', $this->_instance->int);
    }

    public function testDouble()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->double(0.0));
        $this->assertInternalType('double', $this->_instance->double);
    }

    public function testString()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->string(''));
        $this->assertInternalType('string', $this->_instance->string);
    }

    public function testArray()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->array([]));
        $this->assertInternalType('array', $this->_instance->array);
    }

    public function testStdClass()
    {
        $this->assertInstanceOf(TestStruct::class, $this->_instance->stdClass(new \stdClass));
        $this->assertInternalType('object', $this->_instance->stdClass);
        $this->assertInstanceOf(\stdClass::class, $this->_instance->stdClass);
    }

    public function testDefault()
    {
        $this->assertEquals('default value', $this->_instance->default);
    }

    public function testFailMissingArgument()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->_instance->bool();
    }

    public function testFailType()
    {
        $this->expectException(\TypeError::class);
        $this->_instance->bool(0);
    }

    public function testToArray()
    {
        $this->_mockContent();

        $value = $this->_instance->toArray();

        $this->assertInternalType('array', $value);

        $this->assertArrayHasKey('bool', $value);
        $this->assertArrayHasKey('int', $value);
        $this->assertArrayHasKey('double', $value);
        $this->assertArrayHasKey('string', $value);
        $this->assertArrayHasKey('array', $value);
        $this->assertArrayHasKey('stdClass', $value);

        $this->assertInternalType('bool', $value['bool']);
        $this->assertInternalType('int', $value['int']);
        $this->assertInternalType('double', $value['double']);
        $this->assertInternalType('string', $value['string']);
        $this->assertInternalType('array', $value['array']);
        $this->assertInternalType('object', $value['stdClass']);
        $this->assertInstanceOf(\stdClass::class, $value['stdClass']);
    }

    public function testJsonSerialize()
    {
        $this->_mockContent();

        $value = $this->_instance->jsonSerialize();

        $this->assertInstanceOf(\stdClass::class, $value);

        $this->assertObjectHasAttribute('bool', $value);
        $this->assertObjectHasAttribute('int', $value);
        $this->assertObjectHasAttribute('double', $value);
        $this->assertObjectHasAttribute('string', $value);
        $this->assertObjectHasAttribute('array', $value);
        $this->assertObjectHasAttribute('stdClass', $value);

        $this->assertInternalType('bool', $value->bool);
        $this->assertInternalType('int', $value->int);
        $this->assertInternalType('double', $value->double);
        $this->assertInternalType('string', $value->string);
        $this->assertInternalType('array', $value->array);
        $this->assertInternalType('object', $value->stdClass);
        $this->assertInstanceOf(\stdClass::class, $value->stdClass);
    }

    public function test__toString()
    {
        $this->_mockContent();

        $this->assertJson((string)$this->_instance);

        $value = json_decode((string)$this->_instance);

        $this->assertInstanceOf(\stdClass::class, $value);

        $this->assertObjectHasAttribute('bool', $value);
        $this->assertObjectHasAttribute('int', $value);
        $this->assertObjectHasAttribute('double', $value);
        $this->assertObjectHasAttribute('string', $value);
        $this->assertObjectHasAttribute('array', $value);
        $this->assertObjectHasAttribute('stdClass', $value);

        $this->assertInternalType('bool', $value->bool);
        $this->assertInternalType('int', $value->int);
        $this->assertInternalType('double', $value->double);
        $this->assertInternalType('string', $value->string);
        $this->assertInternalType('array', $value->array);
        $this->assertInternalType('object', $value->stdClass);
        $this->assertInstanceOf(\stdClass::class, $value->stdClass);
    }
}
