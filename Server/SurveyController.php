<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 19:46
 */

namespace controller;
require_once 'Controller.php';

class SurveyController extends Controller
{
    public function __construct($t)
    {
        parent::__construct($t);
    }

    public function insertSurvey($db, $idClass, $idStudent) {
        $sql = "INSERT INTO $this->tableName(idClass, idStudent) SELECT * FROM (SELECT ?, ?) AS tmp WHERE NOT EXISTS (SELECT * FROM $this->tableName WHERE idClass= ? AND idStudent = ?) LIMIT 1; ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, ''.$idClass);
        $stmt->bindValue(2, ''.$idStudent);
        $stmt->bindValue(3, ''.$idClass);
        $stmt->bindValue(4, ''.$idStudent);
        $stmt->execute();
    }

}