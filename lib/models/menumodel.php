<?php

class MenuModel extends Model
{
	private $_name;
	private $_level;
	private $_order;
	private $_entrypoint;
	private $_ksMenuOverview;

	public function setName($name){
		$this->_name = $name;
	}
	public function setLevel($level){
		$this->_level = (int)$level;
	}
	public function setOrder($order){
		$this->_order = (int)$order;
	}
	public function setEntrypoint($entrypoint){
		$this->_entrypoint = $entrypoint;
	}
	public function setKsMenuOverview($ksMenuoverview){
		$this->_ksMenuOverview = (int)$ksMenuoverview;
	}
	public function getMenu()
	{
		$sql = "SELECT *
				FROM menu";
		
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function getMenuById($id)
	{
		$sql = "SELECT *
				FROM menu
				WHERE ksMenuoverview = ?";
		
		$this->_setSql($sql);
		$menu = $this->getAll(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	public function getMenuByIdMenu($id)
	{
		$sql = "SELECT *
				FROM menu
				WHERE idMenu = ?";
	
		$this->_setSql($sql);
		$menu = $this->getRow(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function save(){
		$sql = "INSERT INTO menu (name,level,order_view,entrypoint,ksmenuoverview) VALUES ( :name, :level, :order_view, :entrypoint, :ksMenuoverview)";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":name",  $this->_name, PDO::PARAM_STR);
		$sth->bindParam(":level", $this->_level, PDO::PARAM_INT);
		$sth->bindParam(":order_view", $this->_order, PDO::PARAM_INT);
		$sth->bindParam(":entrypoint", $this->_entrypoint, PDO::PARAM_STR);
		$sth->bindParam(":ksMenuoverview", $this->_ksMenuOverview, PDO::PARAM_INT);
		$sth->execute();
		$this->lastID = $this->_db->getLastID();
		return $sth;		
	}
}