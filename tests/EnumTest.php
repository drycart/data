<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\tests\dummy\DummyEnum;
use drycart\data\tests\dummy\DummyExtendedEnum;

/**
 * @author mendel
 */
class EnumTest extends \PHPUnit\Framework\TestCase
{
    public function testData()
    {
        $this->assertEquals(DummyEnum::data(),['GUEST'=>0, 'USER'=>1, 'ADMIN'=>10]);
        $this->assertEquals(DummyExtendedEnum::data(),['GUEST'=>0, 'USER'=>1, 'ADMIN'=>10, 'SUPER_ADMIN'=>100]);
    }
    
    public function testKeyValue()
    {
        $this->assertEquals(DummyEnum::value('ADMIN'),10);
        $this->assertEquals(DummyEnum::key(10),'ADMIN');
    }
    
    public function testKeyTitles()
    {
        $this->assertEquals(DummyEnum::keyTitles(),['GUEST'=>'Guest', 'USER'=>'User', 'ADMIN'=>'Admin']);
        $this->assertEquals(DummyExtendedEnum::keyTitles(),['GUEST'=>'Guest', 'USER'=>'Default user', 'ADMIN'=>'Admin', 'SUPER_ADMIN'=>'Super admin']);
    }
    
    public function testValueTitles()
    {
        $this->assertEquals(DummyEnum::valueTitles(),[0=>'Guest', 1=>'User', 10=>'Admin']);
        $this->assertEquals(DummyExtendedEnum::valueTitles(),[0=>'Guest', 1=>'Default user', 10=>'Admin', 100=>'Super admin']);
    }
    
    public function testTitlesIterator()
    {
        $arr = iterator_to_array(DummyEnum::titlesIterator());
        $this->assertCount(3, $arr);
        $this->assertEquals('GUEST', $arr[0]->key);
        $this->assertEquals(0, $arr[0]->value);
        $this->assertEquals('Guest', $arr[0]->title);
        
        $this->assertEquals('USER', $arr[1]->key);
        $this->assertEquals(1, $arr[1]->value);
        $this->assertEquals('User', $arr[1]->title);
        
        $this->assertEquals('ADMIN', $arr[2]->key);
        $this->assertEquals(10, $arr[2]->value);
        $this->assertEquals('Admin', $arr[2]->title);
    }
}
