<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\CheckHelper;
use drycart\data\DataWrapper;
use PHPUnit\Framework\TestCase;

/**
 * @author mendel
 */
class CheckHelperTest extends TestCase
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
        $checker = new CheckHelper();
        $this->assertTrue($checker->check($wrapper, []));
        $this->assertTrue($checker->check($wrapper, ['=','field1', 'value1']));
        $this->assertFalse($checker->check($wrapper, ['=','field1', 'wrongValue']));
        $this->assertTrue($checker->check($wrapper, ['=','notExistField', null]));
        $this->assertTrue($checker->check($wrapper, ['=','arrayObj.count()', 2]));
        $this->assertTrue($checker->check($wrapper, ['>','arrayObj.count()', 1]));
    }
    
    public function testCheckFieldRelated()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $checker = new CheckHelper();
        $this->assertTrue($checker->check($wrapper, ['*=','field1', 'array.field1']));
        $this->assertFalse($checker->check($wrapper, ['*=','field1', 'notExistField']));
        $this->assertTrue($checker->check($wrapper, ['*=','notExistField', 'notExistField2']));
        $this->assertTrue($checker->check($wrapper, ['*=','arrayObj.count()', 'arrayObj.count()']));
        $this->assertTrue($checker->check($wrapper, ['*>=','arrayObj.count()', 'arrayObj.count()']));
    }
    
    public function testCheckLogical()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $checker = new CheckHelper();
        $this->assertFalse($checker->check($wrapper, [
            'NOT',
            ['*=','field1', 'array.field1']
        ]));
        $this->assertFalse($checker->check($wrapper, [
            'AND',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue($checker->check($wrapper, [
            'OR',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertFalse($checker->check($wrapper, [
            'OR',
            ['*!=','field1', 'array.field1'],
            ['*>','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue($checker->check($wrapper, [
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
        $this->assertTrue($checker->check($wrapper, [
            '*=field1' => 'array.field1',
            '*>=arrayObj.count()' => 'arrayObj.count()',
            '*<>field1' => 'notExistField'
        ]));
        $this->assertTrue($checker->check($wrapper, [
            'field1' => 'value1',
            '>=arrayObj.count()' => 1,
            '<>field1' => 'notExist'
        ]));
    }
}
