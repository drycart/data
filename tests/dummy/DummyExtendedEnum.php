<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests\dummy;

/**
 * Extended enum example for test
 *
 * @author mendel
 */
class DummyExtendedEnum extends DummyEnum
{
    const SUPER_ADMIN = 100;
    
    protected static function titles(): array
    {
        return ['USER'=>'Default user'];
    }
}
