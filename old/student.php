<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:52
 */


use management\StudentManagement;

require_once 'Server/StudentManagement.php';

$studentM = new StudentManagement();
$studentM->process();




