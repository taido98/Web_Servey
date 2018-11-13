<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 04/11/2018
 * Time: 16:17
 */


//require_once "Server/Excel.php";
//require_once "Server/DatabaseUtils.php";
//require_once "Server/StudentController.php";
//require_once "Server/TeacherController.php";
//require_once "Server/AdminController.php";
//require_once "Server/ClassController.php";
//require_once 'Server/CriteriaLevelController.php';

//
//$serverName = 'localhost';
//$dbName = 'servey';
//$username = 'root';
//$password = 'root';
//$dsn = "mysql:host=$serverName;dbname=$dbName;";
//
//// test select sql;
//
//$criteriaLevel = new \controller\CriteriaLevelController(\config\Config::CRITERIA_TABLE_NAME);
//$arr = $criteriaLevel->insertCriteria($dsn, $username, $password, \config\Config::DEFAULT_FORM);
//print_r($arr);
//
//try {
//    $db = new \PDO($dsn, $username, $password);
//    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//
//    $db->beginTransaction();
//
//    $criteriaLevel->getAllData($db);
//    $db->commit();
//} catch (\PDOException $e) {
//    if ($db !== null) {
//        $db->rollBack();
//    }
//    echo "Error: " . $e->getMessage();
//} finally {
//    if ($db !== null) {
//        $db = null;
//    }
//
//}
//
//
//if(isset($_POST['BtnSubmit'])) {
//    $fileName = $_FILES['file']['tmp_name'];
//    if($fileName !== null) {
//        echo $fileName ."<br>";
//        $data = [];
//        try {
////            $data = \excel\Excel::readStudentExcel($fileName);
////            $data = \excel\Excel::readTeacherExcel($fileName);
////            $data =\excel\Excel::readSubjectExcel($fileName);
////            print_r($data);
//
////                DatabaseUtils::insertClassToDataBase("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);
//
////            $studentController = new \controller\StudentController(\config\Config::STUDENT_TABLE_NAME);
////            $studentController->insertStudents("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);
//
//
////            $teacherController = new \controller\TeacherController(\config\Config::TEACHER_TABLE_NAME);
////            $teacherController->insertTeachers("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);
//
//
////            $classController = new \controller\ClassController(\config\Config::CLASS_TABLE_NAME);
////            $classController->insertClass("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);
//
//
//
////            $adminController = new \controller\AdminController(\config\Config::ADMIN_TABLE_NAME);
////            $adminController->insertAdmins("mysql:host=$serverName;dbname=$dbName;", $username, $password, [['vanminh', 1]]);
//
//
//
//
//
//            //
//
//
//
//
//        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
//            print_r($e->getMessage());
//        } catch (\exception\ExsitingUserNameException $e) {
//            echo "existing user name";
//        }
//    } else {
//        echo 'file name is empty';
//    }
//
//} else {
//    echo 'error';
//}
//
//
////echo "<!DOCTYPE html>
////<html lang=\"en\">
////<head>
////    <meta charset=\"UTF-8\">
////    <title>Title</title>
////</head>
////<body>
////
////    <form method=\"post\" enctype=\"multipart/form-data\">
////        <input type=\"file\" name=\"file\">
////        <button type=\"submit\" name=\"BtnSubmit\"></button>
////    </form>
////
////</body>
////</html>";



//echo base64_encode(openssl_random_pseudo_bytes(\config\Config::LENGTH_OPENSSL_GENERATE));




