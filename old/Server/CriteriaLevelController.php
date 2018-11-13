<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 09/11/2018
 * Time: 14:49
 */

namespace controller;
require_once 'Controller.php';

class CriteriaLevelController extends Controller
{
    public function __construct($t)
    {
        parent::__construct($t);
    }


    /**
     * @param $db
     * @return array [id=>name, ...]
     */
    public function getAllData($db) {
        $sql = "SELECT * FROM $this->tableName";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $retSQL =  $stmt->fetchAll();
//        print_r($retSQL);

        $retArr = [];

        for($i = 0; $i < count($retSQL); ++$i) {
            $retArr[$retSQL[$i][0]] = $retSQL[$i][1];
        }
        return  $retArr;
    }

    /**
     * @param $dsn
     * @param $username
     * @param $password
     * @param $data
     */
    public function insertCriteria($dsn, $username, $password, $data) {
        $db = null;
        try {
            $db = new \PDO($dsn, $username, $password);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();
            for($i = 0; $i < count($data); ++$i) {

                $sql = "SELECT id FROM $this->tableName WHERE name = :name";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':name', $data[$i][0]);
                $stmt->execute();

                $retSQL = $stmt->fetchAll();
                if(count($retSQL) === 0) {
                    $sql = "INSERT INTO $this->tableName(name)  SELECT * FROM (SELECT :name) AS tmp WHERE NOT EXISTS (SELECT name FROM $this->tableName WHERE name = :name) LIMIT 1;";

                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':name', $data[$i][0]);
                    $stmt->execute();
                } else {
                    print_r("already exist name");
                }




            }


            $db->commit();
        } catch (\PDOException $e) {
            if ($db !== null) {
                $db->rollBack();
            }
            print_r($e);
        } finally {
            if ($db !== null) {
                $db = null;
            }

        }
    }

}