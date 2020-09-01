<?php

declare(strict_types=1);

namespace NeoVg\Struct;

use JsonSerializable;
use NeoVg\Struct\Exception\InvalidValueException;
use NeoVg\Struct\Helper\DebugHelper;

abstract class EnumAbstract implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var bool
     */
    protected $_hasOriginalValue = false;

    /**
     * @var mixed
     */
    protected $_originalValue;

    /**
     * @var bool
     */
    protected $_isSet = false;

    /**
     * @var bool
     */
    protected $_isDirty = false;

    ####################################################################################################################
    # Setup
    ####################################################################################################################

    /**
     * EnumAbstract constructor.
     *
     * @param null $value
     */
    public function __construct($value = null)
    {
        try {
            if (func_num_args()) {
                $this->_value = $this->_originalValue = $this->_checkValue($value);
                $this->_isSet = $this->_hasOriginalValue = true;
            }
        } catch (InvalidValueException $e) {
            trigger_error(sprintf('Invalid initial value %s for %s in %s', $this->_formatValueForError($value), static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }
    }

    ####################################################################################################################
    # Metadata
    ####################################################################################################################

    /**
     * @return bool
     */
    public function hasOriginalValue(): bool
    {
        return $this->_hasOriginalValue;
    }

    /**
     * @return mixed
     */
    public function getOriginalValue()
    {
        if (!$this->hasOriginalValue()) {
            trigger_error(sprintf('Cannot access not existing original value of %s in %s', static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return $this->_originalValue;
    }

    ####################################################################################################################
    # Value
    ####################################################################################################################

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return $this->_isSet;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        try {
            $this->_value = $this->_checkValue($value);
            $this->_isSet = true;
            $this->_isDirty = true;
        } catch (InvalidValueException $e) {
            trigger_error(sprintf('Cannot set invalid value %s for %s in %s', $this->_formatValueForError($value), static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (!$this->isSet()) {
            trigger_error(sprintf('Cannot access unset value of %s in %s', static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return $this->_value;
    }

    /**
     * @return $this
     */
    public function unsetValue(): self
    {
        if (!$this->isSet()) {
            trigger_error(sprintf('Cannot unset already unset value of %s in %s', static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        unset($this->_value, $this->_originalValue);
        $this->_hasOriginalValue = false;
        $this->_isSet = false;
        $this->_isDirty = false;

        return $this;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function _formatValueForError($value): string
    {
        if ($value === null) {
            return 'null';
        } elseif (is_array($value)) {
            return '(array)';
        } elseif (is_object($value)) {
            return '(object)';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            if (strlen($value) > 50) {
                $value = sprintf('%s...', substr($value, 0, 50));
            }
            return sprintf('"%s"', $value);
        } elseif (is_scalar($value)) {
            return $value;
        } else {
            return '(unknown)';
        }
    }

    ####################################################################################################################
    # Dirty State
    ####################################################################################################################

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->_isDirty;
    }

    /**
     * @return $this
     */
    public function setDirty(): self
    {
        if ($this->isSet()) {
            $this->_isDirty = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setClean(): self
    {
        if ($this->isSet()) {
            $this->_originalValue = $this->_value;
            $this->_hasOriginalValue = true;
            $this->_isDirty = false;
        }

        return $this;
    }

    ####################################################################################################################
    # Types
    ####################################################################################################################

    /**
     * @param $value
     *
     * @return bool
     */
    public function isValidValue($value): bool
    {
        try {
            $this->_checkValue($value);

            return true;
        } catch (InvalidValueException $e) {
            return false;
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws InvalidValueException
     */
    protected function _checkValue($value)
    {
        try {
            if (!in_array(
                $value,
                array_values(
                    (new \ReflectionClass($this))
                        ->getConstants()
                ),
                true
            )) {
                throw new InvalidValueException();
            }

            return $value;
        } catch (\ReflectionException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    ####################################################################################################################
    # Type Conversion
    ####################################################################################################################

    /**
     * @return EnumAbstract
     */
    public function toArray()
    {
        return $this->getValue();
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        if (!$this->isSet()) {
            trigger_error(sprintf('Cannot serialize unset value of %s to JSON in %s', static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return $this->_value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->isSet()) {
            trigger_error(sprintf('Cannot cast unset value of %s to string in %s', static::class, DebugHelper::getCaller()), E_USER_ERROR);
        }

        return json_encode($this->_value, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return ['value' => $this->getValue()];
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        $this->_value = $this->_originalValue = $data['value'];
        $this->_isSet = $this->_hasOriginalValue = true;
    }
}
