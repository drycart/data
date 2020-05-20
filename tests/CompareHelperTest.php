<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\CompareHelper;

/**
 * @author mendel
 */
class CompareHelperTest extends \PHPUnit\Framework\TestCase
{    
    public function testCheckRule()
    {
        $this->assertTrue(CompareHelper::checkRule('<=', 100, 150));
        $this->assertTrue(CompareHelper::checkRule('>=', 100, 80));
        $this->assertTrue(CompareHelper::checkRule('<', 100, 150));
        $this->assertTrue(CompareHelper::checkRule('>', 100, 80));
        $this->assertTrue(CompareHelper::checkRule('=', 100, 100));
        $this->assertTrue(CompareHelper::checkRule('<>', 100, 1));
        $this->assertTrue(CompareHelper::checkRule('like:', 'Hello World!', 'Hello %!'));
        $this->assertTrue(CompareHelper::checkRule('!like:', 'Hello World!', 'Bye %!'));
        $this->assertTrue(CompareHelper::checkRule('contain:', ['a','b','c'], 'b'));
        $this->assertTrue(CompareHelper::checkRule('!contain:', ['a','b','c'], 'd'));
        $this->assertTrue(CompareHelper::checkRule('in:', 'b', ['a','b','c']));
        $this->assertTrue(CompareHelper::checkRule('!in:', 'd', ['a','b','c']));
        //
        $this->assertFalse(CompareHelper::checkRule('<=', 100, 80));
        $this->assertFalse(CompareHelper::checkRule('>=', 100, 150));
        $this->assertFalse(CompareHelper::checkRule('<', 100, 80));
        $this->assertFalse(CompareHelper::checkRule('>', 100, 150));
        $this->assertFalse(CompareHelper::checkRule('=', 100, 1));
        $this->assertFalse(CompareHelper::checkRule('<>', 100, 100));
        $this->assertFalse(CompareHelper::checkRule('like:', 'Hello World!', 'Bue %!'));
        $this->assertFalse(CompareHelper::checkRule('!like:', 'Hello World!', 'Hello %!'));
        $this->assertFalse(CompareHelper::checkRule('contain:', ['a','b','c'], 'd'));
        $this->assertFalse(CompareHelper::checkRule('!contain:', ['a','b','c'], 'b'));
        $this->assertFalse(CompareHelper::checkRule('in:', 'd', ['a','b','c']));
        $this->assertFalse(CompareHelper::checkRule('!in:', 'b', ['a','b','c']));
    }
    
    public function testFindRulePrefix()
    {
        $this->assertTrue(CompareHelper::checkRule('=', 100, 100));
        
    }

}
