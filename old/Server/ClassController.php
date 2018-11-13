<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 07/11/2018
 * Time: 19:44
 */

namespace controller;

use config\Config;
use excel\Excel;
use exception\InvalidClassException;
use exception\InvalidStudentException;
use exception\NotFoundTeacherAccount;

require_once 'Controller.php';
require_once 'TeacherController.php';
require_once 'UserController.php';
require_once 'SurveyController.php';
require_once 'StudentController.php';


class ClassController extends Controller
{

    public function __construct($t)
    {
        parent::__construct($t);
    }

    public function insertClass($dsn, $username, $passwd, $data) {
        $db = null;
        $teacherController = new TeacherController(Config::TEACHER_TABLE_NAME);
        $userController = new UserController(Config::USER_TABLE_NAME);
        $surVeyController = new SurveyController(Config::SURVEY_TABLE_NAME);
        $studentController = new StudentController(Config::STUDENT_TABLE_NAME);
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            $idTeacher = $data[Excel::TeacherId];
            $teacherName = $data[Excel::TeacherName];
            $idClass = $data[Excel::SubSubjectId];
            $idSubject = $data[Excel::SubjectId];
            $subjectName = $data[Excel::SubjectName];
            $location = $data[Excel::LectureLocation];
            $numLession = $data[Excel::TinChi];


            $idTeacherDataBase = $teacherController->getTeacherId($db, $idTeacher);


            if(count($idTeacherDataBase) === 1) {
                $sql = "INSERT INTO $this->tableName(idClass , idTeacher, idSubject, subjectName, location, numberLession)  SELECT * FROM (SELECT :idClass, :idTeacher, :idSubject, :subjectName, :location, :numberLession) AS tmp WHERE NOT EXISTS (SELECT idClass FROM $this->tableName WHERE idClass= :idClass) LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':idClass', $idClass);
                $stmt->bindParam(':idTeacher', $idTeacherDataBase[0][0]);
                $stmt->bindParam(':idSubject', $idSubject);
                $stmt->bindParam(':subjectName', $subjectName);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':numberLession', $numLession);
                $stmt->execute();

                $dataStudent = $data[Excel::Data];
                for ($i = 0; $i < count($dataStudent); ++$i) {
                    $idStudent = $dataStudent[$i][0];

                    $idStudentDB = $studentController->getStudentId($db, $idStudent);

                    if(count($idStudentDB) === 0) {
                        $studentController->insertOneStudent($db, $userController, [$idStudent, Config::DEFAULT_PASSWORD, $dataStudent[$i][1],  $idStudent.'@vnu.edu.vn', $dataStudent[$i][3]]);
                    } else if(count($idStudentDB) >= 2) {
                        throw new InvalidStudentException();
                    }


                    $idStudentDB = $studentController->getStudentId($db, $idStudent);

                    $idClassDB = $this->getIdClass($db, $idClass);

                    if(count($idClassDB) === 1) {
                        $surVeyController->insertSurvey($db, $idClassDB[0][0], $idStudentDB[0][0]);
                    } else {
                        throw new InvalidClassException();
                    }






                }
            } else {
                throw new NotFoundTeacherAccount();
            }



            for ($i = 0; $i < count($data); ++$i) {


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

    public function getIdClass($db, $idClass) {
        $sql = "SELECT id FROM $this->tableName WHERE idClass= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $idClass);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getIdClassName($db, $idClassDB) {
        $sql = "SELECT idClass, subjectName FROM $this->tableName WHERE id= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $idClassDB);
        $stmt->execute();
        $ret = $stmt->fetchAll();

        return   $ret[0];

    }

}