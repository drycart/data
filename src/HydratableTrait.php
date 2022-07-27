<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Trait for dummy hydrate/dehydrate methods
 *
 * @author mendel
 */
trait HydratableTrait
{
    /**
     * Hydrate
     *
     * @param array $data
     * @return HydratableInterface
     */
    public static function hydrate(array $data): HydratableInterface
    {
        $model = new static();
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * Dehydrate
     *
     * @return array
     */
    public function dehydrate(): array
    {
        $data = [];
        foreach (GetterHelper::getKeys($this) as $key) {
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
