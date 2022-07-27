<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests;

use drycart\data\UpdateHelper;
use PHPUnit\Framework\TestCase;

/**
 * Description of UpdateHelperTest
 *
 * @author mendel
 */
class UpdateHelperTest extends TestCase
{
    public function testUpdate()
    {
        $data = new \ArrayObject([
            'a'=>1,
            'b'=>100,
            'c'=>-100,
            'd'=>100500,
            'e'=>10,
        ]);

        $helper = new UpdateHelper();
    
        $helper->update($data, [
            'a'=>2,
            'set:b'=>0,
            'min:c'=>0,
            'max:d'=>100,
            'add:e'=>5
        ]);
        
        $this->assertEquals(2, $data['a']);
        $this->assertEquals(0, $data['b']);
        $this->assertEquals(-100, $data['c']);
        $this->assertEquals(100500, $data['d']);
        $this->assertEquals(15, $data['e']);
    }
}
