<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:51
 */


namespace management;

use authorize\Authorize;
use config\Config;
use controller\CriteriaLevelController;
use controller\Role;
use controller\StudentController;
use controller\SurveyController;
use controller\UserController;
use utils\UtilsServer;

require_once 'Management.php';
require_once 'authorize/Authorize.php';
require_once 'StudentController.php';
require_once 'SurveyController.php';
require_once 'UserController.php';
require_once 'Role.php';
require_once 'utils/UtilsServer.php';

class StudentManagement extends Management
{

    public function __construct()
    {
        $this->role = Role::STUDENT;
    }

    /**
     *
     */
    public function process()
    {

        $au = new Authorize(Config::CONFIG_SERVER);
        if($_GET){
            if ($au->verify($_GET['jwt']) === true) {

                $this->userName = UtilsServer::clean($_GET['username']);
                if($this->userName) {
                    //            // comment it if use real
//            $this->userName = '16020030';

                    $db = null;
                    try {
                        $config = Config::CONFIG_SERVER;
                        $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['name'];
                        $userDB = $config['database']['user'];
                        $passwordDB = $config['database']['password'];
                        $db = new \PDO($dsn, $userDB, $passwordDB);
                        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                        $db->beginTransaction();

                        $userController = new UserController(Config::USER_TABLE_NAME);

                        if($this->checkTokenOfThisUser($db, $userController)) {


                            // check role
                            if ($this->checkRole($db, $userController) === true) {
                                //success verify
                                $response = [];
                                $response['profile'] = [];
                                $response['profile']['idStudent'] = $this->userName;
                                $response['data']['class'] = [];
                                // get data in database
                                $studentController = new StudentController(Config::STUDENT_TABLE_NAME);
                                $response['profile']['name'] = $studentController->getFullName($db, $this->userName)['fullName'];
                                $formSurvey = new SurveyController(Config::SURVEY_TABLE_NAME);

                                $surVeyFormsDB = $formSurvey->getAllClassInformOfStudent($db, $studentController->getStudentId($db, $this->userName)[0][0]);


                                $criteriaController = new CriteriaLevelController(Config::CRITERIA_TABLE_NAME);

                                $criteriaDB = $criteriaController->getAllData($db);


                                for ($i = 0; $i < count($surVeyFormsDB); ++$i) {
                                    if ($surVeyFormsDB[$i]['formSurvey'] === '') {
                                        $surVeyFormsDB[$i]['formSurvey'] = [];
                                        foreach ($criteriaDB as $key => $value) {
                                            array_push($surVeyFormsDB[$i]['formSurvey'], [
                                                'id' => $key,
                                                'name' => $value,
                                                'value' => '0']);
                                        }
                                    } else {

                                        $content = json_decode($surVeyFormsDB[$i]['formSurvey'], true);

//                    // comment it
//                    $sampleJson = '{"1":"1", "2":"2"}';
//                    $content = json_decode($sampleJson, true);
//                    print_r($content);
//                    //

                                        $surVeyFormsDB[$i]['formSurvey'] = [];
                                        foreach ($criteriaDB as $key => $value) {
                                            if (array_key_exists($key, $content)) {
                                                array_push($surVeyFormsDB[$i]['formSurvey'], [
                                                    'id' => $key,
                                                    'name' => $criteriaDB[$key],
                                                    'value' => $content[$key]]);
                                            } else {
                                                array_push($surVeyFormsDB[$i]['formSurvey'], [
                                                    'id' => $key,
                                                    'name' => $criteriaDB[$key],
                                                    'value' => '0']);
                                            }

                                        }

                                    }

                                    array_push($response['data']['class'], [
                                        'idClass' => $surVeyFormsDB[$i]['idClass'],
                                        'name' => $surVeyFormsDB[$i]['name'],
                                        'formSurVey' => $surVeyFormsDB[$i]['formSurvey']
                                    ]);

                                }

                                echo json_encode($response, JSON_UNESCAPED_UNICODE);


                            } else {
                                header('HTTP/1.0 401 Unauthorized');
                            }
                        } else {
                            echo "not same person";
                            header('HTTP/1.0 401 Unauthorized');
                        }
                    } catch (\PDOException $e) {

                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
            }
        } else if($_POST) {
            if ($au->verify($_POST['jwt']) === true) {

                $this->userName = UtilsServer::clean($_GET['username']);
                if($this->userName) {

                    $db = null;
                    try {
                        $config = Config::CONFIG_SERVER;
                        $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['name'];
                        $userDB = $config['database']['user'];
                        $passwordDB = $config['database']['password'];
                        $db = new \PDO($dsn, $userDB, $passwordDB);
                        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                        $db->beginTransaction();

                        $userController = new UserController(Config::USER_TABLE_NAME);

                        if($this->checkTokenOfThisUser($db, $userController)) {


                            // check role
                            if ($this->checkRole($db, $userController) === true) {
                                //success verify
                                $data = json_decode(UtilsServer::clean($_POST['data']), true,
                                    512, JSON_UNESCAPED_UNICODE);



                            } else {
                                header('HTTP/1.0 401 Unauthorized');
                            }
                        } else {
                            header('HTTP/1.0 401 Unauthorized');
                        }
                    } catch (\PDOException $e) {
                        print_r($e);
                    }
                } else {
                    header('HTTP/1.0 400 Bad Request');
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
        }

    }
}