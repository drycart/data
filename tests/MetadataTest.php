<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\MetaData;

/**
 * @author mendel
 */
class MetadataTest extends \PHPUnit\Framework\TestCase
{
    public function testDirectFields()
    {
        $helper = new MetaData(
            dummy\DummyModel::class,
            ['@var'=>'var','@param'=>'param', '@return'=>'return']);
        $fields = $helper->fields(true);
        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('age', $fields);
        
        $this->assertIsArray($fields['name']);
        $this->assertCount(1, $fields['name']);
        $this->assertArrayHasKey('var', $fields['name']);
        $this->assertEquals([['string','name']],$fields['name']['var']);
        
        $this->assertIsArray($fields['age']);
        $this->assertCount(1, $fields['age']);
        $this->assertArrayHasKey('var', $fields['age']);
        $this->assertEquals([['int','age']],$fields['age']['var']);
    }
    public function testDirectMethods()
    {
        $helper = new MetaData(
            dummy\DummyModel::class,
            ['@var'=>'var','@param'=>'param', '@return'=>'return']);
        
        $this->assertEquals(
            $helper->methods(true),
            [
                'hydrate'=>['return'=>[['void']],'param'=>[['array', '$data']]],
            ]
        );
    }
    
    public function testExtendedFields()
    {
        $helper = new MetaData(
            dummy\DummyExtendedModel::class,
            ['@var'=>'var','@param'=>'param', '@return'=>'return']);
        $fields = $helper->fields(true);
        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('age', $fields);
        
        $this->assertIsArray($fields['name']);
        $this->assertCount(1, $fields['name']);
        $this->assertArrayHasKey('var', $fields['name']);
        $this->assertEquals([['string','name']],$fields['name']['var']);
        
        $this->assertIsArray($fields['age']);
        $this->assertCount(1, $fields['age']);
        $this->assertArrayHasKey('var', $fields['age']);
        $this->assertEquals([['int','age']],$fields['age']['var']);
    }
    
    public function testExtendedMethods()
    {
        $helper = new MetaData(
            dummy\DummyExtendedModel::class,
            ['@var'=>'var','@param'=>'param', '@return'=>'return']);
        
        $this->assertEquals(
            $helper->methods(true),
            [
                'hydrate'=>['return'=>[['void']],'param'=>[['array', '$data']]],
            ]
        );
    }
    
    public function testClassRules()
    {
        $helper = new \drycart\data\MetaDataHelper();
        $rules = $helper->classRules(dummy\DummyModel::class);
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('@author', $rules);
        $this->assertEquals('mendel',$rules['@author'][0][0]);
    }
    
    public function testGetSet()
    {
        // Yes, it just for 100% coverage
        $helper = new \drycart\data\MetaDataHelper();
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
        var_dump($helper->getCache());
        $this->assertEquals('max',$rules['@author'][0][0]);
        $this->assertEquals([dummy\DummyModel::class=>$config],$helper->getCache());
    }
}
