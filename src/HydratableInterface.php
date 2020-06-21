<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Hydratable, i.e. object what can be converted to array
 * of scalar data and converted back to array without side effects
 * 
 * @author mendel
 */
interface HydratableInterface
{
    /**
     * Hydrate model by array of data
     * 
     * @param array $data
     * @return void
     */
    public function hydrate(array $data) : void;
    
    /**
     * Dehydrate object to array
     * 
     * @return array
     */
    public function dehydrate(): array;
}
