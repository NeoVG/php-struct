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
    protected const INTERNAL_TYPES = [
        'boolean',
        'integer',
        'double',
        'string',
        'array',
        'callable',
    ];

    /**
     * @var StructAbstract
     */
    protected $_parent;

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
     * @var mixed
     */
    protected $_originalValue = null;

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
     * @param string         $name
     * @param string         $type
     * @param mixed          $defaultValue
     */
    public function __construct(?StructAbstract $parent, string $name, string $type, $defaultValue)
    {
        $this->_parent = $parent;
        $this->_name = $name;

        if (!($this->_type = $this->_normalizeType($type))) {
            throw new UnknownTypeError(sprintf('Cannot parse data definition for %s::%s, %s is no valid internal datatype and no known class name.',
                get_class($this->_parent),
                $name,
                $type
            ));
        }

        $this->_type = $this->_normalizeType($type);

        if ($defaultValue !== null) {
            $this->_defaultValue = $defaultValue;
            $this->setValue($defaultValue);
        }

        $this->_containsObject = !in_array($this->_type, static::INTERNAL_TYPES);
        $this->_containsStruct = $this->_containsObject && is_subclass_of($this->_type, StructAbstract::class);
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
     * Sets the value of the property.
     *
     * @param mixed $value
     *
     * @return StructProperty
     * @throws \TypeError Thrown if an invalid type was passed.
     */
    public function setValue($value): self
    {
        if (!$this->_isOfValidType($value)) {
            throw new \TypeError(sprintf('Argument 1 passed to %s::%s() must be of type %s, %s given',
                static::class,
                $this->_name,
                $this->_type,
                gettype($value)
            ));
        }

        $this->_value = $value;

        if (!$this->_isSet) {
            $this->_isSet = true;

            return $this->setDirty(true);
        }

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
        if ($this->containsStruct() && $this->_value !== null) {
            $this->_value->clean();
        }

        return $this->setDirty(false);
    }

    /**
     * Indicates that this property has been set since its creation.
     *
     * @param bool $isDirty
     *
     * @return StructProperty
     */
    public function setDirty(bool $isDirty = null): self
    {
        if ($isDirty === null) {
            $isDirty = $this->_value !== $this->_originalValue;
        } elseif ($isDirty === false) {
            $this->_originalValue = $this->_value;
        }

        $this->_isDirty = $isDirty;

        return $this;
    }

    /**
     * Normalizes the type read from the phpDoc annotation to be compatible with gettype().
     *
     * @param string $type
     *
     * @return string|null
     */
    protected function _normalizeType(string $type): ?string
    {
        # Normalize alternative spellings for internal types
        switch ($type) {
            case 'bool':
                $type = 'boolean';
                break;
            case 'int':
                $type = 'integer';
                break;
            case 'float':
                $type = 'double';
                break;
        }

        # If type is internal, immediately return
        if (in_array($type, static::INTERNAL_TYPES)) {
            return $type;
        }

        # If type is a class including full namespace, check if it exists and if yes, immediately return
        if (class_exists($type)) {
            return $type;
        }

        # Check if type is a class with relative namespace, check if it exists and if yes, immediately return
        $type = sprintf(
            '\%s\%s',
            preg_replace('/\\\?[^\\\]+$/', '', get_class($this->_parent)),
            $type
        );
        if (class_exists($type)) {
            return $type;
        }

        # Unknown type, return null
        return null;
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
            $properties[$key] = $value;
        }

        $properties += [
            '[containsObject]' => $this->containsObject(),
            '[containsStruct]' => $this->containsStruct(),
        ];

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
