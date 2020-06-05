<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 * Wrapper for pretty access to field
 * Used for deep access to some data at some unknown data
 *
 * fieldName for example forum.moderators.first().name work correct for any of this sence:
 * $data->forum->moderators->first()->name
 * $data['forum']['moderators']->first()['name']
 * $data['forum']->moderators->first()['name']
 * etc...
 *
 * Object field is priority option, second is array, after this we try method,
 * so if exist something like this, it will be used
 * $data['forum']->moderators['first()']['name']
 *
 * methods parameters not supports at any format
 */
class GetterHelper
{
    /**
     * Get some data by pretty name
     * 
     * @param array|object $data data for pretty access
     * @param string $name name for access
     * @param bool $safe if true - Exception for not exist fields
     * @param mixed $default used for non safe request, if we dont find answer
     * @return mixed
     * @throws \Exception
     */
    public static function get($data, string $name, bool $safe = true, $default = null) {
        $fields = explode('.', $name);
        foreach ($fields as $key) {
            if (is_array($data)) { // Just array, because ArrayAccess can have his own logic as object field
                $data = (object) $data;
            }
            //
            if (isset($data->{$key})) { // simple
                $data = $data->{$key};
            } elseif (is_a($data, \ArrayAccess::class) and isset($data[$key])) { // for ArrayAccess obj
                $data = $data[$key];
                // Methods magic...
            } elseif ((substr($key, -2) == '()') and method_exists($data, substr($key, 0, -2))) {
                $data = call_user_func_array([$data, substr($key, 0, -2)], []);
            } elseif ($safe) {
                throw new \Exception("Bad field name $key at name $name fields");
            } else {
                return $default;
            }
        }
        return $data;
    }
}
