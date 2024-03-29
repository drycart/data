<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\Iterator\SortIterator;
use drycart\data\Iterator\FilterIterator;
use drycart\data\Iterator\HydrateIterator;
use drycart\data\Iterator\DehydrateIterator;
use drycart\data\Money;
use PHPUnit\Framework\TestCase;

/**
 * @author mendel
 */
class IteratorsTest extends TestCase
{
    public function testSortIterator()
    {
        $data = new \ArrayObject([
            ['name'=>'Max','age'=>39,'score'=>12, 'salary' =>Money::fromFloat(150, 'USD')],
            ['name'=>'Jonn','age'=>18,'score'=>8, 'salary' =>Money::fromFloat(50, 'USD')],
            ['name'=>'Anton','age'=>18,'score'=>36, 'salary' =>Money::fromFloat(25, 'USD')],
        ]);
        $iterator = new SortIterator($data, ['score','age']);
        $arr = iterator_to_array($iterator);

        $this->assertEquals($arr[0]['name'], 'Jonn');
        $this->assertEquals($arr[1]['name'], 'Max');
        $this->assertEquals($arr[2]['name'], 'Anton');

        $iterator = new SortIterator($data, ['age','!score']);
        $arr = iterator_to_array($iterator);

        $this->assertEquals($arr[0]['name'], 'Anton');
        $this->assertEquals($arr[1]['name'], 'Jonn');
        $this->assertEquals($arr[2]['name'], 'Max');

        $iterator = new SortIterator($data, ['salary']);
        $arr = iterator_to_array($iterator);

        $this->assertEquals($arr[0]['name'], 'Anton');
        $this->assertEquals($arr[1]['name'], 'Jonn');
        $this->assertEquals($arr[2]['name'], 'Max');
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
    
    public function testDehydrateIterator()
    {
        $data = [
            ['name'=>'Max','age'=>39],
            ['name'=>'Jonn','age'=>18],
        ];
        $hydrateIterator = new HydrateIterator(new \ArrayObject($data), dummy\DummyModel::class);
        $dehydrateIterator = new DehydrateIterator($hydrateIterator);
        $arr = iterator_to_array($dehydrateIterator);
        $this->assertCount(2, $arr);
        $this->assertIsArray($arr[0]);
        $this->assertIsArray($arr[1]);
        $this->assertEquals($data[0],$arr[0]);
        $this->assertEquals($data[1],$arr[1]);
    }
}
