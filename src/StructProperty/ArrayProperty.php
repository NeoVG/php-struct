<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

/**
 * Class ArrayStructProperty
 */
class ArrayProperty extends DefaultProperty
{
    /**
     * @param mixed $value
     *
     * @return DefaultProperty
     * @throws \TypeError
     */
    public function setValue($value): DefaultProperty
    {
        $this->_checkTypes($value);

        $this->_value = $value;
        $this->_isSet = true;
        $this->_isDirty = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function setClean(): DefaultProperty
    {
        $this->_isDirty = false;

        return $this;
    }

    /**
     * @param $values
     *
     * @throws \TypeError
     */
    protected function _checkTypes($values): void
    {
        if ($values === null) {
            return;
        }

        if (!is_array($values)) {
            throw new \TypeError(sprintf('Argument 1 passed to %s::%s() must be of type %s[], %s given',
                static::class,
                $this->_name,
                $this->_type,
                gettype($values)
            ));
        }

        foreach (array_keys($values) as $key) {
            if (!$this->_isOfValidType($values[$key])) {
                throw new \TypeError(sprintf('Key %s in argument 1 passed to %s::%s() must be of type %s, %s given',
                    $key,
                    static::class,
                    $this->_name,
                    $this->_type,
                    gettype($values[$key])
                ));
            }
        }
    }

    /**
     * @param int|string $key
     * @param            $value
     *
     * @return
     * @throws \TypeError
     */
    public function checkValue($key, $value)
    {
        if (!$this->_isOfValidType($value)) {
            throw new \TypeError(sprintf('Key %s in argument 1 passed to %s::%s() must be of type %s, %s given',
                $key,
                static::class,
                $this->_name,
                $this->_type,
                gettype($value)
            ));
        }

        return $value;
    }
}
