<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\SortIterator;
use drycart\data\FilterIterator;
use drycart\data\HydrateIterator;

/**
 * @author mendel
 */
class IteratorsTest extends \PHPUnit\Framework\TestCase
{
    public function testSortIterator()
    {
        $data = new \ArrayObject([
            ['name'=>'Max','age'=>39,'score'=>12],
            ['name'=>'Jonn','age'=>18,'score'=>8],
            ['name'=>'Anton','age'=>18,'score'=>36],
        ]);
        $iterator = new SortIterator($data, ['score','age']);
        $arr = iterator_to_array($iterator);
        
        $this->assertEquals($arr[0]['name'], 'Anton');
        $this->assertEquals($arr[1]['name'], 'Max');
        $this->assertEquals($arr[2]['name'], 'Jonn');
        
        $iterator = new SortIterator($data, ['age','!score']);
        $arr = iterator_to_array($iterator);
        
        $this->assertEquals($arr[0]['name'], 'Max');
        $this->assertEquals($arr[1]['name'], 'Jonn');
        $this->assertEquals($arr[2]['name'], 'Anton');
    }
    
    public function testFilterIterator()
    {
        $data = new \ArrayObject([
            ['name'=>'Max','age'=>39,'score'=>12],
            ['name'=>'Jonn','age'=>18,'score'=>8],
            ['name'=>'Anton','age'=>18,'score'=>36],
        ]);
        $iterator = new FilterIterator($data,['age'=>18,'>score'=>10]);
        $arr = array_values(iterator_to_array($iterator));
        $this->assertEquals($arr[0]['name'], 'Anton');
        $this->assertEquals(count($arr), 1);
    }
    
    public function testHydrateIterator()
    {
        $data = new \ArrayObject([
            ['name'=>'Max','age'=>39],
            ['name'=>'Jonn','age'=>18],
        ]);
        $iterator = new HydrateIterator($data, dummy\DummyModel::class);
        $arr = iterator_to_array($iterator);
        
        $this->assertEquals(get_class($arr[0]), dummy\DummyModel::class);
        $this->assertEquals($arr[0]->name, 'Max');
        $this->assertEquals($arr[0]->age, 39);
        
        $this->assertEquals(get_class($arr[1]), dummy\DummyModel::class);
        $this->assertEquals($arr[1]->name, 'Jonn');
        $this->assertEquals($arr[1]->age, 18);
    }
}
