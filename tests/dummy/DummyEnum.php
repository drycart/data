<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests\dummy;

use drycart\data\EnumTrait;
/**
 * Simple enum example for test
 *
 * @author mendel
 */
class DummyEnum
{
    use EnumTrait;
    
    const GUEST = 0;
    const USER = 1;
    const ADMIN = 10;
}
