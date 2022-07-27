<?php

namespace drycart\data;

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

use Exception;
use Iterator;
use UnexpectedValueException;

/**
 * Trait for pretty use for list of constants and/or data arrays
 *
 * @author mendel
 */
trait EnumTrait
{
    /**
     * Array for titles. Just default dummy for override if need
     *
     * @return array
     */
    protected static function titles(): array
    {
        return [];
    }

    /**
     * Main function. Return list of constants i.e. keys and values...
     *
     * @return array
     */
    public static function data(): array
    {
        // @2DO: add assertion for check if values is uniqal
        $reflector = new \ReflectionClass(static::class);
        return $reflector->getConstants();
    }

    /**
     * Get iterator for titles/keys/values
     *
     * @return Iterator
     */
    public static function titlesIterator(): Iterator
    {
        $titles = static::titles();
        foreach (static::data() as $key => $value) {
            yield new DataWrapper([
                'key' => $key,
                'value' => $value,
                'title' => $titles[$key] ?? StrHelper::key2Label(strtolower($key))
            ]);
        }
    }

    /**
     * Return all keys and his titles (defined at titles() or default generated)
     *
     * @return array
     */
    public static function keyTitles(): array
    {
        $titles = static::titles();
        $result = [];
        foreach (array_keys(static::data()) as $key) {
            $result[$key] = $titles[$key] ?? StrHelper::key2Label(strtolower($key));
        }
        return $result;
    }

    /**
     * Return all values and his titles (defined at titles() or default generated)
     *
     * @return array
     */
    public static function valueTitles(): array
    {
        $titles = static::titles();
        $result = [];
        foreach (static::data() as $key => $value) {
            $result[$value] = $titles[$key] ?? StrHelper::key2Label(strtolower($key));
        }
        return $result;
    }

    /**
     * Get value by key
     * @param string $key
     * @return mixed
     */
    public static function value(string $key)
    {
        return static::data()[$key] ?? null;
    }

    /**
     * Get key by value
     * @param mixed $value
     * @return string|null
     */
    public static function key($value): ?string
    {
        $data = array_flip(static::data());
        return $data[$value] ?? null;
    }

    public static function checkKey(string $key): void
    {
        if (is_null(static::value($key))) {
            throw new UnexpectedValueException();
        }
    }
}
