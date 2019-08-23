<?php

namespace Makeable\LaravelCurrencies\Helpers;

trait ValidatesArrays
{
    /**
     * @param $keys
     * @param $array
     *
     * @throws MissingPropertiesException
     */
    public static function requiresProperties($keys, $array)
    {
        $keys = is_array($keys) ? $keys : [$keys];
        $missingKeys = array_keys(array_diff_key(array_flip($keys), $array));

        if (count($missingKeys) > 0) {
            [$one, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            throw new MissingPropertiesException(
                'Missing properties '.implode(', ', $missingKeys).' for function '.$caller['function']
            );
        }
    }
}
