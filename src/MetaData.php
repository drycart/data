<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Metadata for some class
 * i.e. some data from comments for class, fields, methods
 * 
 * @deprecated
 * #see MetaDataHelper
 * 
 * @author mendel
 */
class MetaData
{
    protected static $helper;
    protected $className;
    protected $rules = [
    ];
    
    public function __construct(string $className, array $rules) {
        $this->rules = $rules;
        $this->className = $className;
    }
    
    protected static function getHelper() : MetaDataHelper
    {
        if(!isset(static::$helper)) {
            static::$helper = new MetaDataHelper();
        }
        return static::$helper;
    }


    public function methods() : array {
        $rules = self::getHelper()->methodsRules($this->className);
        return $this->filterRules($rules, $this->rules);
    }
    
    public function fields() : array {
        $rules = self::getHelper()->fieldsRules($this->className);
        return $this->filterRules($rules, $this->rules);
    }
    
    protected function filterRules(array $data, array $rules) : array
    {
        $result = [];
        foreach($data as $name=>$line) {
            foreach($line as $key=>$value) {
                if(isset($rules[$key])) {
                    $rule = $rules[$key];
                    $result[$name][$rule] = $value;
                }
            }
        }
        return $result;
    }
}
