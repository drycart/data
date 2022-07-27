<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

use UnexpectedValueException;

interface ComparableInterface
{
    /**
     * @param object $b
     * @return int
     * @throws UnexpectedValueException
     */
    public function compare($b): int;
}
