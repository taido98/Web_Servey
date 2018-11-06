<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 04/11/2018
 * Time: 16:01
 */

namespace database;
use excel\Excel;
use exception\MoreThanOneTeacherSameNameException;
use exception\NotFoundTeacherUserAccount;


abstract class UserType {
    const student = 0;
    const teacher = 1;
    const admin = 1;

}

class Database
{
    /**
     * @param $dsn
     * @param $username
     * @param $passwd
     */
    public static function connectDatabse($dsn, $username, $passwd) {

        try{
            $db = new \PDO($dsn, $username, $passwd);
            echo "Connected successfully";
            $db = null;

        } catch (\PDOException $e) {
            echo "Erro: " .$e->getMessage();
        }
    }

    /**
     * @param $dsn
     * @param $username
     * @param $passwd
     * @param $data
     */
    public static function insertStudentToDataBase($dsn, $username, $passwd, $data) {
        $db = null;
        try{
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for($i = 0; $i < count($data); ++$i) {

                // insert into user table

                $usrName = $data[$i][0];
                $passwd = $data[$i][1];

                $sql = "INSERT INTO user(username, password) SELECT * FROM (SELECT ?, ?) AS tmp WHERE NOT EXISTS (SELECT username FROM user WHERE username= ?) LIMIT 1; ";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, ''.$usrName);
                $stmt->bindValue(2, ''.$passwd);
                $stmt->bindValue(3, ''.$usrName);
                $stmt->execute();


                $sql ="INSERT INTO student(idStudent, idUser , fullName, vnuEmail, courses )  SELECT * FROM (SELECT ?, (SELECT id from user WHERE username=?), ?, ?, ?) AS tmp WHERE NOT EXISTS (SELECT idStudent FROM student WHERE idStudent = ?) LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, ''.$data[$i][0]);
                $stmt->bindValue(2, ''.$usrName);
                $stmt->bindValue(3, ''.$data[$i][2]);
                $stmt->bindValue(4, ''.$data[$i][3]);
                $stmt->bindValue(5, ''.$data[$i][4]);
                $stmt->bindValue(6, ''.$data[$i][0]);
                $stmt->execute();


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
     * @param $dsn
     * @param $username
     * @param $passwd
     * @param $data
     */
    public static function insertTeacherToDataBase($dsn, $username, $passwd, $data)
    {
        $db = null;
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for ($i = 0; $i < count($data); ++$i) {

                // insert into user table

                $usrName = $data[$i][0];
                $passwd = $data[$i][1];

                $sql = "INSERT INTO user(username, password) SELECT * FROM (SELECT ?, ?) AS tmp WHERE NOT EXISTS (SELECT username FROM user WHERE username= ?) LIMIT 1; ";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, '' . $usrName);
                $stmt->bindValue(2, '' . $passwd);
                $stmt->bindValue(3, '' . $usrName);
                $stmt->execute();


                $sql = "INSERT INTO teacher(idUser , fullName, vnuEmail)  SELECT * FROM (SELECT (SELECT id from user WHERE username=?), ?, ?) AS tmp WHERE NOT EXISTS (SELECT idUser FROM teacher WHERE idUser = (SELECT id from user WHERE username=?)) LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, '' . $usrName);
                $stmt->bindValue(2, '' . $data[$i][2]);
                $stmt->bindValue(3, '' . $data[$i][3]);
                $stmt->bindValue(4, '' . $usrName);
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

    public static function insertAdminToDataBase($dsn, $username, $passwd, $data)
    {
        $db = null;
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            for ($i = 0; $i < count($data); ++$i) {


                $usrName = $data[$i][0];
                $passwd = $data[$i][1];

                //check if exist


                $sql = "SELECT username FROM user WHERE username= ?";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, '' . $usrName);
                $stmt->execute();


                $retUserName = $stmt->fetchAll();
                if(count($retUserName) == 0) {



                    // insert into user table



                    $sql = "INSERT INTO user(username, password) SELECT * FROM (SELECT ?, ?) AS tmp WHERE NOT EXISTS (SELECT username FROM user WHERE username= ?) LIMIT 1; ";

                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(1, '' . $usrName);
                    $stmt->bindValue(2, '' . $passwd);
                    $stmt->bindValue(3, '' . $usrName);
                    $stmt->execute();


                    $sql = "INSERT INTO admin(idUser , fullName)  VALUES ((SELECT id from user WHERE username=?), ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(1, '' . $usrName);
                    $stmt->bindValue(2, '' . $data[$i][2]);
                    $stmt->execute();


                } else {
                    echo 'user name is exist';
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


    /**
     * @param $db
     * @param $teacherName
     * @return mixed
     */
    private static function getTeacherId($db, $teacherName) {
        $sql = "SELECT id FROM teacher WHERE FullName= ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, '' . $teacherName);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    private  static function insertIntoSurveyForm($db, $idClass, $idStudent) {

    }

    /**
     * @param $dsn
     * @param $username
     * @param $passwd
     * @param $data
     * @throws MoreThanOneTeacherSameNameException
     * @throws NotFoundTeacherUserAccount
     */
    public static function insertClassToDataBase($dsn, $username, $passwd, $data)
    {
        $db = null;
        try {
            $db = new \PDO($dsn, $username, $passwd);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction();

            $idTeacher = $data[Excel::TeacherId];
            $teacherName = $data[Excel::TeacherName];
            $idClass = $data[Excel::SubSubjectId];
            $idSubject = $data[Excel::SubjectId];
            $location = $data[Excel::LectureLocation];
            $numLession = $data[Excel::TinChi];


            $idTeacherDataBase = self::getTeacherId($db, $teacherName);
            print_r($idTeacherDataBase);
            if(count($idTeacherDataBase) == 1) {
                $sql = "INSERT INTO class(idClass , idTeacher, idSubject, subjectName, location, numberLession)  SELECT * FROM (SELECT :idClass, :idTeacher, :idSubject, :subjectName, :location, :numberLession) AS tmp WHERE NOT EXISTS (SELECT idClass FROM class WHERE idClass= :idClass) LIMIT 1;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':idClass', $idClass);
                $stmt->bindParam(':idTeacher', $idTeacherDataBase[0][0]);
                $stmt->bindParam(':idSubject', $idSubject);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':numberLession', $numLession);
                $stmt->execute();



                $dataStudent = $data[Excel::Data];
                for ($i = 0; $i < count($dataStudent); ++$i) {
                    $idStudent = $dataStudent[$i][0];
                    self::insertIntoSurveyForm($db, $idClass, $idStudent);

                }
            } else {
                if(count($idTeacherDataBase) >= 2) {
                    throw  new MoreThanOneTeacherSameNameException('invalid teacher name');
                } else if(count($idTeacherDataBase) >= 2) {
                    throw  new NotFoundTeacherUserAccount('invalid teacher name');
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

    public static function insertToDatabase($dsn, $username, $passwd, $data, $userType) {

    }

}