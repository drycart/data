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
    public function testToString()
    {
        $helper = new MetaData(
            dummy\DummyExtendedModel::class,
            ['@var'=>'var','@param'=>'param', '@return'=>'return']);
        $fields = $helper->fields();
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
        
        $this->assertEquals(
            $helper->methods(),
            [
                'hydrate'=>['return'=>[['void']],'param'=>[['array', '$data']]],
            ]
        );
    }
}
