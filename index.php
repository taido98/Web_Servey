<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 04/11/2018
 * Time: 16:17
 */

use database\Database;

require_once 'Server/Excel.php';
require_once 'Server/Database.php';

if(isset($_POST['BtnSubmit'])) {
    $fileName = $_FILES['file']['tmp_name'];
    if($fileName !== null) {
        echo $fileName ."<br>";
        $data = [];
        try {
            $data = \excel\Excel::readStudentTeacherExcel($fileName);
            $data =\excel\Excel::readSubjectExcel($fileName);
//            print_r($data);
            $serverName = 'localhost';
            $dbName = 'servey';
            $username = 'root';
            $password = 'root';
            try {
                Database::insertClassToDataBase("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);

            } catch (\exception\NotFoundTeacherUserAccount $e) {
                print_r($e->getMessage());
            } catch (\exception\MoreThanOneTeacherSameNameException $e) {
                print_r($e->getMessage());
            }

//            \database\Database::insertTeacherToDataBase("mysql:host=$serverName;dbname=$dbName;", $username, $password, $data);
//            \database\Database::insertAdminToDataBase("mysql:host=$serverName;dbname=$dbName;", $username, $password, [['vanminh2', '123', 'Ta Van Minh'],['vanminh1 ', '123', 'Ta Van Minh']]);

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            print_r($e->getMessage());
        }
    } else {
        echo 'file name is empty';
    }

} else {
    echo 'error';
}


echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>Title</title>
</head>
<body>

    <form method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"file\" name=\"file\">
        <button type=\"submit\" name=\"BtnSubmit\"></button>
    </form>

</body>
</html>";
