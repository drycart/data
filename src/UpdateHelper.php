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
    public const RULES = ['set:', 'add:', 'max:', 'min:'];

    public function update(&$data, array $changes): void
    {
        foreach ($changes as $key => $value) {
            [$rule, $fieldName] = StrHelper::findPrefix($key, static::RULES, 'set:');
            $this->updateField($data, $value, $rule, $fieldName);
        }
    }

    protected function updateField(&$data, $value, string $rule, string $fieldName): void
    {
        $current = $data[$fieldName];
        switch ($rule) {
            case 'set:':
                $data[$fieldName] = $value;
                break;
            case 'add:':
                $data[$fieldName] = ($current ?? 0) + $value;
                break;
            case 'max:':
                $data[$fieldName] = max([$current, $value]);
                break;
            case 'min:':
                $data[$fieldName] = min([$current, $value]);
                break;
            default:
                throw new \Exception('Unknown type ' . $rule);
        }
    }
}
