<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests\dummy;

/**
 * Description of DummyModel
 *
 * @author mendel
 */
class DummyModel
{
    /**
     * @var string name
     */
    public $name;
    
    /**
     * @var int age
     */
    public $age;
    
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
}
