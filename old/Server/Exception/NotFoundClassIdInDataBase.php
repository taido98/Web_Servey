<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 19:38
 */

namespace exception;


use Complex\Exception;

class NotFoundClassIdInDataBase extends Exception
{
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage().'</b> not found class id in database';
        return $errorMsg;
    }
}