<?php
namespace drycart\data\Iterator;
use drycart\data\DataWrapper;

/**
 * Filter data at iterator by condition
 * 
 * @author max
 */
class FilterIterator extends \FilterIterator
{
    protected $conditions = [];
    protected $helper;
    
    public function __construct(\Traversable $iterator, array $conditions)
    {
        if(is_a($iterator, \IteratorAggregate::class)) {
            $iterator = $iterator->getIterator();
        }
        parent::__construct($iterator);
        $this->conditions = $conditions;
    }
    
    public function accept(): bool
    {
        $wrapper = new DataWrapper($this->current());
        return $wrapper->check($this->conditions);
    }
}
