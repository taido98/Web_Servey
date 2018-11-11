<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 09/11/2018
 * Time: 21:34
 */


use config\Config;

require 'Server/lib/vendor/autoload.php';
require_once 'Server/config/Config.php';
require_once 'Server/UserController.php';
require_once 'Server/utils/UtilsServer.php';
require_once 'Server/LoginManagement.php';


$loginManagement = new \management\LoginManagement(Config::CONFIG_SERVER);
$loginManagement->process();
