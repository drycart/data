<?php
/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Description of UpdateHelper
 *
 * @author mendel
 */
class UpdateHelper
{
    // Dont change order - longer will be first (before other started from same symbols)
    const RULES = ['set:', 'sum:', 'max:', 'min:'];
    
    public static function updateAllFields(&$data, array $changes) : void
    {
        foreach($changes as $key=>$value) {
            [$rule, $fieldName] = StrHelper::findPrefix($key, static::RULES, 'set:');
            static::updateField($data, $value, $rule, $fieldName);
        }
    }
    
    public static function updateField(&$data, $value, string $rule, string $fieldName) : void
    {
        $current = $data[$fieldName];
        switch ($rule) {
            case 'set:':
                $data[$fieldName] = $value;
                break;
            case 'sum:':
                $data[$fieldName] = ($current ?? 0) + $value;
                break;
            case 'max:':
                $data[$fieldName] = max([$current, $value]);
                break;
            case 'min:':
                $data[$fieldName] = min([$current, $value]);
                break;
            default:
                throw new \Exception('Unknown type '.$rule);
        }
    }
}
