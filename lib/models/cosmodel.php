<?php

class CosModel extends Model
{
	private $_cosid;
	private $_ksMenuOverview;

	public function setCosid($cosid){
		$this->_cosid = $cosid;
	}
	public function setKsMenuOverview($idMenu){
		$this->_ksMenuOverview = $idMenu;
	}	
	public function findById($id)
	{
		$sql = "SELECT *
				FROM cos
				WHERE idCos = ?";
		
		$this->_setSql($sql);
		$cos = $this->getRow(array($id));
		
		if (empty($cos))
		{
			return false;
		}
		
		return $cos;
	}
	
	public function getListCos($idMenuOverview){
		$sql ="SELECT DISTINCT c.cosid
					FROM menuItems as mi
					LEFT JOIN menu as mp ON mp.idMenu = mi.ksMenuParent
					LEFT JOIN menu as md ON md.idMenu = mi.ksMenu
					LEFT JOIN items as i ON i.idItem = mi.ksItem
					LEFT JOIN itemsCos as ic ON ic.ksItem = i.idItem
					LEFT JOIN cos as c ON c.idCos = ic.ksCos
				WHERE mp.ksMenuoverview =".$idMenuOverview." AND c.cosid is not null ORDER BY mp.level,mp.order_view,i.order_view, c.cosid";
		$this->_setSql($sql);
		$cos = $this->getAll();
	
		if (empty($cos))
		{
			return false;
		}
	
		return $cos;
	
	}
	public function isExist($cosid){
		$sql = "SELECT *
				FROM cos
				WHERE cosid = ?";
		
		$this->_setSql($sql);
		$cos = $this->getRow(array($cosid));
		
		if (empty($cos))
		{
			return false;
		}
		
		return $cos->idCos;
	}
	public function save(){
		$sql = "INSERT INTO cos (cosid,ksMenuOverview) VALUES ( :cosid, :ksMenuOverview )";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":cosid",      $this->_cosid, PDO::PARAM_STR);
		$sth->bindParam(":ksMenuOverview",      $this->_ksMenuOverview, PDO::PARAM_INT);
		$sth->execute();
		$this->lastID = $this->_db->getLastID();
		return $sth;		
	}
}