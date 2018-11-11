<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 16:46
 */

namespace controller;

require_once 'Controller.php';

use config\Config;
use exception\NotFoundUserException;
use utils\UtilsServer;

require_once 'Role.php';
require_once 'NotFoundUserException.php';


class UserController extends Controller
{


   public function __construct($t)
   {
       parent::__construct($t);
   }

    /**
     * @param $db
     * @param $data [userName, password, role]
     */
    public function insertOneUser($db, $data) {
        $sql = "INSERT INTO $this->tableName(username, password, role, token) SELECT * FROM (SELECT ?, ?, ?, ?) AS tmp WHERE NOT EXISTS (SELECT username FROM $this->tableName WHERE username= ?) LIMIT 1; ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, ''.$data[0]);
        $stmt->bindValue(2, ''.$data[1]);
        $stmt->bindValue(3, ''.$data[2]);
        $stmt->bindValue(4, Config::DEFAULT_TOKEN);
        $stmt->bindValue(5, ''.$data[0]);
        $stmt->execute();
    }

    /**
     * @param $db
     * @param $userName
     * @return bool
     */
    public function isExistedUserName($db, $userName) {
        $sql = "SELECT username FROM $this->tableName WHERE username= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $userName);
        $stmt->execute();
        $retUserName = $stmt->fetchAll();

        if(count($retUserName) === 0) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * @param $db
     * @param $userName
     * @return array|null
     */
    public function selectIdPassword($db, $userName) {
        $sql = "SELECT id, password FROM $this->tableName WHERE username= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $userName);
        $stmt->execute();
        $retUserName = $stmt->fetchAll();

        if(count($retUserName) === 1){
            return ['id'=>$retUserName[0][0], 'password'=>$retUserName[0][1]];
        } else {
            return null;
        }
    }
    public function getRole($db, $userName) {
        $sql = "SELECT role FROM $this->tableName WHERE username= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $userName);
        $stmt->execute();
        $ret = $stmt->fetchAll();

        if(count($ret) === 1) {
            return $ret[0][0];
        } else {
            throw new NotFoundUserException();
        }
    }

    public function updateTokenForUserName($db, $userName, $token) {
        $sql = "UPDATE $this->tableName SET token=? WHERE username=?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1,''.$token);
        $stmt->bindValue(2, ''.$userName);
        $stmt->execute();
    }

    public function equalsTokenWithUserName($db, $userName, $token) {
        $sql = "SELECT token FROM $this->tableName WHERE username= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $userName);
        $stmt->execute();
        $ret = $stmt->fetchAll();
        return $ret[0]['token'] === $token;
    }
}