<?php

class HomeController extends Controller
{
	public function __construct($model, $action)
	{
		parent::__construct($model, $action);
		//$this->_setModel($model);
	}

	public function index()
	{
		if(defined('_LOGIN')){
			$this->_view->set('title', "Home page");
			$this->_view->set('view', "home");
			return $this->_view->output();
		}
		else{
			header("Location: /users");
		}
	}

}