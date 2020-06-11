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
 * @author mendel
 */
class MetaDataHelper
{
    /**
     * Session cache
     * @var array
     */
    protected $cache = [];
    
    /**
     * Get current cache data for store to external cache
     * @return array
     */
    public function getCache() : array
    {
        return $this->cache;
    }
    
    /**
     * Set cache data from external cache
     * @param array $cache
     * @return void
     */
    public function setCache(array $cache) : void
    {
        $this->cache = $cache;
    }
    
    /**
     * Metadata for class
     * 
     * @param string $className
     * @return array
     */
    public function classMeta(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $classReflector = new \ReflectionClass($className);
            $doc = $classReflector->getDocComment();
            $this->cache[$className][__METHOD__] = StrHelper::parseDocComment($doc);
        }
        return $this->cache[$className][__METHOD__];
    }
    
    /**
     * Get rules for class
     * 
     * @param string $className
     * @return array
     */
    public function classRules(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $this->cache[$className][__METHOD__] = $this->prepareRules($this->classMeta($className));
        }
        return $this->cache[$className][__METHOD__];
    }
    
    /**
     * Metadata for methods
     * meta at name for future add return type info
     * 
     * @param string $className
     * @return array[]
     */
    public function methodsMeta(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $this->cache[$className][__METHOD__] = [];
            $classReflector = new \ReflectionClass($className);
            foreach($classReflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $line) {
                if(!$line->isStatic()) {
                    $doc = $line->getDocComment();
                    $this->cache[$className][__METHOD__][$line->name] = StrHelper::parseDocComment($doc);
                }
            }
        }
        return $this->cache[$className][__METHOD__];
    }

    /**
     * Get methods rules
     * 
     * @param string $className
     * @return array
     */
    public function methodsRules(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $this->cache[$className][__METHOD__] = $this->prepareRulesArray($this->methodsMeta($className));
        }
        return $this->cache[$className][__METHOD__];
    }
    
    /**
     * Metadata for fields
     * meta at name for future add return type info
     * 
     * @param string $className
     * @return array
     */
    public function fieldsMeta(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $this->cache[$className][__METHOD__] = [];
            $classReflector = new \ReflectionClass($className);
            foreach($classReflector->getProperties(\ReflectionProperty::IS_PUBLIC) as $line) {
                if(!$line->isStatic()) {
                    $doc = $line->getDocComment();
                    $this->cache[$className][__METHOD__][$line->name] = StrHelper::parseDocComment($doc);
                }
            }
        }
        return $this->cache[$className][__METHOD__];
    }

    /**
     * Get fields rules
     * 
     * @param string $className
     * @return array
     */
    public function fieldsRules(string $className) : array
    {
        if(!isset($this->cache[$className][__METHOD__])) {
            $this->cache[$className][__METHOD__] = $this->prepareRulesArray($this->fieldsMeta($className));
        }
        return $this->cache[$className][__METHOD__];
    }
    
    protected function prepareRulesArray(array $data) : array
    {
        $result = [];
        foreach($data as $name=>$lines) {
            $result[$name] = $this->prepareRules($lines);
        }
        return $result;
    }
    
    /**
     * Prepare rules i.e. array of "meta" parameters group by first word
     * 
     * @param array $lines
     * @return array
     */
    protected function prepareRules(array $lines) : array
    {
        $result = [];
        foreach($lines as $line) {
            $data = explode(' ', $line);
            $key = array_shift($data); // take first
            $result[$key][] = $data;                
        }
        return $result;
    }
}
