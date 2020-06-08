<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\DataWrapper;

/**
 * @author mendel
 */
class DataWrapperTest extends \PHPUnit\Framework\TestCase
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
    
    public function testSafe()
    {
        $wrapper = $this->prepareWrapper(TRUE);
        //
        $this->assertEquals($wrapper->get('field1'), 'value1');
        $this->assertEquals($wrapper->get('obj.field2'), 'value2');
        $this->assertEquals($wrapper->get('array.field1'), 'value1');
        $this->assertEquals($wrapper->get('arrayObj.field1'), 'value1');
        $this->assertEquals($wrapper->get('arrayObj.count()'), 2);
        //
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Bad field name notExistField");        
        $wrapper->get('obj.notExistField');
    }
    
    public function testNotSafe()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        //
        $this->assertEquals($wrapper->get('field1'), 'value1');
        $this->assertEquals($wrapper->get('obj.field2'), 'value2');
        $this->assertEquals($wrapper->get('array.field1'), 'value1');
        $this->assertEquals($wrapper->get('arrayObj.field1'), 'value1');
        $this->assertEquals($wrapper->get('arrayObj.count()'), 2);
        //
        $this->assertEquals($wrapper->get('obj.notExistField'), null);
        $this->assertEquals($wrapper->get('obj.notExistMethod()'), null);
        //
        $this->assertEquals($wrapper->get('obj.notExistField', 'default'), 'default');
        $this->assertEquals($wrapper->get('obj.notExistMethod()', 'default'), 'default');
    }
    
    public function testMagic()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        //
        $field2 = 'arrayObj.field1';
        $field3 = 'arrayObj.count()';
        $this->assertEquals($wrapper->field1, 'value1');
        $this->assertEquals($wrapper->$field2, 'value1');
        $this->assertEquals($wrapper->$field3, 2);
        
        $this->assertFalse(isset($wrapper->notExistField));
        $this->assertTrue(isset($wrapper->field1));
        
        $this->assertEquals($wrapper->arrayObj->count(), 2);
        $wrapper2 = new DataWrapper(new \ArrayObject(['field1'=>'value1','field2'=>'value2']));
        $this->assertEquals($wrapper2->count(), 2);
    }
    
    public function testCheckFieldDirect()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertTrue($wrapper->check(['=','field1', 'value1']));
        $this->assertFalse($wrapper->check(['=','field1', 'wrongValue']));
        $this->assertTrue($wrapper->check(['=','notExistField', null]));
        $this->assertTrue($wrapper->check(['=','arrayObj.count()', 2]));
        $this->assertTrue($wrapper->check(['>','arrayObj.count()', 1]));
        //
    }
    
    public function testCheckFieldRelated()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertTrue($wrapper->check(['*=','field1', 'array.field1']));
        $this->assertFalse($wrapper->check(['*=','field1', 'notExistField']));
        $this->assertTrue($wrapper->check(['*=','notExistField', 'notExistField2']));
        $this->assertTrue($wrapper->check(['*=','arrayObj.count()', 'arrayObj.count()']));
        $this->assertTrue($wrapper->check(['*>=','arrayObj.count()', 'arrayObj.count()']));
        //
    }
    
    public function testCheckLogical()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertFalse($wrapper->check([
            'NOT',
            ['*=','field1', 'array.field1']
        ]));
        $this->assertFalse($wrapper->check([
            'AND',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue($wrapper->check([
            'OR',
            ['*=','field1', 'array.field1'],
            ['*>=','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertFalse($wrapper->check([
            'OR',
            ['*<>','field1', 'array.field1'],
            ['*>','arrayObj.count()', 'arrayObj.count()'],
            ['*=','field1', 'notExistField']
        ]));
        $this->assertTrue($wrapper->check([
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
        $this->assertTrue($wrapper->check([
            '*=field1' => 'array.field1',
            '*>=arrayObj.count()' => 'arrayObj.count()',
            '*<>field1' => 'notExistField'
        ]));
        $this->assertTrue($wrapper->check([
            'field1' => 'value1',
            '>=arrayObj.count()' => 1,
            '<>field1' => 'notExist'
        ]));
    }
}
