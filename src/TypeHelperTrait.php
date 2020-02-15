<?php

declare(strict_types=1);

namespace NeoVg\Struct;

trait TypeHelperTrait
{
    protected static $_internalTypes = [
        'boolean',
        'integer',
        'double',
        'string',
        'array',
        'callable',
        'mixed',
    ];

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
        if (in_array($type, static::$_internalTypes)) {
            return $type;
        }

        # If type is a class including full namespace, check if it exists and if yes, immediately return
        if (class_exists($type) || interface_exists($type)) {
            return $type;
        }

        # Check if type is a class with relative namespace, check if it exists and if yes, immediately return
        $type = sprintf(
            '\%s\%s',
            preg_replace('/\\\?[^\\\]+$/', '', static::class),
            $type
        );
        if (class_exists($type) || interface_exists($type)) {
            return $type;
        }

        # Unknown type, return null
        return null;
    }
}
