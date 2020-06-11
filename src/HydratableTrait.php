<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 *
 * @author mendel
 */
trait HydratableTrait
{
    /**
     * Hydrate
     * @param array $data
     * @return void
     */
    public function hydrate(array $data) : void
    {
        foreach($data as $key=>$value) {
            $this->$key = $value;
        }
    }

    public function dehydrate(): array
    {
        $data = [];
        foreach(GetterHelper::getKeys($this) as $key) {
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
