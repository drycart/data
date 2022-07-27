<?php

/*
 *  @copyright (c) 2022 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

use UnexpectedValueException;

class RuleHelper
{
    /** @var CheckHelper */
    private $checkHelper;

    /** @var CompareHelper */
    private $compareHelper;

    /** @var UpdateHelper */
    private $updateHelper;

    public function __construct()
    {
        $this->checkHelper = new CheckHelper();
        $this->compareHelper = new CompareHelper();
        $this->updateHelper = new UpdateHelper();
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
        return $this->checkHelper->check($data, $conditions);
    }

    /**
     * Compare two values, scalar or Comparable
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return int
     */
    public function compare($value1, $value2): int
    {
        return $this->compareHelper->compare($value1, $value2);
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
        return $this->compareHelper->compareByOrders($orders, $value1, $value2);
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
        return $this->compareHelper->compareByRule($rule, $value1, $value2);
    }

    public function update(&$data, array $changes): void
    {
        $this->updateHelper->update($data, $changes);
    }

    public function modify($data, string $modifier, $modifierParam = null, array $extraData = [])
    {
        return ModifyHelper::modify($data, $modifier, $modifierParam, $extraData);
    }
}
