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
        $this->assertEquals(
            $helper->fields(),
            [
                'name'=>['var'=>[['string','name']]],
                'age'=>['var'=>[['int','age']]]
            ]
        );
        $this->assertEquals(
            $helper->methods(),
            [
                'hydrate'=>['return'=>[['void']],'param'=>[['array', '$data']]],
            ]
        );
    }
}
