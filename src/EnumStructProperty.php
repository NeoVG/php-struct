<?php

declare(strict_types=1);

namespace NeoVg\Struct;

use NeoVg\Struct\Test\EnumStruct;

/**
 * Class EnumStructProperty
 */
class EnumStructProperty extends StructProperty
{
    /**
     * @var EnumAbstract
     */
    protected $_value;

    /**
     * EnumStructProperty constructor.
     *
     * @param StructAbstract|null $parent
     * @param string              $class
     * @param string              $name
     * @param string              $type
     * @param bool                $hasDefaultValue
     * @param                     $defaultValue
     *
     * @throws \TypeError
     */
    public function __construct(?StructAbstract $parent, string $class, string $name, string $type, bool $hasDefaultValue, $defaultValue)
    {
        if ($hasDefaultValue && (!is_object($defaultValue) || !is_subclass_of($defaultValue, EnumStruct::class))) {
            /** @var EnumAbstract $defaultValue */
            $defaultValue = new $type($defaultValue);
            $defaultValue->setDirty();
        }

        parent::__construct($parent, $class, $name, $type, $hasDefaultValue, $defaultValue);
    }

    /**
     * @return bool
     */
    public function containsEnum(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        if ($this->_isSet) {
            return $this->_value->isSet();
        }

        return false;
    }

    /**
     * @param mixed $value
     *
     * @return StructProperty
     * @throws \TypeError
     */
    public function setValue($value): StructProperty
    {
        if (!is_object($value) || !($value instanceof EnumAbstract)) {
            $class = $this->_type;

            /** @var EnumAbstract $value */
            $value = new $class($value);
        }

        if ($value->isSet()) {
            $value->setDirty();
        }

        return parent::setValue($value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (!$this->isSet()) {
            $class = $this->_type;

            /** @var EnumAbstract $value */
            $this->_value = new $class();
            $this->_isSet = true;
        }

        return parent::getValue();
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        if ($this->isSet()) {
            return $this->_value->isDirty();
        }

        return false;
    }

    /**
     * @return StructProperty
     */
    public function setDirty(): StructProperty
    {
        if ($this->isSet()) {
            $this->_value->setDirty();
        }

        return $this;
    }

    /**
     * @return StructProperty
     */
    public function setClean(): StructProperty
    {
        if ($this->isSet()) {
            $this->_value->setClean();
        }

        return $this;
    }
}
