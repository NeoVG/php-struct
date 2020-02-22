<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

use NeoVg\Struct\StructAbstract;

/**
 * Class StructArrayProperty
 */
class StructArrayProperty extends ArrayProperty
{
    /**
     * @var StructAbstract[]
     */
    protected $_value;

    ####################################################################################################################
    # Metadata
    ####################################################################################################################

    /**
     * @return bool
     */
    public function containsStruct(): bool
    {
        return true;
    }

    ####################################################################################################################
    # Dirty State
    ####################################################################################################################

    /**
     * @return DefaultProperty
     */
    public function setClean(): DefaultProperty
    {
        if ($this->_value !== null) {
            foreach (array_keys($this->_value) as $key) {
                /** @var StructAbstract $value */
                $value = $this->_value[$key];
                $value->clean(false);
            }
        }

        $this->_isDirty = false;

        return $this;
    }
}
