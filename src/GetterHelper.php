<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Wrapper for pretty access to field
 * Used for deep access to some data at some unknown data
 *
 * fieldName for example forum.moderators.first().name work correct for any of this sence:
 * $data->forum->moderators->first()->name
 * $data['forum']['moderators']->first()['name']
 * $data['forum']->moderators->first()['name']
 * etc...
 *
 * Object field is priority option, second is array, after this we try method,
 * so if exist something like this, it will be used
 * $data['forum']->moderators['first()']['name']
 *
 * methods parameters not supports at any format
 */
class GetterHelper
{
    /**
     * Get some data by pretty name
     * 
     * @param array|object $data data for pretty access
     * @param string $name name for access
     * @param bool $safe if true - Exception for not exist fields
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public static function get($data, string $name, bool $safe = true)
    {
        $fields = explode('.', $name);
        foreach ($fields as $key) {
            $data = static::subGet($data, $key, $safe);
        }
        return $data;
    }

    /**
     * Get iterator for data
     * 
     * @param mixed $data
     * @return \Traversable
     */
    public static function getIterator($data): \Traversable
    {
        if(is_a($data, \Traversable::class)) {
            return $data;
        } elseif(is_array($data)) {
            return new \ArrayIterator($data);
        } else {
            return new \ArrayIterator((array) $data);
        }
    }

    /**
     * Get keys list for data
     * 
     * @param mixed $data
     * @return array
     */
    public static function getKeys($data) : array
    {
        if(is_object($data) and is_a($data, ModelInterface::class)) {
            return $data->keys();
        } elseif(is_array($data)) {
            return array_keys($data);
        } elseif(is_a($data, \ArrayObject::class)) {
            $arr = $data->getArrayCopy();
            return array_keys($arr);
        } elseif(is_a($data, \Traversable::class)) {
            $arr = iterator_to_array($data);
            return array_keys($arr);
        } else {
            $arr = (array) $data;
            return array_keys($arr);
        }
    }

    /**
     * One level get
     * 
     * @param type $data
     * @param string $key
     * @param bool $safe
     * @return mixed
     * @throws \UnexpectedValueException
     */
    protected static function subGet($data, string $key, bool $safe = true)
    {
        if (is_array($data)) { // Just array, because ArrayAccess can have his own logic as object field
            $data = (object) $data;
        }
        //
        if (isset($data->{$key})) { // simple
            return $data->{$key};
        } elseif (is_a($data, \ArrayAccess::class) and isset($data[$key])) { // for ArrayAccess obj
            return $data[$key];
            // Methods magic...
        } elseif ((substr($key, -2) == '()') and method_exists($data, substr($key, 0, -2))) {
            return call_user_func_array([$data, substr($key, 0, -2)], []);
        } elseif ($safe) {
            throw new \UnexpectedValueException("Bad field name $key");
        } else {
            return null;
        }
    }
}
