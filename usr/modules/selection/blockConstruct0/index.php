<?php
session_start();
$theme = '/usr/modules/selection/blockConstructor/';
//define('ROOT_CONTRUCTOR', dirname(__FILE__) . '/');
require_once('system/config.php');
require_once('system/controller.php');
require_once('system/mysql.php');
require_once('system/restFilter.php');


$controller = new controller;

?>