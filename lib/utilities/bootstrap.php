<?php
$controller = "users";
$action = "index";
$query = null;

if (isset($_GET['load']))
{
	$params = array();
	$params = explode("/", $_GET['load']);

	$controller = ucwords($params[0]);
	
	if (isset($params[1]) && !empty($params[1]))
	{
		$action = $params[1];
	}
	
	if (isset($params[2]) && !empty($params[2]))
	{
		$query = $params[2];
	}
}

if($params[0] != "js" && $params[0] != "uploads" && $params[0] != "css" && $params[0] != "ico" ){
	$modelName = $controller;
	$controller .= 'Controller';
	$load = new $controller($modelName, $action);
	
	if (method_exists($load, $action))
	{
	    $load->{$action}($query);
	}
	else 
	{
		die('Invalid method. Please check the URL.');
	}
}