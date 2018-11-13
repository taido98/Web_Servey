<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:04
 */

namespace utils;


class UtilsServer
{
    public static function equalsPasswordHash($psw,  $hash) {

    }

    public static function equalsTwoPassword($psw1, $psw2) {
        return $psw1 === $psw2;
    }
    public static function clean($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}