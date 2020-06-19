<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Helper for simple compare checks
 *
 * @author mendel
 */
class CheckHelper
{
    protected static $allRules = [];
    
    /**
     * Check if data satisfies the condition
     * 
     * @param mixed $data
     * @param array $conditions
     * @return bool
     */
    public static function check($data, array $conditions) : bool
    {
        $args = self::tryPrepareSimpleRules($conditions);
        $type = array_shift($args);
        switch (strtolower($type)) {
            case 'and':
                return self::checkAnd($data, $args);
            case 'or':
                return self::checkOr($data, $args);
            case 'not':
                return !self::check($data, $args[0]);
            default:
                return self::checkField($data, $type, $args[0], $args[1]);
        }
    }
    
    /**
     * If array of rules is in "simple format"
     * convert it to full format
     * @param array $rules
     * @return array
     */
    protected static function tryPrepareSimpleRules(array $rules) : array
    {
        self::initAllRules();
        if(empty($rules) or isset($rules[0])) {
            return $rules;
        }
        $result = ['and'];
        foreach($rules as $fieldRule=>$arg2) {
            [$rule, $arg1] = StrHelper::findPrefix($fieldRule, static::$allRules, '=');
            $result[] = [$rule, $arg1, $arg2];
        }
        return $result;
    }

    /**
     * Init list of rules if not initialized
     * 
     * @return void
     */
    public static function initAllRules() : void
    {
        if(empty(static::$allRules)) {
            $rules = [];
            foreach(CompareHelper::RULES as $rule) {
                $rules[] = '*'.$rule;
            }
            static::$allRules = array_merge($rules, CompareHelper::RULES);
        }
    }
    
    /**
     * Check AND condition
     * 
     * @param mixed $data
     * @param array $conditions
     * @return bool
     */
    protected static function checkAnd($data, array $conditions) : bool
    {
        foreach($conditions as $line) {
            if(!self::check($data,$line)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check OR condition
     * 
     * @param mixed $data
     * @param array $conditions
     * @return bool
     */
    protected static function checkOr($data, array $conditions) : bool
    {
        foreach($conditions as $line) {
            if(self::check($data,$line)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check/compare some field by rule and some value
     * 
     * @param mixed $data
     * @param string $staredRule
     * @param mixed $arg1
     * @param mixed $arg2
     * @return bool
     */
    protected static function checkField($data, string $staredRule, $arg1, $arg2) : bool
    {
        [$rulePrefix, $rule] = StrHelper::findPrefix($staredRule, ['*']);
        $value1 = $data->$arg1;
        if($rulePrefix == '*') {
            $value2 = $data->$arg2;
        } else {
            $value2 = $arg2;
        }
        return CompareHelper::compareByRule($rule, $value1, $value2);
    }
}
