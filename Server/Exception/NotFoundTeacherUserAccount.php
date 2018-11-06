<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 06/11/2018
 * Time: 10:46
 */

namespace exception;

use Complex\Exception;

class NotFoundTeacherUserAccount extends Exception
{


    public function errorMessage()
    {
        //error message
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
            . ': <b>' . $this->getMessage() . '</b> is not found teacher count';
        return $errorMsg;
    }
}