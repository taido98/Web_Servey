<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 19:40
 */

namespace exception;


use Complex\Exception;

class MoreThanOneIdClassInDatabase extends Exception
{
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage().'</b> more than one id class in database';
        return $errorMsg;
    }
}