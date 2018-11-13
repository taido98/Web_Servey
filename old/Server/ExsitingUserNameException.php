<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 20:02
 */

namespace exception;


use Complex\Exception;

class ExsitingUserNameException extends Exception
{
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage().'</b> user name already exist';
        return $errorMsg;
    }
}