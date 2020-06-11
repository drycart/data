<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 *
 * @author mendel
 */
interface ModelInterface extends \Countable, \JsonSerializable
{
    public function get(string $name, $default = null);
    public function check(array $conditions) : bool;
    public function keys() : array;
    public function fieldLabel(string $key) : string;
    public function title() : string;
    public function fieldsInfo() : array;
}
