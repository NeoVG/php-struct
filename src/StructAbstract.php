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
     * @var StructProperty[]
     */
    protected $_properties = [];

    /**
     * StructAbstract constructor.
     */
    public function __construct()
    {
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

            preg_match_all('/@property(-read)? +([\w\\\]+) +\$(\w+)/', $docCommentString, $matches, PREG_SET_ORDER);

            $properties = [];
            foreach ($matches as $match) {
                $name = $match[3];
                $type = $match[2];
                $value = property_exists(static::class, $name) ? $this->$name : null;

                if (array_filter($properties, function (StructProperty $property) use ($name) {
                    return $property->getName() === $name;
                })) {
                    trigger_error(sprintf('Cannot redefine property $%s', $name), E_USER_NOTICE);

                    continue;
                }

                try {
                    $properties[] = new StructProperty(
                        $name,
                        $type,
                        $value
                    );
                } catch (\TypeError $e) {
                    # Never happens, but in case it happens nevertheless, notify Bugsnag
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
     * @return self
     */
    public static function createFromJson(string $jsonProperties): self
    {
        return self::createFromArray(json_decode($jsonProperties, true));
    }

    /**
     * Returns a struct object with properties set from an array.
     *
     * @param array $arrayProperties
     *
     * @return self
     */
    public static function createFromArray(array $arrayProperties): self
    {
        $class = static::class;
        /** @var StructAbstract $struct */
        $struct = new $class();
        unset($class);

        foreach ($struct->getProperties() as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $arrayProperties)) {
                $value = $arrayProperties[$name];

                if ($value !== null) {
                    if ($property->containsStruct()) {
                        /** @var StructAbstract $class */
                        $class = $property->getType();
                        $value = $class::createFromArray($arrayProperties[$name]);
                        unset($class);
                    } elseif ($property->containsObject()) {
                        $class = $property->getType();
                        if (method_exists($class, 'createFromString')) {
                            $value = $class::createFromString($value);
                        } else {
                            $value = new $class($value);
                        }
                        unset($class);
                    }

                    $struct->$name($value);
                }
                unset($value);
            }
        }

        return $struct;
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
        $property->setValue($args[0] ?? null);

        return $this;
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
            return new StructProperty(
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
     * @return StructAbstract
     */
    public function setDirty(string $name, bool $isDirty): self
    {
        if (!($property = $this->_getProperty($name))) {
            throw new InvalidPropertyException($name);
        }

        $property->setDirty($isDirty);

        return $this;
    }

    /**
     * Returns the dirty-status of a property.
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
     * Marks all properties as not dirty.
     *
     * @return StructAbstract
     */
    public function clean(): self
    {
        foreach ($this->_properties as $property) {
            $property->setClean();
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
                /** @var StructProperty $property */
                $propertiesArray[$property->getName()] = $property->getValue();
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
        foreach ($this->toArray() as $key => $value) {
            $object->$key = $value;
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

        $properties['[isDirty]'] = $this->isDirty();
        $properties['[dirty]'] = array_map(function (StructProperty $property) {
            return $property->getName();
        }, $this->getDirty());

        return $properties;
    }
}
