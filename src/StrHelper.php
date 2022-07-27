<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Popular string helpers
 *
 * @author mendel
 */
class StrHelper
{
    /**
     * Check if string contain $needle
     * Old name: haveStr
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitivity
     * @return bool
     */
    public static function contain(string $haystack, string $needle, bool $caseSensitivity = true): bool
    {
        if (!$caseSensitivity) {
            $haystack = strtolower($haystack);
            $needle = strtolower($needle);
        }
        return (strpos($haystack, $needle) !== false);
    }

    /**
     * Check if string start from $needle
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitivity
     * @return bool
     */
    public static function start(string $haystack, string $needle, bool $caseSensitivity = true): bool
    {
        if (!$caseSensitivity) {
            $haystack = strtolower($haystack);
            $needle = strtolower($needle);
        }
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Check if string end by $needle
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitivity
     * @return bool
     */
    public static function end(string $haystack, string $needle, bool $caseSensitivity = true): bool
    {
        if (!$caseSensitivity) {
            $haystack = strtolower($haystack);
            $needle = strtolower($needle);
        }
        $length = strlen($needle);
        return ($length === 0) || (substr($haystack, -$length) === $needle);
    }

    /**
     * SQL Like operator in PHP.
     * Returns TRUE if match else FALSE.
     * @param string $subject
     * @param string $pattern
     * @param bool $caseSensitivity
     * @return bool
     */
    public static function like(string $subject, string $pattern, bool $caseSensitivity = true): bool
    {
        if (!$caseSensitivity) {
            $subject = strtolower($subject);
            $pattern = strtolower($pattern);
        }
        $preparedPattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match("/^{$preparedPattern}$/", $subject);
    }

    /**
     * Remove prefix from string if string start from prefix
     * @param string $str
     * @param string $prefix
     * @return string
     */
    public static function removePrefix(string $str, string $prefix): string
    {
        $length = strlen($prefix);
        if (substr($str, 0, $length) === $prefix) { // if start from prefix
            return substr($str, $length);
        } else {
            return $str;
        }
    }

    /**
     * Find prefix if exist. If no - return default.
     * Return array - first value is prefix, second == after prefix
     * @param string $str
     * @param array $prefixes
     * @param string|null $default
     * @return array
     */
    public static function findPrefix(string $str, array $prefixes, ?string $default = null): array
    {
        // @2DO: sort prefixes list by lenght and refactor constants at compare helper
        foreach ($prefixes as $prefix) {
            if (static::start($str, $prefix, false)) {
                return [$prefix, static::removePrefix($str, $prefix)];
            }
        }
        return [$default, $str];
    }

    /**
     * Convert string to int
     * Can be used for short nicknames etc
     * return null if too big number
     *
     * @param string $str
     * @return int|null
     */
    public static function str2int(string $str): ?int
    {
        $low = strtolower($str);
        if (preg_match('/^[a-z0-1]+$/', $low) != 1) {
            return null;
        }
        $hex = base_convert($low, 36, 16);
        $i = hexdec($hex);
        if ($i > PHP_INT_MAX) {
            return null;
        }
        return $i;
    }

    /**
     * Convert integer to string
     * @see str2int
     *
     * @param int $i
     * @return string
     */
    public static function int2str(int $i): string
    {
        return base_convert(dechex($i), 16, 36);
    }

    /**
     * Transform string from camel case format to underscore
     * @param string $key
     * @return string
     */
    public static function camelCase2underscore(string $key): string
    {
        $str = lcfirst($key);
        return strtr($str, [
            'A' => '_a', 'B' => '_b', 'C' => '_c', 'D' => '_d',
            'E' => '_e', 'F' => '_f', 'G' => '_g', 'H' => '_h',
            'I' => '_i', 'J' => '_j', 'K' => '_k', 'L' => '_l',
            'M' => '_m', 'N' => '_n', 'O' => '_o', 'P' => '_p',
            'Q' => '_q', 'R' => '_r', 'S' => '_s', 'T' => '_t',
            'U' => '_u', 'V' => '_v', 'W' => '_w', 'X' => '_x',
            'Y' => '_y', 'Z' => '_z',
        ]);
    }

    /**
     * Transform underscore format, to camel case
     * @param string $key
     * @return string
     */
    public static function underscore2camelCase(string $key): string
    {
        return strtr($key, array_flip([
            'A' => '_a', 'B' => '_b', 'C' => '_c', 'D' => '_d',
            'E' => '_e', 'F' => '_f', 'G' => '_g', 'H' => '_h',
            'I' => '_i', 'J' => '_j', 'K' => '_k', 'L' => '_l',
            'M' => '_m', 'N' => '_n', 'O' => '_o', 'P' => '_p',
            'Q' => '_q', 'R' => '_r', 'S' => '_s', 'T' => '_t',
            'U' => '_u', 'V' => '_v', 'W' => '_w', 'X' => '_x',
            'Y' => '_y', 'Z' => '_z',
        ]));
    }

    /**
     * Simple prettyfyer for keys
     * Make readable names from key
     * @param string $key
     * @return string
     */
    public static function key2Label(string $key): string
    {
        $str = static::camelCase2underscore($key);
        $prepared = ucfirst(strtolower(
            strtr($str, ['_' => ' ', '.' => ' ','()' => ''])
        ));
            return preg_replace('/\s+/', ' ', $prepared);
    }

    /**
     * Parse doc comment string and get array of trimmed strings
     * @param string $doc
     * @return array
     */
    public static function parseDocComment(string $doc): array
    {
        // Split to lines
        $parts = explode(\PHP_EOL, $doc);
        // First and last lines not contain data
        if (count($parts) < 3) {
            return [];
        }
        array_pop($parts); // take last element and not use it, so just delete
        array_shift($parts); // take first, and not use it, so just delete
        // delete multispaces and delete spaces and star at start/end
        return array_map(function (string $line) {
            return preg_replace('/\s+/', ' ', trim($line, ' *'));
        }, $parts);
    }

    /**
     * Make string from template
     * @param string $template
     * @param type $data
     * @return string
     */
    public static function templateToString(string $template, $data = []): string
    {
        $wrapper = new DataWrapper($data);
        $replace = [];
        foreach (static::templateKeys($template) as $key) {
            $replace['{' . $key . '}'] = $wrapper[$key];
        }
        return strtr($template, $replace);
    }

    /**
     * Take data from string using template
     * if not correct template - return null
     * Dont support nested keys, return it at doted format
     *
     * @param string $template
     * @param string $data
     * @return array|null
     */
    public static function templateFromString(string $template, string $data): ?array
    {
        $keys = static::templateKeys($template);
        $pattern = static::makeTemplatePattern($template, $keys);
        if (preg_match($pattern, $data, $array)) {
            array_shift($array);
            return array_combine($keys, $array);
        } else {
            return null;
        }
    }

    /**
     * Return list of keys at template
     * @param string $template
     * @return array
     */
    public static function templateKeys(string $template): array
    {
        preg_match_all('/{([^}]+)}/i', $template, $data);
        return $data[1];
    }

    /**
     * Make pattern for parsing string
     * @see templateFromString
     * @param string $template
     * @param array $keys
     * @return string
     */
    protected static function makeTemplatePattern(string $template, array $keys): string
    {
        $replace = [];
        foreach ($keys as $key) {
            $replace['{' . $key . '}'] = '%';
        }
        $pattern = strtr($template, $replace);
        return '/^' . str_replace('%', '(.*)', preg_quote($pattern, '/')) . '$/i';
    }
}
