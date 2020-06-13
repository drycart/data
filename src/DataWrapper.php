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
class DataWrapper implements ModelInterface, \IteratorAggregate, \ArrayAccess
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
        if(!is_array($data) and ! is_object($data)) {
            throw new \RuntimeException('DataWraper can wrap only array or object');
        }
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
     * @return object|array
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

    public function fieldLabel(string $key): string
    {
        if(is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->fieldLabel($key);
        } else {
            return StrHelper::key2Label($key);
        }
    }

    public function title(): string
    {
        if(is_array($this->data)) {
            return 'Some array...';
        } elseif(is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->title();
        } else {
            return 'Object #'.spl_object_id($this->data);
        }
    }

    public function fieldsInfo(): array
    {
        if(is_object($this->data) and is_a($this->data, ModelInterface::class)) {
            return $this->data->fieldsInfo();
        }
        
        $info = [];
        foreach($this->keys() as $key) {
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
        if(is_array($this->data) or is_a($this->data, \ArrayAccess::class)) {
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
    public function __unset($name) : void
    {
        if(is_array($this->data) or is_a($this->data, \ArrayAccess::class)) {
            unset($this->data[$name]);
        } else {
            unset($this->data->$name);
        }
    }

}