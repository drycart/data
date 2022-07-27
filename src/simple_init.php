<?php

/*
 *  @copyright (c) 2021 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

use Carbon\Carbon;
use drycart\data\ModifyHelper;

/*
 * Example fof config helpers
 * usage: require_once 'simple_init,php';
 */

ModifyHelper::addModifier('json', function ($data) {
    return json_decode($data, true);
});
ModifyHelper::addModifier('prettyTimeDiff', function ($data) {
    $time = new Carbon($data);
    return $time->diffForHumans();
});
