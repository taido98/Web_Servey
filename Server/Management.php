<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:26
 */

namespace management;

use exception\NotFoundUserException;

chdir(dirname(__DIR__));
require_once 'NotFoundUserException.php';

abstract class Management
{
    protected $role, $userName;

    abstract public function process();

    /**
     * @param $db
     * @param $userController
     * @return bool
     */
    protected function checkRole($db, $userController)
    {
        try {
            $retRole = $userController->getRole($db, $this->userName);
            return $retRole === $this->role ? true : false;

        } catch (NotFoundUserException $e) {
            return false;
        }
    }

    protected function checkTokenOfThisUser($db, $userController)
    {
        $jwt = $_GET['jwt'];

        if ($jwt) {
            // check token is of this username
            return $userController->equalsTokenWithUserName($db, $this->userName, $jwt);
        } else {
            return false;
        }
    }
}