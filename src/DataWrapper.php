<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */
namespace drycart\data;

use ArrayAccess;
use IteratorAggregate;
use Traversable;

/**
 * Wrapper for pretty access to field
 * Used for deep access to some data at some unknown data
 */
class DataWrapper implements ModelInterface, IteratorAggregate, ArrayAccess
{
    protected $data;
    protected $safe = true;
    protected $titleKey = null;

    /**
     * @param mixed $data Data for we access. Array, object etc...
     * @param bool $safe if true - Exception for not exist fields, else NULL
     * @param string|null $titleKey if not null - used as key for title method
     * @throws \UnexpectedValueException
     */
    public function __construct($data, bool $safe = true, ?string $titleKey = null)
    {
        $this->data = $data;
        $this->safe = $safe;
        $this->titleKey = $titleKey;
    }

    /**
     * Magic proxy call to data
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // @2DO: check if method exist
        return call_user_func_array([$this->data, $name], $arguments);
    }

    /**
     * Magic isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return !is_null($this->__get($name));
    }

    /**
     * Get some data by pretty name
     *
     * @param string $name name for access
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function __get($name)
    {
        return GetterHelper::get($this->data, $name, $this->safe);
    }

    /**
     * Get count of data fields, just sugar for data methods
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Json serialise data - here just data object/array
     *
     * @return object|array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Get iterator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return GetterHelper::getIterator($this->data);
    }

    /**
     * Get keys list
     *
     * @return array
     */
    public function keys(): array
    {
        return GetterHelper::getKeys($this->data);
    }

    /**
     * Sugar for array access is_set
     *
     * @param int|string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * Sugar ArrayAccess getter
     *
     * @param int|string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Human readable name for some field (by key)
     *
     * @param string $key
     * @return string
     */
    public function fieldLabel(string $key): string
    {
        if (is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->fieldLabel($key);
        } else {
            return StrHelper::key2Label($key);
        }
    }

    /**
     * Dummy title if not exist
     *
     * @return string
     */
    public function title(): string
    {
        if (is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->title();
        } elseif (!empty($this->titleKey)) {
            return $this[$this->titleKey];
        } elseif (is_array($this->data)) {
            return 'Some array...';
        } else {
            return 'Object #' . spl_object_id($this->data);
        }
    }

    /**
     * Dummy fieldsInfo if not exist
     *
     * @return array
     */
    public function fieldsInfo(): array
    {
        if (is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->fieldsInfo();
        }

        $info = [];
        foreach ($this->keys() as $key) {
            $info[$key] = [];
        }
        return $info;
    }

    /**
     * Magic setter for ArrayAccess
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_array($this->data) or is_a($this->data, ArrayAccess::class)) {
            $this->data[$offset] = $value;
        } else {
            $this->data->$offset = $value;
        }
    }

    /**
     * Magic unset for ArrayAccess
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->$offset);
    }

    /**
     * Magic unset
     *
     * @param string $name
     * @return void
     */
    public function __unset($name): void
    {
        if (is_array($this->data) or is_a($this->data, ArrayAccess::class)) {
            unset($this->data[$name]);
        } else {
            unset($this->data->$name);
        }
    }
}
