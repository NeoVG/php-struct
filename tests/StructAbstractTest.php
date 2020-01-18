<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\StructProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class StructAbstractTest
 */
class StructAbstractTest extends TestCase
{
    /**
     * @var StructAbstract
     */
    private $_stub;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->_stub = $this->getMockForAbstractClass(StructAbstract::class);
    }

    /**
     * @throws \ReflectionException
     */
    private function _mockContent()
    {
        $reflectionClass = new \ReflectionClass(StructAbstract::class);
        $reflectionProperties = $reflectionClass->getProperty('_properties');
        $reflectionProperties->setAccessible(true);

        $reflectionProperties->setValue($this->_stub, [
            'test' => new StructProperty(null, 'null', 'test', 'bool', null),
        ]);
    }

    /**
     *
     */
    public function testNew()
    {
        $this->assertInstanceOf(StructAbstract::class, $this->_stub);
    }

    /**
     * @throws \ReflectionException
     */
    public function test__call()
    {
        $this->_mockContent();

        # Positive
        $this->assertInstanceOf(StructAbstract::class, $this->_stub->test(true));

        # Negative
        $this->expectException(\BadMethodCallException::class);
        $this->_stub->doesNotExist();
    }

    /**
     * @throws \ReflectionException
     */
    public function test__get()
    {
        $this->_mockContent();

        $this->assertNull($this->_stub->test);
    }

    /**
     *
     */
    public function testToArray()
    {
        $this->assertEquals([], $this->_stub->toArray());
    }

    /**
     *
     */
    public function test_jsonSerialize()
    {
        $this->assertInstanceOf(\stdClass::class, $this->_stub->jsonSerialize());
    }

    /**
     *
     */
    public function test__toString()
    {
        $this->assertJson((string)$this->_stub);
        $this->assertInstanceOf(\stdClass::class, json_decode((string)$this->_stub));
    }
}
