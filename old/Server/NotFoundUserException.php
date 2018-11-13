<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 13:51
 */

namespace exception;

chdir(dirname(__DIR__));
require_once 'lib/vendor/autoload.php';
use Complex\Exception;

class NotFoundUserException extends Exception
{

}