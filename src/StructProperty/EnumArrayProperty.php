<?php

declare(strict_types=1);

namespace NeoVg\Struct\StructProperty;

use NeoVg\Struct\EnumAbstract;

/**
 * Class EnumArrayProperty
 */
class EnumArrayProperty extends ArrayProperty
{
    /**
     * @var EnumAbstract[]
     */
    protected $_value;

    ####################################################################################################################
    # Metadata
    ####################################################################################################################

    /**
     * @return bool
     */
    public function containsEnum(): bool
    {
        return true;
    }

    ####################################################################################################################
    # Debug
    ####################################################################################################################

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return parent::__debugInfo() + [
                '[containsEnum]' => true,
            ];
    }
}
