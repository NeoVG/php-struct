<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

use NeoVg\Struct\StructAbstract;

/**
 * Class StructProperty
 *
 * Represents one property in a Struct.
 */
class DefaultProperty
{
    /**
     * @var StructAbstract
     */
    protected $_parent;

    /**
     * @var
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_type;

    /**
     * @var bool
     */
    protected $_containsObject;

    /**
     * @var bool
     */
    protected $_containsStruct;

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var null
     */
    protected $_defaultValue;

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
     * @param StructAbstract $parent
     * @param string         $class
     * @param string         $name
     * @param string         $type
     * @param bool           $hasDefaultValue
     * @param mixed          $defaultValue
     *
     * @throws \TypeError
     */
    public function __construct(?StructAbstract $parent, string $class, string $name, string $type, bool $hasDefaultValue, $defaultValue)
    {
        $this->_parent = $parent;
        $this->_class = $class;
        $this->_name = $name;
        $this->_type = $type;

        if ($hasDefaultValue) {
            $this->_defaultValue = $defaultValue;
            $this->setValue($defaultValue);
        }

        $this->_containsObject = class_exists($type);
        $this->_containsStruct = $this->_containsObject && is_subclass_of($type, StructAbstract::class);
    }

    /**
     * Returns the name of the class where this property was defined.
     * This is especially handy when extending struct classes.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->_class;
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
        return $this->_containsObject;
    }

    /**
     * Returns whether the type of this property is a Struct.
     *
     * @return bool
     */
    public function containsStruct(): bool
    {
        return $this->_containsStruct;
    }

    /**
     * @return bool
     */
    public function containsEnum(): bool
    {
        return false;
    }

    /**
     * Returns true if this property has a default value.
     *
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return isset($this->_defaultValue);
    }

    /**
     * Returns the default value of this property if available.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->_defaultValue;
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
     * Sets the value of the property.
     *
     * @param mixed $value
     *
     * @return DefaultProperty
     * @throws \TypeError
     */
    public function setValue($value): self
    {
        if (!$this->_isOfValidType($value)) {
            throw new \TypeError(
                sprintf('Argument 1 passed to %s::%s() must be of type %s, %s given',
                    static::class,
                    $this->_name,
                    $this->_type,
                    gettype($value)
                )
            );
        }

        $this->_value = $value;
        $this->_isSet = true;
        $this->_isDirty = true;

        return $this;
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
     * Returns whether this propery has been set since its creation.
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->_isDirty;
    }

    /**
     * Indicates that this property has been set since its creation.
     *
     * @return DefaultProperty
     */
    public function setDirty(): self
    {
        if ($this->isSet()) {
            $this->_isDirty = true;
        }

        return $this;
    }

    /**
     * Shortcut for setting this property to be clean.
     *
     * @return DefaultProperty
     */
    public function setClean(): self
    {
        if ($this->isSet()) {
            if ($this->containsStruct()) {
                $this->_value->clean(false);
            }

            $this->_isDirty = false;
        }

        return $this;
    }

    /**
     * Validates a variable against the type of the property.
     *
     * @param mixed $variable
     *
     * @return bool
     */
    protected function _isOfValidType($variable): bool
    {
        if ($variable === null) {
            return true;
        }
        if ($this->_type === 'mixed') {
            return true;
        }
        if ($variable instanceof \Closure) {
            return $this->_type === 'callable';
        } elseif (gettype($variable) === 'object') {
            return $variable instanceof $this->_type;
        } else {
            return gettype($variable) === $this->_type;
        }
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        $properties = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== '_parent') {
                $properties[$key] = $value;
            }
        }

        if (isset($this->_containsObject)) {
            $properties['[containsObject]'] = $this->_containsObject;
        }
        if (isset($this->_containsStruct)) {
            $properties['[containsStruct'] = $this->_containsStruct;
        }

        return $properties;
    }

    /**
     * @return array
     */
    public function debugInfo(): array
    {
        return $this->__debugInfo();
    }
}
