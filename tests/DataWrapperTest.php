<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use ArrayObject;
use drycart\data\DataWrapper;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * @author mendel
 */
class DataWrapperTest extends TestCase
{
    protected function prepareWrapper(bool $safe, ?string $titleKey = null) : DataWrapper
    {
        $data = [
            'field1'=>'value1',
            'field2'=>'value2',
            'obj'=>(object) ['field1'=>'value1','field2'=>'value2'],
            'array'=>['field1'=>'value1','field2'=>'value2'],
            'arrayObj'=> new ArrayObject(['field1'=>'value1','field2'=>'value2'])
        ];
        return new DataWrapper($data, $safe, $titleKey);
    }
    
    public function testSafe()
    {
        $wrapper = $this->prepareWrapper(TRUE);
        //
        $this->assertEquals($wrapper['field1'], 'value1');
        $this->assertEquals($wrapper['obj.field2'], 'value2');
        $this->assertEquals($wrapper['array.field1'], 'value1');
        $this->assertEquals($wrapper['arrayObj.field1'], 'value1');
        $this->assertEquals($wrapper['arrayObj.count()'], 2);
        //
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Bad field name notExistField");        
        $wrapper['obj.notExistField'];
    }
    
    public function testNotSafe()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        //
        $this->assertEquals($wrapper['field1'], 'value1');
        $this->assertEquals($wrapper['obj.field2'], 'value2');
        $this->assertEquals($wrapper['array.field1'], 'value1');
        $this->assertEquals($wrapper['arrayObj.field1'], 'value1');
        $this->assertEquals($wrapper['arrayObj.count()'], 2);
        //
        $this->assertEquals($wrapper['obj.notExistField'], null);
        $this->assertEquals($wrapper['obj.notExistMethod()'], null);
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
        $this->assertEquals($wrapper['field1'], 'value1');
        $this->assertEquals($wrapper[$field2], 'value1');
        $this->assertEquals($wrapper[$field3], 2);
        
        $this->assertFalse(isset($wrapper->notExistField));
        $this->assertTrue(isset($wrapper->field1));
        $this->assertFalse(isset($wrapper['notExistField']));
        $this->assertTrue(isset($wrapper['field1']));
        
        $this->assertEquals($wrapper['field1'], 'value1');
        unset($wrapper['field1']);
        $this->assertNull($wrapper['field1']);
        
        $wrapper['someField'] = 'some string';
        $this->assertEquals('some string', $wrapper['someField']);
        
        $this->assertEquals($wrapper->arrayObj->count(), 2);
        $wrapper2 = new DataWrapper(new ArrayObject(['field1'=>'value1','field2'=>'value2']));
        $this->assertEquals($wrapper2->count(), 2);
        $this->assertEquals(json_encode(['field1'=>'value1','field2'=>'value2']), json_encode($wrapper2));
        
        $wrapper3 = new DataWrapper(new dummy\DummyModel(), false);
        $this->assertEquals($wrapper3->getSomeString(), 'some string');
        
        $wrapper3['someField'] = 'some string3';
        $this->assertEquals('some string3', $wrapper3['someField']);
        unset($wrapper3['someField']);
        $this->assertNull($wrapper3['someField']);
        
        $this->assertEquals('Array obj count', $wrapper->fieldLabel($field3));
        $wrapper4 = new DataWrapper($wrapper);
        $this->assertEquals('Array obj count', $wrapper4->fieldLabel($field3));

        require_once dirname(__DIR__, 1).'/src/simple_init.php';
        $wrapper5 = new DataWrapper(json_encode(['field1'=>'value1','field2'=>'value2']));
        $this->assertEquals( 'value1', $wrapper5['#json.field1']);
        $this->assertEquals(json_encode(['field1'=>'value1','field2'=>'value2']), json_encode($wrapper5['#json']));
    }
    
    public function testIterator()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $array = iterator_to_array($wrapper);
        $this->assertArrayHasKey('field1', $array);
        $this->assertEquals('value1',$array['field1']);
        
        $wrapper2 = new DataWrapper(new ArrayObject(['field1'=>'value1']));
        $array2 = iterator_to_array($wrapper2);
        $this->assertArrayHasKey('field1', $array2);
        $this->assertEquals('value1',$array2['field1']);
        
        $wrapper3 = new DataWrapper((object) ['field1'=>'value1']);
        $array3 = iterator_to_array($wrapper3);
        $this->assertArrayHasKey('field1', $array3);
        $this->assertEquals('value1',$array3['field1']);
    }
    
    public function testKeys()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertEquals([
            'field1','field2','obj','array','arrayObj'
        ],$wrapper->keys());
        
        $wrapper2 = new DataWrapper(new ArrayObject(['field1'=>'value1']));
        $this->assertEquals([
            'field1'
        ],$wrapper2->keys());
        
        $wrapper3 = new DataWrapper((object) ['field1'=>'value1']);
        $this->assertEquals([
            'field1'
        ],$wrapper3->keys());
        
        $arrayObj = new ArrayObject(['field1'=>'value1']);
        $wrapper4 = new DataWrapper($arrayObj->getIterator());
        $this->assertEquals([
            'field1'
        ],$wrapper4->keys());
        
        $wrapper5 = new DataWrapper($wrapper2);
        $this->assertEquals([
            'field1'
        ],$wrapper5->keys());
    }
    
    public function testTitle()
    {
        $wrapper0 = $this->prepareWrapper(FALSE);
        $this->assertEquals('Some array...',$wrapper0->title());
        
        $wrapper1 = $this->prepareWrapper(FALSE, 'field1');
        $this->assertEquals('value1',$wrapper1->title());
        
        $arrayObj = new ArrayObject(['field1'=>'value1']);
        $wrapper2 = new DataWrapper($arrayObj);
        $this->assertEquals('Object #'.spl_object_id($arrayObj),$wrapper2->title());
        
        $wrapper3 = new DataWrapper($wrapper2);
        $this->assertEquals('Object #'.spl_object_id($arrayObj),$wrapper3->title());
    }
    
    public function testFieldsInfo()
    {
        $wrapper = $this->prepareWrapper(FALSE);
        $this->assertEquals([
            'field1'=>[],'field2'=>[],'obj'=>[],'array'=>[],'arrayObj'=>[]
        ],$wrapper->fieldsInfo());
        
        $wrapper2 = new DataWrapper($wrapper);
        $this->assertEquals([
            'field1'=>[],'field2'=>[],'obj'=>[],'array'=>[],'arrayObj'=>[]
        ],$wrapper2->fieldsInfo());
    }
}
