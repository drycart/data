<?php
namespace drycart\data;

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

/**
 * Abstract class for pretty use for list of constants and/or data arrays
 *
 * @author mendel
 */
abstract class AbstractEnum
{
    /**
     * Array for titles. Just default dummy for override if need
     * @return array
     */
    static protected function titles() : array
    {
        return [];
    }
    
    /**
     * Main function. Return list of constants i.e. keys and values...
     * @return array
     */
    static public function data() : array
    {
        // @2DO: add assertion for check if values is uniqal
        $reflector = new \ReflectionClass(static::class);
        return $reflector->getConstants();
    }
    
    /**
     * Return all keys and his titles (defined at titles() or default generated)
     * @return array
     */
    static public function keyTitles() : array
    {
        $titles = static::titles();
        $result = [];
        foreach(array_keys(static::data()) as $key) {
            $result[$key] = $titles[$key] ?? StrHelper::key2Title(strtolower($key));
        }
        return $result;
    }
    
    /**
     * Return all values and his titles (defined at titles() or default generated)
     * @return array
     */
    static public function valueTitles() : array
    {
        $titles = static::titles();
        $result = [];
        foreach(static::data() as $key=>$value) {
            $result[$value] = $titles[$key] ?? StrHelper::key2Title(strtolower($key));
        }
        return $result;
    }
        
    /**
     * Get value by key
     * @param string $key
     * @return mixed
     */
    static public function value(string $key)
    {
        return static::data()[$key] ?? null;
    }
    
    /**
     * Get key by value
     * @param mixed $value
     * @return string|null
     */
    static public function key($value) : ?string
    {
        $data = array_flip(static::data());
        return $data[$value] ?? null;
    }
    
}
