<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\CheckHelper;
use drycart\data\DataWrapper;

/**
 * @author mendel
 */
class CheckHelperTest extends \PHPUnit\Framework\TestCase
{
    protected function prepareWrapper(bool $safe) : DataWrapper
    {
        $data = [
            'field1'=>'value1',
            'field2'=>'value2',
            'obj'=>(object) ['field1'=>'value1','field2'=>'value2'],
            'array'=>['field1'=>'value1','field2'=>'value2'],
            'arrayObj'=> new \ArrayObject(['field1'=>'value1','field2'=>'value2'])
        ];
        return new DataWrapper($data, $safe);
    }
    
    public function testCheckFieldDirect()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertTrue(CheckHelper::check($wrapper, []));
        $this->assertTrue(CheckHelper::check($wrapper, ['=','field1', 'value1']));
        $this->assertFalse(CheckHelper::check($wrapper, ['=','field1', 'wrongValue']));
        $this->assertTrue(CheckHelper::check($wrapper, ['=','notExistField', null]));
        $this->assertTrue(CheckHelper::check($wrapper, ['=','arrayObj.count()', 2]));
        $this->assertTrue(CheckHelper::check($wrapper, ['>','arrayObj.count()', 1]));
    }
    
    public function testCheckFieldRelated()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertTrue(CheckHelper::check($wrapper, ['*=','field1', 'array.field1']));
        $this->assertFalse(CheckHelper::check($wrapper, ['*=','field1', 'notExistField']));
        $this->assertTrue(CheckHelper::check($wrapper, ['*=','notExistField', 'notExistField2']));
        $this->assertTrue(CheckHelper::check($wrapper, ['*=','arrayObj.count()', 'arrayObj.count()']));
        $this->assertTrue(CheckHelper::check($wrapper, ['*>=','arrayObj.count()', 'arrayObj.count()']));
    }
    
    public function testCheckLogical()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertFalse(CheckHelper::check($wrapper, [
            'NOT',
            ['*=','field1', 'array.field1']
        ]));
        $this->assertFalse(CheckHelper::check($wrapper, [
            'AND',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue(CheckHelper::check($wrapper, [
            'OR',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertFalse(CheckHelper::check($wrapper, [
            'OR',
            ['*!=','field1', 'array.field1'],
            ['*>','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue(CheckHelper::check($wrapper, [
            'and',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            [
                'not',
                ['*=','field1', 'notExistField']
            ],
            [
                'or',
                ['*=','field1', 'array.field1'],
                ['*>=','arrayObj.count()', 'arrayObj.count()'],
                ['*=','field1', 'notExistField']
            ]
        ]));
        $this->assertTrue(CheckHelper::check($wrapper, [
            '*=field1' => 'array.field1',
            '*>=arrayObj.count()' => 'arrayObj.count()',
            '*<>field1' => 'notExistField'
        ]));
        $this->assertTrue(CheckHelper::check($wrapper, [
            'field1' => 'value1',
            '>=arrayObj.count()' => 1,
            '<>field1' => 'notExist'
        ]));
    }
}
