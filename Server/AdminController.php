<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 19:45
 */




namespace controller;

use config\Config;
use exception\ExsitingUserNameException;

require_once 'Controller.php';
require_once 'UserController.php';
require_once 'config/Config.php';
require_once 'ExsitingUserNameException.php';

class AdminController extends Controller
{

    public function __construct($t)
    {
        parent::__construct($t);
    }


    public function insertAdmins($dsn, $username, $passwd, $data) {
        $db = null;
        $userController = new UserController(Config::USER_TABLE_NAME);
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for ($i = 0; $i < count($data); ++$i) {


                $usrName = $data[$i][0];
                $passwd = $data[$i][1];

                //check if exist



                $retValue = $userController->isExistedUserName($db, $usrName);


                if($retValue === true) {
                    throw new ExsitingUserNameException();
                } else {
                    $userController->insertOneUser($db, [$usrName, $passwd, Role::ADMIN]);
                }
            }
            $db->commit();
        } catch (\PDOException $e) {
            if ($db !== null) {
                $db->rollBack();
            }
            echo "Error: " . $e->getMessage();
        } finally {
            if ($db !== null) {
                $db = null;
            }

        }
    }
}