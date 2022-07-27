<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\MetaDataHelper;
use PHPUnit\Framework\TestCase;

/**
 * @author mendel
 */
class MetadataTest extends TestCase
{
    public function testGetSet()
    {
        // Yes, it just for 100% coverage, but thanks to it I find some small bug :)
        $helper = new MetaDataHelper();
        $config = [
            'classMeta' => [
                "Description of DummyModel",
                '',
                "@author mendel"
            ],
            'classRules' => [
                '@author' => [['max']]
            ]
        ];
        $helper->setCache([dummy\DummyModel::class=>$config]);
        $rules = $helper->classRules(dummy\DummyModel::class);
        
        $this->assertEquals('max',$rules['@author'][0][0]);
        $this->assertEquals([dummy\DummyModel::class=>$config],$helper->getCache());
    }
    
    public function testClassRules()
    {
        $helper = new MetaDataHelper();
        $rules = $helper->classRules(dummy\DummyModel::class);
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('@author', $rules);
        $this->assertEquals('mendel',$rules['@author'][0][0]);
    }
    
    public function testDirectFields()
    {
        $helper = new MetaDataHelper();
        $fields = $helper->fieldsRules(dummy\DummyModel::class);
        
        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('age', $fields);
        
        $this->assertIsArray($fields['name']);
        $this->assertCount(1, $fields['name']);
        $this->assertArrayHasKey('@var', $fields['name']);
        $this->assertEquals([['string','name']],$fields['name']['@var']);
        
        $this->assertIsArray($fields['age']);
        $this->assertCount(1, $fields['age']);
        $this->assertArrayHasKey('@var', $fields['age']);
        $this->assertEquals([['int','age']],$fields['age']['@var']);
    }
    
    public function testDirectMethods()
    {
        $helper = new MetaDataHelper();
        $methods = $helper->methodsRules(dummy\DummyModel::class);
        
        $this->assertIsArray($methods);
        $this->assertCount(2, $methods);
        $this->assertArrayHasKey('getSomeString', $methods);
        $this->assertArrayHasKey('dehydrate', $methods);
        
        $this->assertIsArray($methods['getSomeString']);
        $this->assertCount(0, $methods['getSomeString']);
        
        $this->assertIsArray($methods['dehydrate']);
        $this->assertCount(3, $methods['dehydrate']);
        $this->assertArrayHasKey('@return', $methods['dehydrate']);
        $this->assertEquals([['array']],$methods['dehydrate']['@return']);
    }
    
    public function testExtendedFields()
    {
        $helper = new MetaDataHelper();
        $fields = $helper->fieldsRules(dummy\DummyExtendedModel::class);
        
        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('age', $fields);
        
        $this->assertIsArray($fields['name']);
        $this->assertCount(1, $fields['name']);
        $this->assertArrayHasKey('@var', $fields['name']);
        $this->assertEquals([['string','name']],$fields['name']['@var']);
        
        $this->assertIsArray($fields['age']);
        $this->assertCount(1, $fields['age']);
        $this->assertArrayHasKey('@var', $fields['age']);
        $this->assertEquals([['int','age']],$fields['age']['@var']);
    }
}
