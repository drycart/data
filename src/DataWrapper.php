<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */
namespace drycart\data;

/**
 * Wrapper for pretty access to field and check flexible logic conditions
 * Used for deep access to some data at some unknown data
 */
class DataWrapper implements DataInterface, \IteratorAggregate, \ArrayAccess
{
    use CheckTrait;
    
    protected $data;
    protected $safe = true;

    /**
     * @param mixed $data Data for we access. Array, object etc...
     * @param bool $safe if true - Exception for not exist fields
     */
    public function __construct($data, bool $safe = true)
    {
        $this->data = $data;
        $this->safe = $safe;
    }

    /**
     * Get some data by pretty name
     * 
     * @param string $name name for access
     * @param mixed $default used for non safe request, if we dont find answer
     * @return mixed
     * @throws \Exception
     */
    public function get(string $name, $default = null)
    {
        return GetterHelper::get($this->data, $name, $this->safe, $default);
    }
    
    /**
     * Magic proxy call to data
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
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return !is_null($this->get($name));
    }

    /**
     * Magic getter, sugar for get()
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
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
     * @return type
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Get iterator
     * 
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return GetterHelper::getIterator($this->data);
    }

    /**
     * Sugar for array access is_set
     * 
     * @param type $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * Sugar ArrayAccess getter
     * 
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Dummy method for interface only
     * 
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws \RuntimeException
     */
    public function offsetSet($offset, $value): void
    {
        throw \RuntimeException('DataWraper is just read-only wrapper');
    }

    /**
     * Dummy method for interface only
     * 
     * @param mixed $offset
     * @return void
     * @throws type
     */
    public function offsetUnset($offset): void
    {
        throw \RuntimeException('DataWraper is just read-only wrapper');
    }

    public function fieldLabel(string $key): string
    {
        if(is_object($this->data) and is_a($this->data, DataInterface::class)) {
            return $this->data->fieldLabel($key);
        } else {
            StrHelper::key2Label($key);
        }
    }

    public function keys(): array
    {
        return GetterHelper::getKeys($this->data);
    }

}