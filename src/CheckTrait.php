<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 *
 * @author mendel
 */
trait CheckTrait
{
    /**
     * Get field data by field name
     * 
     * @param string $name name for access
     * @return mixed
     */
    abstract public function get(string $name);
    
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
    private function checkAnd(array $conditions) : bool
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
    private function checkOr(array $conditions) : bool
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
    private function checkField(string $staredRule, $arg1, $arg2) : bool
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
}
