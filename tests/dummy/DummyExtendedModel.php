<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\tests\dummy;

/**
 * Description of DummyExtendedModel
 *
 * @author mendel
 */
class DummyExtendedModel extends DummyModel
{
    /**
     * Fake selfMake method...
     * @return self
     */
    public static function make() : self
    {
        return new static;
    }
}
