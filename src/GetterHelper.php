<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Traversable;
use UnexpectedValueException;

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
     * @throws UnexpectedValueException
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
     * @return Traversable
     */
    public static function getIterator($data): Traversable
    {
        if(is_a($data, Traversable::class)) {
            return $data;
        } elseif(is_array($data)) {
            return new ArrayIterator($data);
        } else {
            return new ArrayIterator((array) $data);
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
        } elseif(is_a($data, ArrayObject::class)) {
            $arr = $data->getArrayCopy();
            return array_keys($arr);
        } elseif(is_a($data, Traversable::class)) {
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
     * @param mixed $data
     * @param string $key
     * @param bool $safe
     * @return mixed
     * @throws UnexpectedValueException
     */
    protected static function subGet($data, string $key, bool $safe = true)
    {
        $modifier = null;
        if(StrHelper::contain($key, '#')) {
            [$key, $modifier] = explode('#', $key, 2);
        }
        if(!empty($key)) {
            $data = self::subGetRaw($data, $key, $safe);
        }
        if(!empty($modifier)) {
            $data = ModifyHelper::modify($data, $modifier);
        }
        return $data;
    }

    /**
     * Just get, no modifier etc
     *
     * @param mixed $data
     * @param string $key
     * @param bool $safe
     * @return mixed
     * @throws UnexpectedValueException
     */
    protected static function subGetRaw($data, string $key, bool $safe = true)
    {
        if (static::isArrayable($data) and isset($data[$key])) {
            return $data[$key];
        }

        if (is_object($data) AND isset($data->{$key})) {
            return $data->{$key};
        }

        // Methods magic...
        $method = static::tryGetMethodName($key);
        if (!is_null($method) and method_exists($data, $method)) {
            return call_user_func_array([$data, $method], []);
        }

        if ($safe) {
            throw new UnexpectedValueException("Bad field name $key");
        }
        return null;
    }

    /**
     * Check if data is array or ArrayAccess
     * @param mixed $data
     * @return bool
     */
    protected static function isArrayable($data) : bool
    {
        return is_array($data) OR is_a($data, ArrayAccess::class);
    }

    /**
     * Check if key is methods name (i.e. finished to "()")
     * and return methods name
     *
     * @param string $key
     * @return string|null
     */
    protected static function tryGetMethodName(string $key) : ?string
    {
        if(substr($key, -2) == '()') {
            return substr($key, 0, -2);
        }
        return null;
    }
}
