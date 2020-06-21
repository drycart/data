<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;
use drycart\data\Hydrator;

/**
 * Hydrate iterator
 * wrap iterator of array and return hydrated models
 *
 * @author mendel
 */
class HydrateIterator extends \IteratorIterator
{
    /**
     * Model class for hydrated data
     * @var string
     */
    protected $modelClass;
    
    /**
     * Constructor
     * @param \Traversable $iterator
     * @param string $modelClass
     */
    public function __construct(\Traversable $iterator, string $modelClass)
    {
        $this->modelClass = $modelClass;
        parent::__construct($iterator);
    }

    /**
     * return hydrated object
     * 
     * @return mixed
     */
    public function current()
    {
        $data = parent::current();
        return Hydrator::hydrate($this->modelClass, $data);
    }
}
