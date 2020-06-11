<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests\dummy;

use drycart\data\HydratableInterface;
use drycart\data\HydratableTrait;
/**
 * Description of DummyModel
 *
 * @author mendel
 */
class DummyModel implements HydratableInterface
{
    use HydratableTrait;
    
    /**
     * @var string name
     */
    public $name;
    
    /**
     * @var int age
     */
    public $age;
        
    public function getSomeString() : string
    {
        return 'some string';
    }

}
