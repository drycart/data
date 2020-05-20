<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */
namespace drycart\data;

/**
 * Wrapper for pretty access to field and check flexible logic conditions
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
class DataWrapper {
    protected $data;
    protected $safe = true;

    /**
     * @param mixed $data Data for we access. Array, object etc...
     * @param bool $safe if true - Exception for not exist fields
     */
    public function __construct($data, bool $safe = true) {
        $this->data = $data;
        $this->safe = $safe;
    }

    /**
     * Get some data by pretty name
     * @param string $name name for access
     * @param mixed $default used for non safe request, if we dont find answer
     * @return mixed
     * @throws \Exception
     */
    public function get(string $name, $default = null) {
        $data = $this->data;
        $fields = explode('.', $name);
        foreach ($fields as $key) {
            if (is_array($data)) { // Just array, because ArrayAccess can have his own logic as object field
                $data = (object) $data;
            }
            //
            if (isset($data->{$key})) { // simple
                $data = $data->{$key};
            } elseif (is_a($data, \ArrayAccess::class) and isset($data[$key])) { // for ArrayAccess obj
                $data = $data[$key];
                // Methods magic...
            } elseif ((substr($key, -2) == '()') and method_exists($data, substr($key, 0, -2))) {
                $data = call_user_func_array([$data, substr($key, 0, -2)], []);
            } elseif ($this->safe) {
                throw new \Exception("Bad field name $key at name $name fields");
            } else {
                return $default;
            }
        }
        return $data;
    }
    
    /**
     * Check if data satisfies the condition
     * @param array $conditions
     * @return bool
     */
    public function check(array $conditions) : bool
    {
        $args = CompareHelper::tryPrepareSimpleRules($conditions);
        $type = array_shift($args);
        switch ($type) {
            case 'AND':
            case 'and':
                return $this->checkAnd($args);
            case 'OR':
            case 'or':
                return $this->checkOr($args);
            case 'NOT':
            case 'not':
                return !$this->check($args[0]);
            default:
                return $this->checkField($type, $args[0], $args[1]);
        }
    }

    /**
     * Check AND condition
     * @param array $conditions
     * @return bool
     */
    protected function checkAnd(array $conditions) : bool
    {
        foreach($conditions as $line) {
            if(!$this->check($line)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check OR condition
     * @param array $conditions
     * @return bool
     */
    protected function checkOr(array $conditions) : bool
    {
        foreach($conditions as $line) {
            if($this->check($line)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check/compare some field by rule and some value
     * @param string $staredRule
     * @param mixed $arg1
     * @param mixed $arg2
     * @return bool
     */
    protected function checkField(string $staredRule, $arg1, $arg2) : bool
    {
        [$rulePrefix, $rule] = StrHelper::findPrefix($staredRule, ['*']);
        $value1 = $this->get($arg1);
        if($rulePrefix == '*') {
            $value2 = $this->get($arg2);
        } else {
            $value2 = $arg2;
        }
        return CompareHelper::checkRule($rule, $value1, $value2);
    }
    
    /**
     * Magic proxy call to data
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        // @2DO: check if method exist
        return call_user_func_array([$this->data, $name], $arguments);
    }

    /**
     * Magic isset
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return !is_null($this->get($name));
    }

    /**
     * Magic getter, sugar for get()
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }
}