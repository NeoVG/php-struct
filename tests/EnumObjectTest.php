<?php

declare(strict_types=1);

namespace NeoVg\Struct\Test;

use NeoVg\Struct\EnumAbstract;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class NotNullableEnum
 */
class NotNullableEnum extends EnumAbstract
{
    const FOO   = 'foo';
    const BAR   = 1;
    const BLA   = true;
    const FASEL = false;
}

/**
 * Class NullableEnum
 */
class NullableEnum extends NotNullableEnum
{
    /**
     *
     */
    const NULL = null;
}

/**
 * Class EnumTest
 */
class EnumObjectTest extends TestCase
{
    ####################################################################################################################
    # __construct()
    ####################################################################################################################

    /**
     *
     */
    public function testConstructErrorNull()
    {
        $this->expectException(Error::class);
        new NotNullableEnum(null);
    }

    /**
     *
     */
    public function testConstructErrorWrongValue()
    {
        $this->expectException(Error::class);
        new NotNullableEnum('leberkäs');
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructOkEmpty()
    {
        $enum = new NotNullableEnum();
        $this->assertFalse($enum->isSet());

        $enum = new NullableEnum();
        $this->assertFalse($enum->isSet());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructOkNull()
    {
        $enum = new NullableEnum(null);
        $this->assertTrue($enum->isSet());
        $this->assertNull($enum->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructOkValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NotNullableEnum::FOO, $enum->getValue());

        $enum = new NotNullableEnum(NotNullableEnum::BAR);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NotNullableEnum::BAR, $enum->getValue());

        $enum = new NotNullableEnum(NotNullableEnum::BLA);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NotNullableEnum::BLA, $enum->getValue());
    }

    ####################################################################################################################
    # isSet()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsSetFalse()
    {
        $enum = new NullableEnum();
        $this->assertFalse($enum->isSet());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsSetTrueAfterConstruct()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertTrue($enum->isSet());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsSetTrueAfterSetValue()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertTrue($enum->isSet());
    }

    ####################################################################################################################
    # isDirty()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsDirtyFalseEmpty()
    {
        $enum = new NullableEnum();
        $this->assertFalse($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsDirtyFalseAfterConstruct()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertFalse($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsDirtyTrueAfterSetValue()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertTrue($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsDirtyTrueAfterSetSameValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->setValue(NotNullableEnum::FOO);
        $this->assertTrue($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsDirtyFalseAfterUnsetValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->unsetValue();
        $this->assertFalse($enum->isDirty());
    }

    ####################################################################################################################
    # isValidValue()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsValidValueErrorNull()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertFalse($enum->isValidValue(null));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsValidValueErrorWrongValue()
    {
        $enum = new NullableEnum();
        $this->assertFalse($enum->isValidValue('leberkäs'));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsValidValueOkNull()
    {
        $enum = new NullableEnum();
        $this->assertTrue($enum->isValidValue(null));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testIsValidValueOkValue()
    {
        $enum = new NullableEnum();
        $this->assertTrue($enum->isValidValue(NullableEnum::FOO));
    }

    ####################################################################################################################
    # setValue()
    ####################################################################################################################

    /**
     *
     */
    public function testSetValueErrorNull()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->expectException(Error::class);
        $enum->setValue(null);
    }

    /**
     *
     */
    public function testSetValueErrorWrongValue()
    {
        $enum = new NullableEnum();
        $this->expectException(Error::class);
        $enum->setValue('leberkäs');
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetValueOkNull()
    {
        $enum = new NullableEnum();
        $enum->setValue(null);
        $this->assertNull($enum->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetValueOkValue()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NullableEnum::FOO, $enum->getValue());

        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::BAR);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NullableEnum::BAR, $enum->getValue());

        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::BLA);
        $this->assertTrue($enum->isSet());
        $this->assertEquals(NullableEnum::BLA, $enum->getValue());
    }

    ####################################################################################################################
    # getValue()
    ####################################################################################################################

    /**
     *
     */
    public function testGetValueErrorNotSet()
    {
        $enum = new NullableEnum();
        $this->expectException(Error::class);
        $enum->getValue();
    }

    /**
     *
     */
    public function testGetValueErrorAfterUnset()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->unsetValue();
        $this->expectException(Error::class);
        $enum->getValue();
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetValueOkAfterConstructWithNull()
    {
        $enum = new NullableEnum(null);
        $this->assertEquals(null, $enum->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetValueOkAfterConstructWithValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertEquals(NotNullableEnum::FOO, $enum->getValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetValueOkAfterSetValue()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertEquals(NullableEnum::FOO, $enum->getValue());
    }

    ####################################################################################################################
    # hasOriginalValue()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testHasOriginalValueFalse()
    {
        $enum = new NullableEnum();
        $this->assertFalse($enum->hasOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testHasOriginalValueTrueAfterConstructWithNull()
    {
        $enum = new NullableEnum(null);
        $this->assertTrue($enum->hasOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testHasOriginalValueTrueAfterConstructWithValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertTrue($enum->hasOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testHasOriginalValueTrueAfterSetClean()
    {
        $enum = new NullableEnum();
        $enum->setValue(null);
        $this->assertFalse($enum->hasOriginalValue());
        $enum->setClean();
        $this->assertTrue($enum->hasOriginalValue());
    }

    ####################################################################################################################
    # getOriginalValue()
    ####################################################################################################################

    /**
     *
     */
    public function testGetOriginalValueErrorNotSet()
    {
        $enum = new NullableEnum();
        $this->expectException(Error::class);
        $enum->getOriginalValue();
    }

    /**
     *
     */
    public function testGetOriginalValueErrorAfterUnset()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->unsetValue();
        $this->expectException(Error::class);
        $enum->getOriginalValue();
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOriginalValueOkAfterConstructWithNull()
    {
        $enum = new NullableEnum(null);
        $this->assertEquals(null, $enum->getOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOriginalValueOkAfterConstructWithValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertEquals(NotNullableEnum::FOO, $enum->getOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOriginalValueOkAfterSetValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->setValue(NotNullableEnum::BAR);
        $this->assertEquals(NotNullableEnum::FOO, $enum->getOriginalValue());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOriginalValueOkAfterSetClean()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->setValue(NotNullableEnum::BAR);
        $enum->setClean();
        $this->assertEquals(NotNullableEnum::BAR, $enum->getOriginalValue());
    }

    ####################################################################################################################
    # unsetValue()
    ####################################################################################################################

    /**
     *
     */
    public function testUnsetValueErrorNotSet()
    {
        $enum = new NullableEnum();
        $this->expectException(Error::class);
        $enum->unsetValue();
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUnsetValueOkAfterConstruct()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->unsetValue();
        $this->assertFalse($enum->isSet());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUnsetValueOkAfterSetValue()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $enum->unsetValue();
        $this->assertFalse($enum->isSet());
    }

    ####################################################################################################################
    # setDirty()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function setDirtyOkIfClean()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $enum->setDirty();
        $this->assertTrue($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function setDirtyOkIfAlreadyDirty()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertTrue($enum->isDirty());
        $enum->setDirty();
        $this->assertTrue($enum->isDirty());
    }

    ####################################################################################################################
    # setClean()
    ####################################################################################################################

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetCleanOkIfDirty()
    {
        $enum = new NullableEnum();
        $enum->setValue(NullableEnum::FOO);
        $this->assertTrue($enum->isDirty());
        $enum->setClean();
        $this->assertFalse($enum->isDirty());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetCleanOkIfAlreadyClean()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertFalse($enum->isDirty());
        $enum->setClean();
        $this->assertFalse($enum->isDirty());
    }

    ####################################################################################################################
    # __toString()
    ####################################################################################################################

    /**
     * This does currently NOT work, as PhpUnit cannot transform a user triggered fatal error into an Error exception yet.
     */
//    public function testToStringErrorNotSet()
//    {
//        $enum = new NullableEnum();
//        $this->expectException(Error::class);
//        $foo = (string)$enum;
//    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testToStringOkValue()
    {
        $enum = new NullableEnum(NullableEnum::NULL);
        $this->assertEquals('null', (string)$enum);

        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertEquals(sprintf('"%s"', NotNullableEnum::FOO), (string)$enum);

        $enum = new NotNullableEnum(1);
        $this->assertEquals('1', (string)$enum);

        $enum = new NotNullableEnum(true);
        $this->assertEquals('true', (string)$enum);

        $enum = new NotNullableEnum(false);
        $this->assertEquals('false', (string)$enum);
    }

    ####################################################################################################################
    # jsonSerialize()
    ####################################################################################################################

    /**
     *
     */
    public function testJsonSerializeErrorNotSet()
    {
        $enum = new NullableEnum();
        $this->expectException(Error::class);
        json_encode($enum);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonSerializeOkNull()
    {
        $enum = new NullableEnum(null);
        $this->assertEquals(json_encode(null), json_encode($enum));
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonSerializeOkValue()
    {
        $enum = new NotNullableEnum(NotNullableEnum::FOO);
        $this->assertEquals(json_encode(NotNullableEnum::FOO), json_encode($enum));

        $enum = new NotNullableEnum(NotNullableEnum::BAR);
        $this->assertEquals(json_encode(NotNullableEnum::BAR), json_encode($enum));

        $enum = new NotNullableEnum(NotNullableEnum::BLA);
        $this->assertEquals(json_encode(NotNullableEnum::BLA), json_encode($enum));

        $enum = new NotNullableEnum(NotNullableEnum::FASEL);
        $this->assertEquals(json_encode(NotNullableEnum::FASEL), json_encode($enum));
    }

    ####################################################################################################################
    #
    ####################################################################################################################

}
