<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Trait for hydrate/dehydrate methods
 * 
 * @author mendel
 */
trait HydratableTrait
{
    /**
     * Hydrate
     * 
     * @param array $data
     * @return void
     */
    public function hydrate(array $data) : void
    {
        foreach($data as $key=>$value) {
            $this->$key = $value;
        }
    }

    /**
     * Dehydrate
     * 
     * @return array
     */
    public function dehydrate(): array
    {
        $data = [];
        foreach(GetterHelper::getKeys($this) as $key) {
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
