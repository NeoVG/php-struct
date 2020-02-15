<?php

declare(strict_types=1);

namespace NeoVg\Struct\Helper;

use NeoVg\Struct\EnumAbstract;
use NeoVg\Struct\StructAbstract;
use NeoVg\Struct\StructProperty\ArrayProperty;
use NeoVg\Struct\StructProperty\DefaultProperty;
use NeoVg\Struct\StructProperty\EnumProperty;

/**
 * Class DebugHelper
 */
class DebugHelper
{
    private const IGNORED_CLASSES = [
        self::class,
        StructAbstract::class,
        DefaultProperty::class,
        ArrayProperty::class,
        EnumProperty::class,
        EnumAbstract::class,
    ];

    /**
     * @return string
     */
    public static function getCaller(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        for ($i = 0; $i < count($trace) - 1; $i++) {
            $step = $trace[$i];
            $next = $trace[$i + 1];

            if (!array_key_exists('class', $next)) {
                continue;
            }

            if (in_array($next['class'], static::IGNORED_CLASSES)) {
                continue;
            }

            if ($step['function'] === 'jsonSerialize') {
                continue;
            }

            return sprintf('%s on line %d',
                $step['file'],
                $step['line']
            );
        }

        return '';
    }
}
