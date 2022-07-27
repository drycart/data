<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Helper for flexible conditions
 *
 * @author mendel
 */
class CheckHelper
{
    protected static $allRules = [];

    public function __construct()
    {
        $this->initAllRules();
    }

    /**
     * Check if data satisfies the condition
     *
     * @param mixed $data
     * @param array $conditions
     * @return bool
     */
    public function check($data, array $conditions): bool
    {
        if (empty($conditions)) {
            return true;
        }
        $args = $this->tryPrepareSimpleRules($conditions);
        $type = array_shift($args);
        switch (strtolower($type)) {
            case 'and':
                return $this->checkAnd($data, $args);
            case 'or':
                return $this->checkOr($data, $args);
            case 'not':
                return !$this->check($data, $args[0]);
            default:
                return $this->checkField($data, $type, $args[0], $args[1]);
        }
    }

    /**
     * If array of rules is in "simple format"
     * convert it to full format
     *
     * @param array $rules
     * @return array
     */
    protected function tryPrepareSimpleRules(array $rules): array
    {
        if (empty($rules) or isset($rules[0])) {
            return $rules;
        }
        $result = ['and'];
        foreach ($rules as $fieldRule => $arg2) {
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
    protected function initAllRules(): void
    {
        if (empty(static::$allRules)) {
            foreach (CompareHelper::RULES as $rule) {
                static::$allRules[] = '*' . $rule;
                static::$allRules[] = $rule;
            }
            foreach (array_keys(CompareHelper::RULES_ALIASES) as $rule) {
                static::$allRules[] = '*' . $rule;
                static::$allRules[] = $rule;
            }
            // Sort by lenght
            usort(static::$allRules, function (string $a, string $b): int {
                return strlen($b) <=> strlen($a); // for reversal result
            });
        }
    }

    /**
     * Check AND condition
     *
     * @param mixed $data
     * @param array $conditions
     * @return bool
     */
    protected function checkAnd($data, array $conditions): bool
    {
        foreach ($conditions as $line) {
            if (!$this->check($data, $line)) {
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
    protected function checkOr($data, array $conditions): bool
    {
        foreach ($conditions as $line) {
            if ($this->check($data, $line)) {
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
    protected function checkField($data, string $staredRule, $arg1, $arg2): bool
    {
        [$rulePrefix, $rule] = StrHelper::findPrefix($staredRule, ['*']);
        $value1 = $data->$arg1;
        if ($rulePrefix == '*') {
            $value2 = $data->$arg2;
        } else {
            $value2 = $arg2;
        }
        $helper = new CompareHelper();
        return $helper->compareByRule($rule, $value1, $value2);
    }
}
