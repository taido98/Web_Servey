<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 16/11/2018
 * Time: 21:32
 */

namespace App\Utils;


class Utils
{
    public static function encodeToJSON(array $data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}