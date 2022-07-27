<?php

/*
 *  @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;

use drycart\data\DataWrapper;
use drycart\data\CheckHelper;
use IteratorAggregate;
use Traversable;

/**
 * Filter data at iterator by condition
 *
 * @author mendel
 */
class FilterIterator extends \FilterIterator
{
    protected $conditions = [];
    /** @var CheckHelper  */
    protected $helper;

    public function __construct(Traversable $iterator, array $conditions)
    {
        if (is_a($iterator, IteratorAggregate::class)) {
            $iterator = $iterator->getIterator();
        }
        parent::__construct($iterator);
        $this->conditions = $conditions;
        $this->helper = new CheckHelper();
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return bool
     */
    public function accept(): bool
    {
        $wrapper = new DataWrapper($this->current());
        return $this->helper->check($wrapper, $this->conditions);
    }
}
