<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

use NeoVg\Struct\StructAbstract;

/**
 * Class StructProperty
 */
class StructProperty extends DefaultProperty
{
    /**
     * @var StructAbstract
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
     * Shortcut for setting this property to be clean.
     *
     * @return $this
     */
    public function setClean(): DefaultProperty
    {
        if ($this->isSet()) {
            $this->_value->clean(false);
            $this->_isDirty = false;
        }

        return $this;
    }
}
