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
    /**
     * Get keys list
     * 
     * @return string[]
     */
    public function keys() : array;
    
    /**
     * Human readable name for some field (by key)
     * 
     * @param string $key
     * @return string
     */
    public function fieldLabel(string $key) : string;
    
    /**
     * Title for model
     * for example some uniq name etc 
     * 
     * @return string
     */
    public function title() : string;
    
    /**
     * Fields, and his metadata (type etc)
     * 
     * @return array
     */
    public function fieldsInfo() : array;
}
