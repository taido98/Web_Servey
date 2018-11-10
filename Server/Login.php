<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 09/11/2018
 * Time: 21:21
 */

namespace login;


use manager\Manager;

class Login implements Manager
{

    function process()
    {
        // TODO: Implement process() method.
    }

    function clean($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}