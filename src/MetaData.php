<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;
use drycart\data\StrHelper;

/**
 * Description of MetaData
 *
 * @author mendel
 */
class MetaData
{
    protected $classReflector;
    protected $rules = [
    ];
    
    public function __construct(string $className, array $rules) {
        $this->rules = $rules;
        $this->classReflector = new \ReflectionClass($className);
    }
    
    public function methods() : array {
        $result = [];
        foreach($this->classReflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $line) {
            if(!$line->isStatic()) {
                $name = $line->name;
                $doc = $line->getDocComment();
                $rules = $this->parseDoc($doc);
                $result[$name] = $rules;
            }
        }
        return $result;
    }
    
    public function fields() : array {
        $result = [];
        foreach($this->classReflector->getProperties(\ReflectionProperty::IS_PUBLIC) as $line) {
            if(!$line->isStatic()) {
                $name = $line->name;
                $doc = $line->getDocComment();
                $rules = $this->parseDoc($doc);
                $result[$name] = $rules;
            }
        }
        return $result;
    }
    
    protected function parseDoc(string $doc) {
        $result = [];
        foreach(StrHelper::parseDocComment($doc) as $line) {
            $data = explode(' ', $line);
            $key = array_shift($data); // take first
            if(isset($this->rules[$key])) {
                $rule = $this->rules[$key];
                $result[$rule][] = $data;                
            }
        }
        return $result;
    }    
}
