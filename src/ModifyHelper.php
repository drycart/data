<?php

namespace drycart\data;

class ModifyHelper
{
    /**
     * Modifiers map
     * @var array<string, callable>
     */
    protected static $modifiers = [];

    /**
     * Add some modifier at modifiers map
     * used for configure modifiers
     *
     * @param string $id
     * @param callable $modifier
     */
    public static function addModifier(string $id, callable $modifier): void
    {
        self::$modifiers[$id] = $modifier;
    }

    public static function modify($data, string $modifier, $modifierParam = null, array $extraData = [])
    {
        $modifierCallback = self::$modifiers[$modifier] ?? $modifier;
        return call_user_func($modifierCallback, $data, $modifierParam, $extraData);
    }
}
