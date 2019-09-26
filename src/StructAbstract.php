<?php

declare(strict_types=1);

namespace NeoVg\Struct;

use JsonSerializable;

/**
 * Class Struct
 *
 * This is very useful when you want to return structured data from a function.
 * Just extend this class and add phpDoc annotations.
 * See TestStruct for an example.
 */
abstract class StructAbstract implements JsonSerializable
{
    /**
     * @var StructAbstract
     */
    protected $_parent;

    /**
     * @var string
     */
    protected $_nameInParent;

    /**
     * @var StructProperty[]
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

        $this->_buildProperties();
    }

    /**
     * Reads phpDoc annotations of implementing class and builds internal property structure according to them.
     */
    protected function _buildProperties(): void
    {
        try {
            $class = static::class;
            $docCommentString = (new \ReflectionClass($class))->getDocComment() . '';
            while ($class !== self::class) {
                $class = get_parent_class($class);
                $docCommentString = (new \ReflectionClass($class))->getDocComment() . PHP_EOL . $docCommentString;
            }

            preg_match_all('/@property(-read)? +([\w\\\]+)(\[\])? +\$(\w+)/', $docCommentString, $matches, PREG_SET_ORDER);

            $properties = [];
            foreach ($matches as $match) {
                $name = $match[4];
                $type = $match[2];
                $isArray = $match[3];
                $value = property_exists(static::class, $name) ? $this->$name : null;

                if (array_filter($properties, function (StructProperty $property) use ($name) {
                    return $property->getName() === $name;
                })) {
                    trigger_error(sprintf('Cannot redefine property $%s', $name), E_USER_NOTICE);

                    continue;
                }

                # Might throw a TypeError if the default $value has the wrong type
                if ($isArray) {
                    $properties[] = new ArrayStructProperty(
                        $name,
                        $type,
                        $value
                    );
                } else {
                    $properties[] = new StructProperty(
                        $name,
                        $type,
                        $value
                    );
                }
            }
            $this->_properties = $properties;
        } catch (\ReflectionException $e) {
            # Never happens, but in case it happens nevertheless, notify Bugsnag
        }
    }

    /**
     * Returns a struct object with properties set from an JSON array.
     *
     * @param string $jsonProperties
     *
     * @return static
     */
    public static function createFromJson(string $jsonProperties): self
    {
        return self::createFromArray(json_decode($jsonProperties, true));
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

                            if ($property instanceof ArrayStructProperty) {
                                foreach (array_keys($value) as $key) {
                                    if (is_array($value[$key])) {
                                        $value[$key] = $class::createFromArray($value[$key], $struct, $name);
                                    }

                                    $property->checkValue($key, $value[$key]);
                                }
                            } else {
                                $value = $class::createFromArray($value, $struct, $name);
                            }
                        } elseif (is_string($value) && method_exists($class, 'createFromString')) {
                            $value = $class::createFromString($value);
                        }

                        unset($class);
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
        if (!($property = $this->_getProperty($name))) {
            trigger_error(sprintf('Undefined property: %s::$%s', static::class, $name), E_USER_NOTICE);

            return;
        }

        return $property->isSet();
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
        if (!($property = $this->_getProperty($name))) {
            trigger_error(sprintf('Undefined property: %s::$%s', static::class, $name), E_USER_NOTICE);

            return null;
        }

        return $property->getValue();
    }

    /**
     * Magic setter for the properties of the struct.
     * Throws a notice if you try to access a non-existing property.
     *
     *
     * @param string $name
     * @param        $value
     *
     * @throws \TypeError Thrown if invalid type was put into property.
     */
    public final function __set(string $name, $value): void
    {
        if (!($property = $this->_getProperty($name))) {
            trigger_error(sprintf('Undefined property: %s::$%s', static::class, $name), E_USER_NOTICE);

            return;
        }

        $property->setValue($value);

        $this->_setDirtyStateInParent();
    }

    /**
     * Fluent setter for the properties of the struct.
     * Returns $this so calls can be chained.
     *
     * @param string $name Name of the property to be set.
     * @param array  $args Will always only have one element containing the value to be put into the property.
     *
     * @return $this
     * @throws \BadMethodCallException Thrown if non existent property was accessed.
     * @throws \ArgumentCountError Thrown if value to set is not passed.
     * @throws \TypeError Thrown if invalid type was put into property.
     */
    public final function __call(string $name, array $args): StructAbstract
    {
        if (!($property = $this->_getProperty($name))) {
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
        }
        if (!array_key_exists(0, $args)) {
            throw new \ArgumentCountError(sprintf('%s::%s() expects exactly 1 parameter, 0 given', static::class, $name));
        }
        $property->setValue($args[0]);

        $this->_setDirtyStateInParent();

        return $this;
    }

    /**
     * If this struct itself is a property in another struct, then its dirty-state there needs to be updated if the dirty-state of one of its properties changes.
     */
    protected function _setDirtyStateInParent(): void
    {
        if ($this->_parent !== null) {
            $this->_parent->setDirty($this->_nameInParent, $this->isDirty());
        }
    }

    /**
     * Returns a property object.
     *
     * @param string $name
     *
     * @return StructProperty|null
     */
    protected function _getProperty(string $name): ?StructProperty
    {
        foreach ($this->_properties as $property) {
            /** @var StructProperty $property */
            if ($property->getName() === $name) {
                return $property;
            }
        }

        return null;
    }

    /**
     * Returns an array of all property objects.
     *
     * @return StructProperty[]
     */
    public function getProperties(): array
    {
        return array_map(function (StructProperty $property) {
            $class = get_class($property);

            return new $class(
                $property->getName(),
                $property->getType()
            );
        }, $this->_properties);
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
        if (!($property = $this->_getProperty($name))) {
            throw new InvalidPropertyException($name);
        }

        return $property->isSet();
    }

    /**
     * Returns a list of all set properties.
     *
     * @return StructProperty[]
     */
    public function getSet(): array
    {
        return array_values(
            array_filter($this->_properties, function (StructProperty $property) {
                return $property->isSet();
            })
        );
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
            foreach ($this->_properties as $property) {
                if ($property->isDirty()) {
                    return true;
                }
            }

            return false;
        }

        if (!($property = $this->_getProperty($name))) {
            throw new InvalidPropertyException($name);
        }

        return $property->isDirty();
    }

    /**
     * Sets the dirty-status of a property.
     *
     * @param string $name
     * @param bool   $isDirty
     *
     * @return $this
     */
    public function setDirty(string $name, bool $isDirty): self
    {
        if (!($property = $this->_getProperty($name))) {
            throw new InvalidPropertyException($name);
        }

        $property->setDirty($isDirty);

        $this->_setDirtyStateInParent();

        return $this;
    }

    /**
     * Returns a list of all dirty properties.
     *
     * @return StructProperty[]
     */
    public function getDirty(): array
    {
        return array_values(
            array_filter($this->_properties, function (StructProperty $property) {
                return $property->isDirty();
            })
        );
    }

    /**
     * Returns a new object containing only dirty properties. Useful for sending changes to APIs.
     *
     * @return StructAbstract
     */
    public function withDirtyPropertiesOnly(): self
    {
        $dirtyProperties = array_filter($this->_properties, function (StructProperty $property) {
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
     * Marks all properties as not dirty.
     *
     * @return $this
     */
    public function clean(): self
    {
        foreach ($this->_properties as $property) {
            $property->setClean();
        }

        $this->_setDirtyStateInParent();

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
                /** @var StructProperty $property */
                $value = $property->getValue();

                if (is_object($value) && method_exists($value, 'toArray')) {
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
        return json_encode($this, JSON_PRESERVE_ZERO_FRACTION);
    }

    /**
     * Returns an array with the virtual property structure and values for beautified output in print_r() or Xdebug.
     *
     * @return array
     */
    public function __debugInfo()
    {
        $properties = [];

        foreach ($this->_properties as $property) {
            /** @var StructProperty $property */
            $properties[$property->getName()] = $property->getValue();
        }

        $properties += [
            '[properties]'          => $this->_properties,
            '[setProperties]'       => array_map(function (StructProperty $property) {
                return $property->getName();
            }, $this->getSet()),
            '[isDirty]'             => $this->isDirty(),
            '[dirtyProperties]'     => array_map(function (StructProperty $property) {
                return $property->getName();
            }, $this->getDirty()),
            '[_parentStruct]'       => $this->_parent,
            '[_nameInParentStruct]' => $this->_nameInParent,
        ];

        return $properties;
    }
}
