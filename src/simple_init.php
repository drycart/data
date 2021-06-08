<?php
/*
 *  @copyright (c) 2021 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

use Carbon\Carbon;
use drycart\data\GetterHelper;

/*
 * Example fof config helpers
 * usage: require_once 'simple_init,php';
 */

GetterHelper::addModifier('json', function($data) {
    return json_decode($data, true);
});
GetterHelper::addModifier('prettyTimeDiff', function($data) {
    $time = new Carbon($data);
    return $time->diffForHumans();
});
