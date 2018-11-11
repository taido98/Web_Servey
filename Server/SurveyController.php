<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 19:46
 */

namespace controller;
use config\Config;

require_once 'Controller.php';
require_once 'CriteriaLevelController.php';
require_once 'ClassController.php';

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
    public function getAllClassInformOfStudent($db, $idStudentDB) {
        $sql = "SELECT idClass, content FROM $this->tableName WHERE idStudent= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $idStudentDB);
        $stmt->execute();
        $retDB = $stmt->fetchAll();
        $retArrClass = [];

        $classController = new ClassController(Config::CLASS_TABLE_NAME);


        for($i = 0; $i < count($retDB); ++$i) {
            $retArrClass[$i] = ['formSurvey' => ''.htmlentities($retDB[$i]['content'])];

            $retClassDB = $classController->getIdClassName($db, $retDB[$i]['idClass']);

            $retArrClass[$i]['idClass'] = $retClassDB['idClass'];
            $retArrClass[$i]['name'] = $retClassDB['subjectName'];
        }
        return $retArrClass;

    }

}