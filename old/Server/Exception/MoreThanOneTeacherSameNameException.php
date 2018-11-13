<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 10:40
 */

namespace exception;


use Complex\Exception;

class MoreThanOneTeacherSameNameException extends Exception
{
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage().'</b> is not a valid teacher name; more than one teacher same name';
        return $errorMsg;
    }
}