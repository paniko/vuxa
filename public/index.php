<?php
session_start();
define ('DS', DIRECTORY_SEPARATOR);
define ('HOME', dirname(__FILE__));
define ('UP',"..");
define ('LIB',UP.DS.'lib'.DS);
define ('UPLOADSXML',UP.DS.'uploads'.DS.'xml'.DS);
define("APPNAME","VMA - Voice Mail Analytics");
if(isset($_SESSION['username']))
	define ('_LOGIN',$_SESSION['username']);

ini_set ('display_errors', 1);
error_reporting(E_ALL^E_NOTICE);

require_once UP.DS.'config.php';
require_once LIB.'utilities'.DS.'bootstrap.php';

function __autoload($class)
{
	if (file_exists(LIB.'utilities' . DS . strtolower($class) . '.php'))
	{
		require_once LIB.'utilities' . DS . strtolower($class) . '.php';
	}
	else if (file_exists(LIB.'models' . DS . strtolower($class) . '.php'))
	{
		require_once LIB.'models' . DS . strtolower($class) . '.php';
	}
	else if (file_exists(LIB.'controllers' . DS . strtolower($class) . '.php'))
	{
		require_once LIB.'controllers'  . DS . strtolower($class) . '.php';
	}
}