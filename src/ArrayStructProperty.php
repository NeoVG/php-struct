<?php

declare(strict_types=1);

namespace NeoVg\Struct;

/**
 * Class ArrayStructProperty
 */
class ArrayStructProperty extends StructProperty
{
    /**
     * @param mixed $value
     *
     * @return StructProperty
     * @throws \TypeError
     */
    public function setValue($value): StructProperty
    {
        $this->_checkTypes($value);

        $this->_value = $value;
        $this->_isSet = true;
        $this->_isDirty = true;

        return $this;
    }

    /**
     * @return StructProperty
     * @throws NotSetException
     */
    public function setClean(): StructProperty
    {
        if ($this->containsStruct() && $this->_value !== null) {
            foreach (array_keys($this->_value) as $key) {
                /** @var StructAbstract $value */
                $value = $this->_value[$key];
                $value->clean(false);
            }
        }

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
     * @throws \TypeError
     */
    public function checkValue($key, $value): void
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
    }
}
