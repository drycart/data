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
class CompareHelper
{
    // Dont change order - longer will be first (before other started from same symbols)
    // @2DO: when updated StrHepler::findPrefix - refactor for remove aliases
    const RULES = [
        '<=', '=<', '>=', '=>','!=','<>', '!like:','!contain:', '!in:', 'like:','contain:', 'in:', '<', '>', '!', '='
    ];
    const STARED_RULES = [
        '*<=', '*=<', '*>=', '*=>','*!=','*<>', '*!like:','*!contain:', '*!in:', '*like:','*contain:', '*in:', '*<', '*>', '*!', '*='
    ];
    const RULES_ALIASES = [
        '=<' => '<=',
        '=>' => '>=',
        '!' => '!=',
        '<>' => '!=',
        
        '*=<' => '*<=',
        '*=>' => '*>=',
        '*!' => '*!=',
        '*<>' => '*!=',
    ];

    /**
     * Compare two values using orders list in format [field1, field2, !field3]
     * where ! is reverse ordering
     * @param array $orders
     * @param type $value1
     * @param type $value2
     * @return int
     */
    public static function compareByOrders(array $orders, $value1, $value2) : int
    {
        $wrapper1 = new DataWrapper($value1);
        $wrapper2 = new DataWrapper($value2);
        foreach($orders as $order) {
            if($order[0] == '!') {
                $field = substr($order, 1);
                $direction = 1;
            } else {
                $field = $order;
                $direction = -1;
            }
            if ($wrapper1[$field] != $wrapper2[$field]) {
                return $direction * ($wrapper1[$field] < $wrapper2[$field] ? -1 : 1);                
            }
        }
        return 0;
    }

    /**
     * Do simple checks by rules
     * rule is one of: '<=', '=<', '>=', '=>', '!like:','!contain:', '!in:',
     * 'like:','contain:', 'in:', '<', '>', '!', '='
     * value1 and value2 is is parameters for compare
     * 
     * @param string $rule
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     * @throws \Exception
     */
    public static function compareByRule(string $rule, $value1, $value2) : bool
    {
        switch ($rule) {
            case '<=':
                return ($value1 <= $value2);
            case '>=':
                return ($value1 >= $value2);
            case '!like:':
                return !StrHelper::like($value1, $value2);
            case '!in:':
                return !in_array($value1, $value2);
            case '!contain:':
                return !in_array($value2, $value1);
            case 'like:':
                return StrHelper::like($value1, $value2);
            case 'contain:':
                return in_array($value2, $value1);
            case 'in:':
                return in_array($value1, $value2);
            case '<':
                return ($value1 < $value2);
            case '>':
                return ($value1 > $value2);
            case '!=':
                return ($value1 != $value2);
            case '=':
                return ($value1 == $value2);
            default:
                throw new \RuntimeException('Unknown rule '.$rule);
        }
    }
    
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
     * Find prefix supported by ruleCheck
     * return two value. first is rule, second is last part of string
     * 
     * @param string $str
     * @return array
     */
    protected static function findRulePrefix(string $str) : array
    {
        $allRules = array_merge(self::STARED_RULES, self::RULES);
        [$rule,$field] = StrHelper::findPrefix($str, $allRules, '=');
        return [static::tryRuleAliase($rule), $field];
    }
    
    /**
     * If array of rules is in "simple format"
     * convert it to full format
     * @param array $rules
     * @return array
     */
    protected static function tryPrepareSimpleRules(array $rules) : array
    {
        if(empty($rules) or isset($rules[0])) {
            return $rules;
        }
        $result = ['and'];
        foreach($rules as $fieldRule=>$arg2) {
            [$rule, $arg1] = static::findRulePrefix($fieldRule);
            $result[] = [$rule, $arg1, $arg2];
        }
        return $result;
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
        return self::compareByRule($rule, $value1, $value2);
    }

    protected static function tryRuleAliase(string $rule) : string
    {
        if(isset(self::RULES_ALIASES[$rule])) {
            return self::RULES_ALIASES[$rule];
        } else {
            return $rule; 
        }
    }
}
