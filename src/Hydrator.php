<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Hydrator - hydrate array to model
 * used by HydrateIterator
 *
 * @author mendel
 */
class Hydrator
{
    /**
     * Hydrate
     * @param string $class
     * @param array $data
     * @return mixed
     */
    public static function hydrate(string $class, array $data)
    {
        if(method_exists($class, 'make')) {
            $model = $class::make();
        } else {
            $model = new $class;
        }
        if(!is_a($model, HydratableInterface::class)) {
            throw new \RuntimeException('Hyrdator will hydrate only instance of HydratableInterface');
        }
        $model->hydrate($data);
        return $model;        
    }
}
