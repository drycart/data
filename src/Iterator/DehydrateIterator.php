<?php

/*
 *  @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;

/**
 * Iterator for dehydrate data from other iterator
 *
 * @author mendel
 */
class DehydrateIterator extends \IteratorIterator
{
    /**
     * return dehydrated object
     * @return array
     */
    public function current()
    {
        $model = parent::current();
        return $model->dehydrate();
    }
}
