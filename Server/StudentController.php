<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 17:03
 */

namespace controller;




use config\Config;


require_once 'UserController.php';
require_once 'Role.php';
require_once 'Controller.php';
require_once 'config/Config.php';



class StudentController extends Controller
{
    public function __construct($t)
    {
        parent::__construct($t);
    }

    /**
     * @param $dsn
     * @param $username
     * @param $passwd
     * @param $data
     */
    public function insertStudents($dsn, $username, $passwd, $data) {
        $db = null;
        $userController = new UserController(Config::USER_TABLE_NAME);
        try{
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for($i = 0; $i < count($data); ++$i) {

                print_r($data[$i]);

                $this->insertOneStudent($db, $userController, $data[$i]);

            }
            $db->commit();
        } catch (\PDOException $e) {
            if($db !== null) {
                $db->rollBack();
            }
            echo "Error: " .$e->getMessage();
        } finally {
            if($db !== null) {
                $db = null;
            }

        }
    }

    /**
     * @param $db
     * @param $idStudent
     * @return mixed
     */
    public function getStudentId($db, $idStudent) {
        $sql = "SELECT id FROM $this->tableName WHERE idStudent= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $idStudent);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param $db
     * @param $userController
     * @param $data
     */
    public function insertOneStudent($db, $userController, $data) {
        $userController->insertOneUser($db, [$data[0], $data[1], Role::STUDENT]);

        $sql ="INSERT INTO $this->tableName(idStudent, idUser , fullName, vnuEmail, courses)  SELECT * FROM (SELECT ?, (SELECT id from user WHERE username=?), ?, ?, ?) AS tmp WHERE NOT EXISTS (SELECT idStudent FROM $this->tableName WHERE idStudent = ?) LIMIT 1;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, ''.$data[0]);
        $stmt->bindValue(2, ''.$data[0]);
        $stmt->bindValue(3, ''.$data[2]);
        $stmt->bindValue(4, ''.$data[3]);
        $stmt->bindValue(5, ''.$data[4]);
        $stmt->bindValue(6, ''.$data[0]);
        $stmt->execute();
    }
}