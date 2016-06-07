<?php
/**
 * Specifica i link tra le voci e i menu.
 * table = `menuItems`
 * @author nicola
 */
class ItemsCosModel extends Model
{
	private $_ksItem;
	private $_ksCos;

	public function setKsItem($ksItem){
		$this->_ksItem = (int)$ksItem;
	}
	public function setksCos($ksCos){
		$this->_ksCos = $ksCos;
	}
	
	public function getCos($idItem){
		$sql ="SELECT *
					FROM itemsCos as ic
					LEFT JOIN cos as c ON ic.ksCos = c.idCos
				WHERE ic.ksItem=".$idItem;
		$this->_setSql($sql);
		$cos = $this->getAll();
		
		if (empty($cos))
		{
			return false;
		}
		
		return $cos;
		
	}
	public function getCosActive($idMenuoverview){
		$sql ="SELECT i.label, i.idItem, c.cosid
		FROM menuItems as mi
		LEFT JOIN menu as mp ON mp.idMenu = mi.ksMenuParent
		LEFT JOIN items as i ON i.idItem = mi.ksItem
		LEFT JOIN itemsCos as ic ON ic.ksItem = i.idItem
		LEFT JOIN cos as c ON c.idCos = ic.ksCos
		WHERE mp.ksMenuoverview =".$idMenuoverview." AND c.cosid is not null ORDER BY i.idItem";
		$this->_setSql($sql);
		$result = $this->getAll();
		
		if (empty($result))
		{
			return false;
		}
		
		return $result;
		
	}
	
	public function save(){
		$sql = "INSERT INTO itemsCos (ksItem,ksCos) VALUES ( :ksItem, :ksCos)";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":ksItem",  $this->_ksItem, PDO::PARAM_INT);
		$sth->bindParam(":ksCos",  $this->_ksCos, PDO::PARAM_STR);
		$sth->execute();
		return $sth;		
	}
}