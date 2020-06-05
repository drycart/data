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
class DataWrapper implements \Countable, \JsonSerializable, \IteratorAggregate
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

    public function getIterator(): \Traversable
    {
        if(is_a($this->data, \Traversable::class)) {
            return $this->data;
        } elseif(is_array($this->data)) {
            return new \ArrayIterator($this->data);
        } else {
            return new \ArrayIterator((array) $this->data);
        }
    }
}