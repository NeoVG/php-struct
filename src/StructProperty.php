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
     * Sets the value of the property.
     *
     * @param mixed $value
     * @param bool $isDefault
     *
     * @throws \TypeError Thrown if an invalid type was passed.
     */
    public function setValue($value, bool $isDefault = false): void
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

        if ($isDefault) {
            $this->_defaultValue = $value;
        }

        $this->_isSet = true;
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
