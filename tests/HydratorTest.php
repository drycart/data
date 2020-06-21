<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\Hydrator;

/**
 * @author mendel
 */
class HydratorTest extends \PHPUnit\Framework\TestCase
{
    public function testHydrate()
    {
        $model1 = Hydrator::hydrate(dummy\DummyModel::class, ['name'=>'Max','age'=>39]);
        $this->assertEquals(get_class($model1), dummy\DummyModel::class);
        $this->assertEquals($model1->name, 'Max');
        $this->assertEquals($model1->age, 39);
        $model2 = Hydrator::hydrate(dummy\DummyExtendedModel::class, ['name'=>'Max','age'=>39]);
        $this->assertEquals(get_class($model2), dummy\DummyExtendedModel::class);
        $this->assertEquals($model2->name, 'Max');
        $this->assertEquals($model2->age, 39);
        //
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Hyrdator will hydrate only instance of HydratableInterface");
        Hydrator::hydrate(dummy\DummyEnum::class, ['name'=>'Max','age'=>39]);
    }
}
