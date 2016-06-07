<?php

class ItemsModel extends Model
{
	private $_label;
	private $_key;
	private $_order_view;
	private $_ksMenu;

	public function setLabel($label){
		$this->_label = $label;
	}
	public function setKey($key){
		$this->_key = $key;
	}
	public function setOrderView($orderView){
		$this->_order_view = (int)$orderView;
	}
	public function setKsMenu($idMenu){
		$this->_ksMenu = $idMenu;
	}
	
	public function getItems()
	{
		$sql = "SELECT *
				FROM items";
		
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function getItemById($id)
	{
		$sql = "SELECT *
				FROM items
				WHERE idItem = ?";
		
		$this->_setSql($sql);
		$menu = $this->getRow(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function save(){
		$sql = "INSERT INTO items (label,keypress,order_view,ksMenu) VALUES ( :label, :key, :order_view, :ksMenu)";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":label",      $this->_label, PDO::PARAM_STR);
		$sth->bindParam(":key",        $this->_key,   PDO::PARAM_STR);
		$sth->bindParam(":order_view", $this->_order_view, PDO::PARAM_INT);
		$sth->bindParam(":ksMenu",     $this->_ksMenu, PDO::PARAM_INT);
		$sth->execute();
		$this->lastID = $this->_db->getLastID();
		return $sth;		
	}
}