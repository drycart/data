<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\CompareHelper;
use PHPUnit\Framework\TestCase;

/**
 * @2DO: add test for compareByOrders
 * @2DO: add test for aliases
 * 
 * @author mendel
 */
class CompareHelperTest extends TestCase
{    
    public function testCheckRule()
    {
        $helper = new CompareHelper();
        $this->assertTrue($helper->compareByRule('<=', 100, 150));
        $this->assertTrue($helper->compareByRule('>=', 100, 80));
        $this->assertTrue($helper->compareByRule('<', 100, 150));
        $this->assertTrue($helper->compareByRule('>', 100, 80));
        $this->assertTrue($helper->compareByRule('=', 100, 100));
        $this->assertTrue($helper->compareByRule('!=', 100, 1));
        $this->assertTrue($helper->compareByRule('like:', 'Hello World!', 'Hello %!'));
        $this->assertTrue($helper->compareByRule('!like:', 'Hello World!', 'Bye %!'));
        $this->assertTrue($helper->compareByRule('contain:', ['a','b','c'], 'b'));
        $this->assertTrue($helper->compareByRule('!contain:', ['a','b','c'], 'd'));
        $this->assertTrue($helper->compareByRule('in:', 'b', ['a','b','c']));
        $this->assertTrue($helper->compareByRule('!in:', 'd', ['a','b','c']));
        //
        $this->assertFalse($helper->compareByRule('<=', 100, 80));
        $this->assertFalse($helper->compareByRule('>=', 100, 150));
        $this->assertFalse($helper->compareByRule('<', 100, 80));
        $this->assertFalse($helper->compareByRule('>', 100, 150));
        $this->assertFalse($helper->compareByRule('=', 100, 1));
        $this->assertFalse($helper->compareByRule('!=', 100, 100));
        $this->assertFalse($helper->compareByRule('like:', 'Hello World!', 'Bue %!'));
        $this->assertFalse($helper->compareByRule('!like:', 'Hello World!', 'Hello %!'));
        $this->assertFalse($helper->compareByRule('contain:', ['a','b','c'], 'd'));
        $this->assertFalse($helper->compareByRule('!contain:', ['a','b','c'], 'b'));
        $this->assertFalse($helper->compareByRule('in:', 'd', ['a','b','c']));
        $this->assertFalse($helper->compareByRule('!in:', 'b', ['a','b','c']));
        //
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Unknown rule notExistRule:");
        $helper->compareByRule('notExistRule:', 'a', 'a');
    }
}
