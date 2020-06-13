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
            if ($wrapper1->get($field) != $wrapper2->get($field)) {
                return $direction * ($wrapper1->get($field) < $wrapper2->get($field) ? -1 : 1);                
            }
        }
        return 0;
    }

    /**
     * Find prefix supported by ruleCheck
     * return two value. first is rule, second is last part of string
     * 
     * @param string $str
     * @return array
     */
    public static function findRulePrefix(string $str) : array
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
    public static function tryPrepareSimpleRules(array $rules) : array
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
    public static function checkRule(string $rule, $value1, $value2) : bool
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
    
    protected static function tryRuleAliase(string $rule) : string
    {
        if(isset(self::RULES_ALIASES[$rule])) {
            return self::RULES_ALIASES[$rule];
        } else {
            return $rule; 
        }
    }
}
