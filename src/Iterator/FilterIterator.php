<?php

/*
 *  @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;

use drycart\data\DataWrapper;
use drycart\data\CheckHelper;

/**
 * Filter data at iterator by condition
 *
 * @author mendel
 */
class FilterIterator extends \FilterIterator
{
    protected $conditions = [];

    public function __construct(\Traversable $iterator, array $conditions)
    {
        if (is_a($iterator, \IteratorAggregate::class)) {
            $iterator = $iterator->getIterator();
        }
        parent::__construct($iterator);
        $this->conditions = $conditions;
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return bool
     */
    public function accept(): bool
    {
        $wrapper = new DataWrapper($this->current());
        return CheckHelper::check($wrapper, $this->conditions);
    }
}
