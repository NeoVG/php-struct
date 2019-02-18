<?php

declare(strict_types=1);

namespace NeoVg\Struct;

/**
 * Class StructProperty
 *
 * Represents one property in a Struct.
 */
class StructProperty
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_type;

    /**
     * @var mixed
     */
    private $_value;

    /**
     * @var mixed
     */
    private $_defaultValue;

    /**
     * @var bool
     */
    protected $_isSet = false;

    /**
     * @var bool
     */
    protected $_isDirty = false;

    /**
     * StructProperty constructor.
     *
     * @param string $name
     * @param string $type
     * @param null   $value
     *
     * @throws \TypeError
     */
    public function __construct(string $name, string $type, $value = null)
    {
        $this->_name = $name;
        $this->_type = $this->_normalizeType($type);
        if ($value !== null) {
            $this->setValue($value, true);
        }
    }

    /**
     * Returns the name of the property.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Returns the data type of the property.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Returns whether the type of this property is some known class.
     *
     * @return bool
     */
    public function containsObject(): bool
    {
        return class_exists($this->_type);
    }

    /**
     * Returns whether the type of this property is a Struct.
     *
     * @return bool
     */
    public function containsStruct(): bool
    {
        return $this->containsObject() && is_subclass_of($this->_type, StructAbstract::class);
    }

    /**
     * @param $value
     *
     * @return StructProperty
     */
    protected function _setDefaultValue($value): self
    {
        if (!$this->_isValidType($value)) {
            throw new \TypeError(sprintf('Default value for %s::%s must be of type %s, %s given',
                static::class,
                $this->_name,
                $this->_type,
                gettype($value)
            ));
        }

        $this->_defaultValue = $value;

        return $this->setValue($value)->setClean();
    }

    /**
     * Sets the value of the property.
     *
     * @param mixed $value
     *
     * @return StructProperty
     * @throws \TypeError Thrown if an invalid type was passed.
     */
    public function setValue($value): self
    {
        if (!$this->_isValidType($value)) {
            throw new \TypeError(sprintf('Argument 1 passed to %s::%s() must be of type %s, %s given',
                static::class,
                $this->_name,
                $this->_type,
                gettype($value)
            ));
        }

        $this->_value = $value;
        $this->_isSet = true;

        return $this->setDirty();
    }

    /**
     * Simply returns the value of the property.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Returns whether this property has been set since the structs creation.
     *
     * @return bool
     */
    public function isSet(): bool
    {
        return $this->_isSet;
    }

    /**
     * Returns whether this propery has been set since its creation.
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->_isDirty;
    }

    /**
     * Shortcut for setting this property to be clean.
     *
     * @return StructProperty
     */
    public function setClean(): self
    {
        return $this->setDirty(false);
    }

    /**
     * Indicates that this property has been set since its creation.
     *
     * @param bool $isDirty
     *
     * @return StructProperty
     */
    public function setDirty(bool $isDirty = true): self
    {
        $this->_isDirty = $isDirty;

        return $this;
    }

    /**
     * Normalizes the type read from the phpDoc annotation to be compatible with gettype().
     *
     * @param string $type
     *
     * @return string
     */
    private function _normalizeType(string $type): string
    {
        switch ($type) {
            case 'bool':
                return 'boolean';
            case 'int':
                return 'integer';
            case 'float':
                return 'double';
            default:
                return $type;
        }
    }

    /**
     * Validates a variable against the type of the property.
     *
     * @param mixed $variable
     *
     * @return bool
     */
    private function _isValidType($variable): bool
    {
        if ($variable === null) {
            return true;
        }
        if (gettype($variable) !== 'object') {
            return gettype($variable) === $this->_type;
        } else {
            return $variable instanceof $this->_type;
        }
    }
}
