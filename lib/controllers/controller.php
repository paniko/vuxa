<?php

class Controller
{
	protected $_model;
	protected $_controller;
	protected $_action;
	protected $_view;
	protected $_modelBaseName;
	protected $logger;
	public $bundle;
	
	public function __construct($model, $action)
	{
		$this->logger = Logger::getLogger('backend');
		$this->_controller = ucwords(__CLASS__);
		$this->_action = $action;
		$this->_modelBaseName = $model;
		$this->logger->debug("Controller: ".$model);
		$this->_setModel($model);
		$this->_view = new View(LIB . 'views' . DS . strtolower($this->_modelBaseName) . DS . $action . '.tpl');
	}
	public function setBundle($bundle){
		$this->bundle=$bundle;
	}
	public function getBundle(){
		return $this->bundle;
	}
	protected function _setModel($modelName)
	{
		$modelName .= 'Model';
		$this->_model = new $modelName();
	}
	
	protected function _setView($viewName)
	{
		$this->_view = new View(LIB . 'views' . DS . strtolower($this->_modelBaseName) . DS . $viewName . '.tpl');
	}
}
