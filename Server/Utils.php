<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 20:36
 */

namespace utils;


class Utils
{
    public static function getOnlyIdNumberString($idString) {
        return preg_replace('/[^0-9]/', '', $idString);
    }
}