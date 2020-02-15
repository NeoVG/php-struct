<?php

declare(strict_types=1);

namespace NeoVg\Struct\Helper;

use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\StructProperty\DefaultProperty;

/**
 * Class PropertyTemplates
 */
class PropertyTemplates
{
    /**
     * @var array
     */
    private static $_templates = [];

    /**
     * @param string $className
     *
     * @return DefaultProperty[]|null
     */
    public static function getTemplate(string $className): ?array
    {
        return static::$_templates[static::_normalizeClassName($className)] ?? null;
    }

    /**
     * @param string $className
     * @param array  $properties
     */
    public static function setTemplate(string $className, array $properties): void
    {
        if (isset(static::$_templates[$className])) {
            trigger_error(sprintf('Property template for class %s is already set', $className), E_USER_ERROR);
        }

        static::$_templates[$className] = $properties;
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private static function _normalizeClassName(string $className): string
    {
        if (preg_match('/^Mock_StructAbstract_/', $className)) {
            return StructAbstract::class;
        }

        return $className;
    }
}
