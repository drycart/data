<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;
use drycart\data\CompareHelper;

/**
 * Sort iterator data using order rules
 *
 * @author mendel
 */
class SortIterator extends \SplHeap
{
    /**
     * Orders
     * 
     * @var array
     */
    protected $orders = [];
    
    /**
     * Constructor
     * 
     * @param \Traversable $iterator
     * @param array $orders
     */
    public function __construct(\Traversable $iterator, array $orders)
    {
        $this->orders = $orders;
        foreach ($iterator as $line) {
            $this->insert($line);
        }
        // Dont call parent, because it not exist (Yes, not exist!)
        //parent::__construct();
    }

    /**
     * Compare two element using order rules
     * 
     * @param type $value1
     * @param type $value2
     * @return int
     */
    protected function compare($value1, $value2): int
    {
        return CompareHelper::compareByOrders($this->orders, $value1, $value2);
    }

}
