<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\CompareHelper;

/**
 * @2DO: add test for compareByOrders
 * @2DO: add test for aliases
 * 
 * @author mendel
 */
class CompareHelperTest extends \PHPUnit\Framework\TestCase
{    
    public function testCheckRule()
    {
        $this->assertTrue(CompareHelper::compareByRule('<=', 100, 150));
        $this->assertTrue(CompareHelper::compareByRule('>=', 100, 80));
        $this->assertTrue(CompareHelper::compareByRule('<', 100, 150));
        $this->assertTrue(CompareHelper::compareByRule('>', 100, 80));
        $this->assertTrue(CompareHelper::compareByRule('=', 100, 100));
        $this->assertTrue(CompareHelper::compareByRule('!=', 100, 1));
        $this->assertTrue(CompareHelper::compareByRule('like:', 'Hello World!', 'Hello %!'));
        $this->assertTrue(CompareHelper::compareByRule('!like:', 'Hello World!', 'Bye %!'));
        $this->assertTrue(CompareHelper::compareByRule('contain:', ['a','b','c'], 'b'));
        $this->assertTrue(CompareHelper::compareByRule('!contain:', ['a','b','c'], 'd'));
        $this->assertTrue(CompareHelper::compareByRule('in:', 'b', ['a','b','c']));
        $this->assertTrue(CompareHelper::compareByRule('!in:', 'd', ['a','b','c']));
        //
        $this->assertFalse(CompareHelper::compareByRule('<=', 100, 80));
        $this->assertFalse(CompareHelper::compareByRule('>=', 100, 150));
        $this->assertFalse(CompareHelper::compareByRule('<', 100, 80));
        $this->assertFalse(CompareHelper::compareByRule('>', 100, 150));
        $this->assertFalse(CompareHelper::compareByRule('=', 100, 1));
        $this->assertFalse(CompareHelper::compareByRule('!=', 100, 100));
        $this->assertFalse(CompareHelper::compareByRule('like:', 'Hello World!', 'Bue %!'));
        $this->assertFalse(CompareHelper::compareByRule('!like:', 'Hello World!', 'Hello %!'));
        $this->assertFalse(CompareHelper::compareByRule('contain:', ['a','b','c'], 'd'));
        $this->assertFalse(CompareHelper::compareByRule('!contain:', ['a','b','c'], 'b'));
        $this->assertFalse(CompareHelper::compareByRule('in:', 'd', ['a','b','c']));
        $this->assertFalse(CompareHelper::compareByRule('!in:', 'b', ['a','b','c']));
        //
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Unknown rule notExistRule:");
        CompareHelper::compareByRule('notExistRule:', 'a', 'a');
    }
}
