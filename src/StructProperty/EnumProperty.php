<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

use NeoVg\Struct\EnumAbstract;
use NeoVg\Struct\StructAbstract;

/**
 * Class EnumStructProperty
 */
class EnumProperty extends DefaultProperty
{
    /**
     * @var EnumAbstract
     */
    protected $_value;

    ####################################################################################################################
    # Setup
    ####################################################################################################################

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
        if ($hasDefaultValue && (!is_object($defaultValue) || !is_subclass_of($defaultValue, EnumAbstract::class))) {
            $defaultValue = new $type($defaultValue);
            /** @var EnumAbstract $defaultValue */
            $defaultValue->setDirty();
        }

        parent::__construct($parent, $class, $name, $type, $hasDefaultValue, $defaultValue);
    }

    ####################################################################################################################
    # Metadata
    ####################################################################################################################

    /**
     * @return bool
     */
    public function containsEnum(): bool
    {
        return true;
    }

    ####################################################################################################################
    # Value
    ####################################################################################################################

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
     * @return DefaultProperty
     * @throws \TypeError
     */
    public function setValue($value): DefaultProperty
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

    ####################################################################################################################
    # Dirty State
    ####################################################################################################################

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
     * @return DefaultProperty
     */
    public function setDirty(): DefaultProperty
    {
        if ($this->isSet()) {
            $this->_value->setDirty();
        }

        return $this;
    }

    /**
     * @return DefaultProperty
     */
    public function setClean(): DefaultProperty
    {
        if ($this->isSet()) {
            $this->_value->setClean();
        }

        return $this;
    }
}
