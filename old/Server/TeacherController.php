<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 18:28
 */



namespace controller;
use config\Config;

require_once 'Controller.php';
require_once 'UserController.php';
require_once 'config/Config.php';

class TeacherController extends Controller
{
    public function __construct($t)
    {
        parent::__construct($t);
    }

    public function insertTeachers($dsn, $username, $passwd, $data) {
        $db = null;
        $userController = new UserController(Config::USER_TABLE_NAME);
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for ($i = 0; $i < count($data); ++$i) {

                // insert into user table

                $usrName = $data[$i][0];
                $passwd = $data[$i][1];

                $userController->insertOneUser($db, [$usrName, $passwd, Role::TEACHER]);
                $sql = "INSERT INTO $this->tableName(idTeacher, idUser , fullName, vnuEmail)  SELECT * FROM (SELECT ?, (SELECT id from user WHERE username=?), ?, ?) AS tmp WHERE NOT EXISTS (SELECT idTeacher FROM $this->tableName WHERE idTeacher = ?) LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, '' . $data[$i][4]);
                $stmt->bindValue(2, '' . $usrName);
                $stmt->bindValue(3, '' . $data[$i][2]);
                $stmt->bindValue(4, '' . $data[$i][3]);
                $stmt->bindValue(5, '' . $data[$i][4]);
                $stmt->execute();
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
    public function getTeacherId($db, $teacherId) {
        $sql = "SELECT id FROM $this->tableName WHERE idTeacher= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $teacherId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}