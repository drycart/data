<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

use UnexpectedValueException;

/**
 * Helper for simple compare checks
 *
 * @author mendel
 */
class CompareHelper
{
    /**
     * List of main compare rules
     */
    public const RULES = [
        '<=', '>=', '!=', '!like:','!contain:', '!in:', 'like:','contain:', 'in:', '<', '>', '='
    ];

    /**
     * list of aliases for rules
     */
    public const RULES_ALIASES = [
        '=<' => '<=',
        '=>' => '>=',
        '!' => '!=',
        '<>' => '!=',
    ];

    /**
     * Compare two values, scalar or Comparable
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return int
     */
    public function compare($value1, $value2): int
    {
        if (is_a($value1, ComparableInterface::class)) {
            return $value1->compare($value2);
        }
        return $value1 <=> $value2;
    }

    /**
     * Compare two values using orders list in format [field1, field2, !field3]
     * where ! is reverse ordering
     *
     * @param array $orders
     * @param mixed $value1
     * @param mixed $value2
     * @return int
     */
    public function compareByOrders(array $orders, $value1, $value2): int
    {
        $wrapper1 = new DataWrapper($value1);
        $wrapper2 = new DataWrapper($value2);
        foreach ($orders as $order) {
            if ($order[0] == '!') {
                $field = substr($order, 1);
                $direction = -1;
            } else {
                $field = $order;
                $direction = 1;
            }
            $compareResult = $this->compare($wrapper1[$field], $wrapper2[$field]);
            if ($compareResult != 0) {
                return $direction * $compareResult;
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
     * @throws UnexpectedValueException
     */
    public function compareByRule(string $rule, $value1, $value2): bool
    {
        switch ($this->tryRuleAliase($rule)) {
            case '<=':
                $compareResult = $this->compare($value1, $value2);
                return $compareResult <= 0;
            case '>=':
                $compareResult = $this->compare($value1, $value2);
                return $compareResult >= 0;
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
                $compareResult = $this->compare($value1, $value2);
                return $compareResult < 0;
            case '>':
                $compareResult = $this->compare($value1, $value2);
                return $compareResult > 0;
            case '!=':
                $compareResult = $this->compare($value1, $value2);
                return $compareResult != 0;
            case '=':
                $compareResult = $this->compare($value1, $value2);
                return $compareResult == 0;
            default:
                throw new UnexpectedValueException('Unknown rule ' . $rule);
        }
    }

    /**
     * Change rules aliase to main rule if it is aliase
     *
     * @param string $rule
     * @return string
     */
    protected function tryRuleAliase(string $rule): string
    {
        if (isset(self::RULES_ALIASES[$rule])) {
            return self::RULES_ALIASES[$rule];
        } else {
            return $rule;
        }
    }
}
