<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\EnumAbstract;
use NeoVg\Struct\EnumStructProperty;
use NeoVg\Struct\StructAbstract;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class NullableEnum
 */
class NullableStructEnum extends EnumAbstract
{
    const NULL = null;
}

/**
 * Class NotNullableEnum
 */
class NotNullableStructEnum extends EnumAbstract
{
    const FOO = 'foo';
}

/**
 * @property NullableStructEnum                       $nullable
 * @property \NeoVg\Struct\Test\NotNullableStructEnum $notNullable
 *
 * @method $this withNullable(?string $value)
 * @method $this withNotNullable(string $value)
 */
class EnumStruct extends StructAbstract
{
}

/**
 * @property NotNullableStructEnum $default
 */
class DefaultEnumStruct extends StructAbstract
{
    protected $default = NotNullableStructEnum::FOO;
}

class EnumStructTest extends TestCase
{
    ####################################################################################################################
    # __construct()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructEnumProperty()
    {
        $struct = new EnumStruct();
        $this->assertInstanceOf(EnumStructProperty::class, $struct->getProperty('nullable'));
        $this->assertInstanceOf(EnumStructProperty::class, $struct->getProperty('notNullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructCreateOkWithDefaultValuesOnly()
    {
        $struct = new DefaultEnumStruct();
        $this->assertNotNull($struct->default);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->default);
        $this->assertEquals(NotNullableStructEnum::FOO, $struct->default->getValue());
    }

    ####################################################################################################################
    # createFromArray()
    ####################################################################################################################

    /**
     *
     */
    public function testStructCreateFromArrayErrorNull()
    {
        $this->expectException(Error::class);
        EnumStruct::createFromArray(['notNullable' => null]);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructCreateFromArrayOkNull()
    {
        $struct = EnumStruct::createFromArray(['nullable' => null]);
        $this->assertNotNull($struct->nullable);
        $this->assertInstanceOf(NullableStructEnum::class, $struct->nullable);
        $this->assertNull($struct->nullable->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructCreateFromArrayOkValue()
    {
        $struct = EnumStruct::createFromArray(['notNullable' => NotNullableStructEnum::FOO]);
        $this->assertNotNull($struct->notNullable);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->notNullable);
        $this->assertEquals(NotNullableStructEnum::FOO, $struct->notNullable->getValue());
    }

    ####################################################################################################################
    # Fluent Setters
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructFluentSetterWithEnumObjects()
    {
        $struct = (new EnumStruct())
            ->withNullable(null)
            ->withNotNullable(NotNullableStructEnum::FOO);

        $this->assertNotNull($struct->notNullable);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->notNullable);

        $this->assertNotNull($struct->nullable);
        $this->assertInstanceOf(NullableStructEnum::class, $struct->nullable);
    }

    ####################################################################################################################
    # Magic Setters
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructMagicSetterWithEnumObjects()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum();
        $struct->notNullable = new NotNullableStructEnum(NotNullableStructEnum::FOO);

        $this->assertNotNull($struct->nullable);
        $this->assertInstanceOf(NullableStructEnum::class, $struct->nullable);

        $this->assertNotNull($struct->notNullable);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->notNullable);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructMagicSetterWithScalarObjects()
    {
        $struct = new EnumStruct();
        $struct->nullable = null;
        $struct->notNullable = NotNullableStructEnum::FOO;

        $this->assertNotNull($struct->nullable);
        $this->assertInstanceOf(NullableStructEnum::class, $struct->nullable);
        $this->assertNull($struct->nullable->getValue());

        $this->assertNotNull($struct->notNullable);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->notNullable);
        $this->assertEquals(NotNullableStructEnum::FOO, $struct->notNullable->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructMagicSetterWithImplizitEnumGeneration()
    {
        $struct = new EnumStruct();
        $struct->nullable->setValue(null);
        $struct->notNullable->setValue(NotNullableStructEnum::FOO);

        $this->assertNotNull($struct->nullable);
        $this->assertInstanceOf(NullableStructEnum::class, $struct->nullable);
        $this->assertNull($struct->nullable->getValue());

        $this->assertNotNull($struct->notNullable);
        $this->assertInstanceOf(NotNullableStructEnum::class, $struct->notNullable);
        $this->assertEquals(NotNullableStructEnum::FOO, $struct->notNullable->getValue());
    }

    ####################################################################################################################
    # isSet()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsSetFalseWithNoEnumInProperty()
    {
        $struct = new EnumStruct();
        $this->assertFalse($struct->isSet('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsSetFalseWithEmptyEnum()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum();
        $this->assertFalse($struct->isSet('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsSetTrueWithEnumInput()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum(null);
        $this->assertTrue($struct->isSet('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsSetTrueWithScalarInput()
    {
        $struct = new EnumStruct();
        $struct->nullable = null;
        $this->assertTrue($struct->isSet('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsSetWithImplicitEnumGeneration()
    {
        $struct = new EnumStruct();
        $struct->nullable->setValue(null);
        $this->assertTrue($struct->isSet('nullable'));
    }

    ####################################################################################################################
    # isDirty()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsDirtyFalseWithNoEnumInProperty()
    {
        $struct = new EnumStruct();
        $this->assertFalse($struct->isDirty('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsDirtyWithEmptyEnum()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum();
        $this->assertFalse($struct->isDirty('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsDirtyOIsTrueWithEnumInput()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum(null);
        $this->assertTrue($struct->isDirty('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsDirtyTrueWithScalarInput()
    {
        $struct = new EnumStruct();
        $struct->nullable = null;
        $this->assertTrue($struct->isDirty('nullable'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructIsDirtyWithImplicidEnumGeneration()
    {
        $struct = new EnumStruct();
        $struct->nullable->setValue(null);
        $this->assertTrue($struct->isDirty('nullable'));
    }

    ####################################################################################################################
    # setDirty()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetDirtyWithNoEnumInProperty()
    {
        $struct = new EnumStruct();

        $struct->setDirty('nullable');
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->nullable->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetDirtyWithEmptyEnum()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum();

        $struct->setDirty('nullable');
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->nullable->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetDirtyOk()
    {
        $struct = EnumStruct::createFromArray(['nullable' => null])->clean();
        $this->assertFalse($struct->isDirty('nullable'));

        $struct->setDirty('nullable');
        $this->assertTrue($struct->nullable->isDirty());
    }

    ####################################################################################################################
    # setClean()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetCleanWithNoEnumInProperty()
    {
        $struct = new EnumStruct();

        $struct->setClean('nullable');
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->nullable->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetCleanWithEmptyEnum()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum();

        $struct->setClean('nullable');
        $this->assertFalse($struct->isDirty());
        $this->assertFalse($struct->nullable->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructSetCleanOk()
    {
        $struct = new EnumStruct();
        $struct->nullable = new NullableStructEnum(null);
        $this->assertTrue($struct->isDirty('nullable'));

        $struct->setClean('nullable');
        $this->assertFalse($struct->nullable->isDirty());
    }

    ####################################################################################################################
    # toArray()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructToArray()
    {
        $struct = EnumStruct::createFromArray([
            'nullable'    => NullableStructEnum::NULL,
            'notNullable' => NotNullableStructEnum::FOO,
        ]);
        $array = $struct->toArray();
        $this->assertNull($array['nullable']);
        $this->assertEquals(NotNullableStructEnum::FOO, $array['notNullable']);
    }

    ####################################################################################################################
    # __toString()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructToString()
    {
        $array = [
            'nullable'    => null,
            'notNullable' => NotNullableStructEnum::FOO,
        ];

        $struct = EnumStruct::createFromArray($array);

        $this->assertEquals(
            json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT),
            (string)$struct
        );
    }
}
