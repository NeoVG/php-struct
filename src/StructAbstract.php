<?php

declare(strict_types=1);

namespace NeoVg\Struct;

use JsonSerializable;
use NeoVg\Struct\Helper\DebugHelper;
use NeoVg\Struct\Helper\PropertyTemplates;
use NeoVg\Struct\Helper\TypeHelperTrait;
use NeoVg\Struct\StructProperty\ArrayProperty;
use NeoVg\Struct\StructProperty\DefaultProperty;
use NeoVg\Struct\StructProperty\EnumProperty;
use NeoVg\Struct\StructProperty\StructArrayProperty;
use NeoVg\Struct\StructProperty\StructProperty;

/**
 * Class Struct
 *
 * This is very useful when you want to return structured data from a function.
 * Just extend this class and add phpDoc annotations.
 * See TestStruct for an example.
 */
abstract class StructAbstract implements JsonSerializable
{
    use TypeHelperTrait;

    /**
     * @var StructAbstract
     */
    protected $_parent;

    /**
     * @var string
     */
    protected $_nameInParent;

    /**
     * @var DefaultProperty[]
     */
    protected $_properties = [];

    /**
     * StructAbstract constructor.
     *
     * @param StructAbstract|null $parent
     * @param string|null         $nameInParent
     */
    public function __construct(?StructAbstract $parent = null, ?string $nameInParent = null)
    {
        $this->_parent = $parent;
        $this->_nameInParent = $nameInParent;

        if (static::class === self::class || preg_match('/^Mock_StructAbstract_/', static::class)) {
            return;
        }

        $this->_buildProperties();
    }

    /**
     * Reads phpDoc annotations of implementing class and builds internal property structure according to them.
     */
    protected function _buildProperties(): void
    {
        if (!($properties = PropertyTemplates::getTemplate(static::class))) {
            try {
                $docCommentString = (new \ReflectionClass(static::class))->getDocComment();
                $docCommentString = preg_replace('/^(.*)$/m', static::class . ' \\1', $docCommentString);

                while (($tmpClass ?? static::class) !== self::class) {
                    $tmpClass = get_parent_class($tmpClass ?? static::class);
                    $tmpDocCommentString = (new \ReflectionClass($tmpClass))->getDocComment();
                    $tmpDocCommentString = preg_replace('/^(.*)$/m', $tmpClass . ' \\1', $tmpDocCommentString);
                    $docCommentString = $tmpDocCommentString . PHP_EOL . $docCommentString;
                }

                preg_match_all('/(.+?)\s+\*\s+@property(-read)? +([\w\\\]+)(\[\])? +\$(\w+)/', $docCommentString, $matches, PREG_SET_ORDER);

                $properties = [];
                foreach ($matches as $match) {
                    $source = $match[1];
                    $name = $match[5];
                    $type = $match[3];
                    $isArray = $match[4];

                    # If a class property $name exists, its value is the default value for this struct property.
                    $hasDefaultValue = property_exists(static::class, $name);
                    $defaultValue = $hasDefaultValue ? $this->$name : null;

                    # Check if $type is any known internal datatype or class name.
                    if (!($realType = $this->_normalizeType($type))) {
                        trigger_error(sprintf('Cannot parse definition for %s::%s, %s is no valid internal datatype and no known class name.', static::class, $name, $type), E_USER_ERROR);
                    }
                    $type = $realType;

                    $isStruct = class_exists($type) && is_subclass_of($type, StructAbstract::class);
                    $isEnum = class_exists($type) && is_subclass_of($type, EnumAbstract::class);

                    # Check if a property with the same name has already been defined and if we are allowed to redefine it.
                    if ($existingProperty = array_values(
                            array_filter(
                                $properties,
                                function (DefaultProperty $property) use ($name) {
                                    return $property->getName() === $name;
                                }
                            )
                        )[0] ?? null
                    ) {
                        /** @var DefaultProperty $existingProperty */
                        if (
                            !class_exists($type)
                            || !class_exists($existingProperty->getType())
                            || !is_subclass_of($type, $existingProperty->getType())
                        ) {
                            trigger_error(sprintf('Cannot redefine property %s%s $%s in class %s, was already defined as %s in class %s.', $type, $isArray, $name, $source, $existingProperty->getType(), $existingProperty->getClass()), E_USER_ERROR);

                            continue;
                        }
                    }

                    # Arrays of enums are not supported yet
                    if ($isArray && $isEnum) {
                        trigger_error(sprintf('Incorrect definition of property %s%s $%s in class %s, arrays of enums are not suppoerted yet.', $type, $isArray, $name, $source), E_USER_ERROR);
                    }

                    # Might throw a TypeError if the default $value has the wrong type
                    try {
                        if ($isArray && $isStruct) {
                            $properties[$name] = new StructArrayProperty($this, $source, $name, $type, $hasDefaultValue, $defaultValue);
                        } elseif ($isArray) {
                            $properties[$name] = new ArrayProperty($this, $source, $name, $type, $hasDefaultValue, $defaultValue);
                        } elseif ($isStruct) {
                            $properties[$name] = new StructProperty($this, $source, $name, $type, $hasDefaultValue, $defaultValue);
                        } elseif ($isEnum) {
                            $properties[$name] = new EnumProperty($this, $source, $name, $type, $hasDefaultValue, $defaultValue);
                        } else {
                            $properties[$name] = new DefaultProperty($this, $source, $name, $type, $hasDefaultValue, $defaultValue);
                        }
                    } catch (\TypeError $e) {
                        trigger_error(sprintf('Default value for property %s $%s in class %s is of invalid type %s', $type, $name, $source, gettype($defaultValue)), E_USER_ERROR);
                    }
                }

                PropertyTemplates::setTemplate(static::class, $properties);
            } catch (\ReflectionException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        $this->_properties = array_map(
            function (DefaultProperty $property) {
                return clone $property;
            },
            $properties
        );
    }

    /**
     * Returns a struct object with properties set from an JSON array.
     *
     * @param string|null $jsonProperties
     *
     * @return static
     * @throws \JsonException
     */
    public static function createFromJson(?string $jsonProperties): self
    {
        if ($jsonProperties === null) {
            throw new \JsonException('Null input');
        }

        if ($jsonProperties === '') {
            throw new \JsonException('Empty input');
        }

        $arrayProperties = json_decode($jsonProperties, true, JSON_THROW_ON_ERROR);

        if (!is_array($arrayProperties)) {
            throw new \JsonException('Decoded input is not an array');
        }

        return static::createFromArray($arrayProperties);
    }

    /**
     * Same as createFromJson(), but does return null instead of throwing an exception on error.
     *
     * @param string|null $jsonProperties
     *
     * @return static|null
     */
    public static function createFromJsonNullOnError(?string $jsonProperties): ?self
    {
        try {
            return static::createFromJson($jsonProperties);
        } catch (\JsonException $e) {
            return null;
        }
    }

    /**
     * Returns a struct object with properties set from an array.
     *
     * @param array               $arrayProperties
     * @param StructAbstract|null $parent
     * @param string|null         $nameInParent
     *
     * @return static
     */
    public static function createFromArray(array $arrayProperties, ?StructAbstract $parent = null, ?string $nameInParent = null): self
    {
        $class = static::class;
        /** @var StructAbstract $struct */
        $struct = new $class($parent, $nameInParent);
        unset($class);

        foreach ($struct->getProperties() as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $arrayProperties)) {
                $value = $arrayProperties[$name];

                if ($value !== null) {
                    if ($property->containsObject()) {
                        $class = $property->getType();

                        if ($property->containsStruct()) {
                            /** @var StructAbstract $class */

                            if ($property instanceof ArrayProperty) {
                                foreach (array_keys($value) as $key) {
                                    if (is_array($value[$key])) {
                                        $value[$key] = $class::createFromArray($value[$key], $struct, $name);
                                    }

                                    try {
                                        $value[$key] = $property->checkValue($key, $value[$key]);
                                    } catch (\TypeError $e) {
                                        trigger_error(sprintf('%s in %s', $e->getMessage(), DebugHelper::getCaller()), E_USER_ERROR);
                                    }
                                }
                            } else {
                                $value = $class::createFromArray($value, $struct, $name);
                            }
                        } elseif ($property->containsEnum()) {
                            /** @var EnumAbstract $class */
                            /** @var EnumProperty $value */
                            $value = new $class($value);
                            $value->setDirty();
                        } elseif (is_array($value) && method_exists($class, 'createFromArray')) {
                            $value = $class::createFromArray($value);
                        } elseif (is_string($value) && method_exists($class, 'createFromString')) {
                            $value = $class::createFromString($value);
                        }

                        unset($class);
                    }
                } else {
                    if ($property->containsEnum()) {
                        /** @var EnumAbstract $class */
                        $class = $property->getType();
                        /** @var EnumProperty $value */
                        $value = new $class($value);
                        $value->setDirty();
                    }
                }

                $struct->$name($value);

                unset($value);
            }
        }

        return $struct;
    }

    /**
     * Magic isset for the properties of the struct.
     * Throws a notice if you try to access a non-existing property.
     *
     * @param string $name
     *
     * @return bool|void
     */
    public final function __isset(string $name)
    {
        return $this->getProperty($name)->isSet();
    }

    /**
     * Returns whether a property value has been set since instanciating this object.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isSet(string $name): bool
    {
        return $this->getProperty($name)->isSet();
    }

    /**
     * Magic setter for the properties of the struct.
     * Throws a notice if you try to access a non-existing property.
     *
     *
     * @param string $name
     * @param        $value
     */
    public final function __set(string $name, $value): void
    {
        try {
            $this->getProperty($name)->setValue($value);
        } catch (\TypeError $e) {
            trigger_error(sprintf('%s in %s', $e->getMessage(), DebugHelper::getCaller()), E_USER_ERROR);
        }

        $this->_setDirtyStateInParent();
    }

    /**
     * Fluent setter for the properties of the struct.
     * Returns $this so calls can be chained.
     *
     * @param string $name Name of the property to be set.
     * @param array  $args Will always only have one element containing the value to be put into the property.
     *
     * @return static
     */
    public final function __call(string $name, array $args): StructAbstract
    {
        $normalizedName = $name;
        if (preg_match('/^with[A-Z]/', $name)) {
            $normalizedName = sprintf(
                '%s%s',
                strtolower(substr($name, 4, 1)),
                substr($name, 5)
            );
        }

        if (!($property = $this->_getProperty($normalizedName))) {
            trigger_error(sprintf('Call to undefined method %s::%s() in %s', static::class, $name, DebugHelper::getCaller()), E_USER_ERROR);
        }
        if (!array_key_exists(0, $args)) {
            trigger_error(sprintf('%s::%s() expects exactly 1 parameter, 0 given in %s', static::class, $name, DebugHelper::getCaller()), E_USER_ERROR);
        }
        try {
            $property->setValue($args[0]);
        } catch (\TypeError $e) {
            trigger_error(sprintf('%s in %s', $e->getMessage(), DebugHelper::getCaller()), E_USER_ERROR);
        }

        $this->_setDirtyStateInParent();

        return $this;
    }

    /**
     * Magic getter for the properties of the struct.
     * Throws a notice if you try to access a non-existing property.
     *
     * @param string $name Name of the property to read.
     *
     * @return mixed
     */
    public final function __get(string $name)
    {
        return $this->getProperty($name)->getValue();
    }

    /**
     * Returns a property object.
     *
     * @param string $name
     *
     * @return DefaultProperty|null
     */
    protected function _getProperty(string $name): ?DefaultProperty
    {
        return $this->_properties[$name] ?? null;
    }

    /**
     * Returns whether this struct has a property with a certain name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return $this->_getProperty($name) !== null;
    }

    /**
     * Returns the property object for a certain name.
     *
     * @param string $name
     *
     * @return DefaultProperty
     */
    public function getProperty(string $name): DefaultProperty
    {
        if (!$this->hasProperty($name)) {
            trigger_error(sprintf('Undefined property: %s::$%s in %s', static::class, $name, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return $this->_getProperty($name);
    }

    /**
     * Returns an array of all property objects.
     *
     * @return DefaultProperty[]
     */
    public function getProperties(): array
    {
        return $this->_properties ?? [];
    }

    /**
     * Returns a list of all set properties.
     *
     * @return DefaultProperty[]
     */
    public function getSetProperties(): array
    {
        return array_values(
            array_filter($this->_properties ?? [], function (DefaultProperty $property) {
                return $property->isSet();
            })
        );
    }

    /**
     * @deprecated Use getSetProperties() instead
     *
     * @return array
     */
    public function getSet(): array
    {
        return $this->getSetProperties();
    }

    /**
     * Returns a list of all dirty properties.
     *
     * @return DefaultProperty[]
     */
    public function getDirtyProperties(): array
    {
        return array_values(
            array_filter($this->getProperties(), function (DefaultProperty $property) {
                return $property->isDirty();
            })
        );
    }

    /**
     * @deprecated Use getDirtyProperties() instead
     *
     * @return array
     */
    public function getDirty(): array
    {
        return $this->getDirtyProperties();
    }

    /**
     * Returns a new object containing only dirty properties. Useful for sending changes to APIs.
     *
     * @return StructAbstract
     */
    public function withDirtyPropertiesOnly(): self
    {
        $dirtyProperties = array_filter($this->_properties, function (DefaultProperty $property) {
            return $property->isDirty();
        });
        $dirtyPropertiesArray = [];
        foreach ($dirtyProperties as $property) {
            if ($property->containsStruct() && $property->getValue() !== null) {
                $dirtyPropertiesArray[$property->getName()] = $property->getValue()->withDirtyPropertiesOnly()->toArray();
            } else {
                $dirtyPropertiesArray[$property->getName()] = $property->getValue();
            }
        }

        return static::createFromArray($dirtyPropertiesArray);
    }

    /**
     * Returns whether any of this structs properties has been changed since the objects initialisation.
     *
     * @param string|null $name
     *
     * @return bool
     */
    public function isDirty(string $name = null): bool
    {
        if ($name === null) {
            foreach (($this->_properties ?? []) as $property) {
                if ($property->isDirty()) {
                    return true;
                }
            }

            return false;
        }

        return $this->getProperty($name)->isDirty();
    }

    /**
     * If this struct itself is a property in another struct, then its dirty-state there needs to be updated if the dirty-state of one of its properties changes.
     */
    protected function _setDirtyStateInParent(): void
    {
        if ($this->_parent !== null) {
            if ($this->isDirty() && !$this->_parent->isDirty($this->_nameInParent)) {
                $this->_parent->setDirty($this->_nameInParent);
            } elseif (!$this->isDirty() && $this->_parent->isDirty($this->_nameInParent)) {
                $this->_parent->setClean($this->_nameInParent);
            }
        }
    }

    /**
     * Sets the dirty-status of a property.
     *
     * @param string $name
     *
     * @return static
     */
    public function setDirty(string $name): self
    {
        $this->getProperty($name)->setDirty();

        $this->_setDirtyStateInParent();

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setClean(string $name): self
    {
        $this->getProperty($name)->setClean();

        $this->_setDirtyStateInParent();

        return $this;
    }

    /**
     * Marks all properties as not dirty.
     *
     * @param bool $cascade
     *
     * @return $this
     */
    public function clean(bool $cascade = true): self
    {
        foreach ($this->getDirtyProperties() as $property) {
            $property->setClean();
        }

        if ($cascade) {
            $this->_setDirtyStateInParent();
        }

        return $this;
    }

    /**
     * Returns the Structs properties as associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $propertiesArray = [];
        foreach ($this->_properties as $property) {
            if ($property->isSet()) {
                /** @var DefaultProperty $property */
                $value = $property->getValue();

                if (is_array($value)) {
                    $value = array_map(function ($item) {
                        if (is_object($item) && method_exists($item, 'toArray')) {
                            return $item->toArray();
                        }

                        return $item;
                    }, $value);
                } elseif (is_object($value) && method_exists($value, 'toArray')) {
                    $value = $value->toArray();
                }

                $propertiesArray[$property->getName()] = $value;
            }
        }

        return $propertiesArray;
    }

    /**
     * Specifies data which should be serialized to JSON.
     *
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $object = new \stdClass();
        foreach ($this->_properties as $property) {
            if ($property->isSet()) {
                $key = $property->getName();
                $object->$key = $property->getValue();
            }
        }

        return $object;
    }

    /**
     * Returns a JSON string representation of $this.
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
    }

    /**
     * Returns an array with the virtual property structure and values for beautified output in var_dump() or Xdebug.
     *
     * PLEASE NOTE: This does ONLY work with Xdebug up to version 2.7.1! Newer versions will dump the actual object structure instead.
     * You can downgrade Xdebug using `pecl uninstall xdebug && pecl install xdebug-2.7.1` or just use the output of StructAbstract::debugInfo().
     *
     * @return array
     */
    public function __debugInfo()
    {
        $properties = [];
        $propertyMetaDatas = [];

        foreach (($this->_properties ?? []) as $property) {
            /** @var DefaultProperty $property */
            $properties[$property->getName()] = $property->getValue();
            $propertyMetaDatas[$property->getName()] = $property->__debugInfo();
        }

        $properties += [
            '[_isDirty]'        => $this->isDirty(),
            '[properties]'      => $propertyMetaDatas,
            '[setProperties]'   => array_map(function (DefaultProperty $property) {
                return $property->getName();
            }, $this->getSetProperties()),
            '[dirtyProperties]' => array_map(function (DefaultProperty $property) {
                return $property->getName();
            }, $this->getDirtyProperties()),
        ];

        return $properties;
    }

    /**
     * Returns an array with the virtual property structure and values for debugging purposes.
     *
     * @return array
     */
    public function debugInfo(): array
    {
        $properties = [];
        $propertyMetaDatas = [];

        foreach ($this->_properties as $property) {
            if ($property instanceof ArrayProperty && $property->isSet()) {
                $properties[$property->getName()] = array_map(
                    function ($subValue) {
                        if (is_object($subValue) && $subValue instanceof StructAbstract) {
                            return $subValue->debugInfo();
                        }

                        return $subValue;
                    },
                    $property->getValue()
                );
            } else {
                /** @var DefaultProperty $property */
                $properties[$property->getName()] = (is_object($property->getValue()) && $property->getValue() instanceof StructAbstract)
                    ? $property->getValue()->debugInfo()
                    : $property->getValue();
            }

            $propertyMetaDatas[$property->getName()] = $property->__debugInfo();
        }

        $properties += [
            '[_isDirty]'        => $this->isDirty(),
            '[properties]'      => $propertyMetaDatas,
            '[setProperties]'   => array_map(function (DefaultProperty $property) {
                return $property->getName();
            }, $this->getSetProperties()),
            '[dirtyProperties]' => array_map(function (DefaultProperty $property) {
                return $property->getName();
            }, $this->getDirtyProperties()),
        ];

        return $properties;
    }
}
