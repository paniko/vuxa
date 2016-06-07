<?php
/**
 * Specifica i link tra le voci e i menu.
 * table = `menuItems`
 * @author nicola
 */
class MenuItemsModel extends Model
{
	private $_ksItem;
	private $_ksMenu;// menu di destistazione
	private $_ksMenuParent;//menu di provenienza

	public function setKsItem($ksItem){
		$this->_ksItem = (int)$ksItem;
	}
	public function setksMenu($ksMenu){
		$this->_ksMenu = (int)$ksMenu;
	}
	public function setksMenuParent($ksMenuParent){
		$this->_ksMenuParent = (int)$ksMenuParent;
	}

	public function getMenuItems()
	{
		$sql = "SELECT *
				FROM menuItems";
		
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function findByIdMenuKeypress($idMenu, $keypress)
	{
		$sql = "SELECT i.label FROM vuxadb.menuItems as mi
		inner join items as i ON i.idItem = mi.ksItem
		where mi.ksMenuParent=? AND i.keypress=? ";
	
		$this->_setSql($sql);
		$menu = $this->getRow(array($idMenu, $keypress));
	
		if (empty($menu))
		{
			return false;
		}
	
		return $menu;
	}
	public function getItemsById($id)
	{
		$sql = "SELECT *
				FROM menuItems
				WHERE ksMenuParent = ?";
		
		$this->_setSql($sql);
		$menu = $this->getRow(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function getMenu($id){
		$sql ="SELECT mp.name as namemenu, mp.idMenu, md.name as destination, md.idMenu as idDestination, md.level as levelDestination, mp.level, i.keypress, i.idItem, i.order_view, i.label
					FROM menuItems as mi
					LEFT JOIN menu as mp ON mp.idMenu = mi.ksMenuParent
					LEFT JOIN menu as md ON md.idMenu = mi.ksMenu
					LEFT JOIN items as i ON i.idItem = mi.ksItem
				WHERE mp.ksMenuoverview =".$id." ORDER BY mp.level,mp.order_view,i.order_view";
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
		
	}
	public function save(){
		$sql = "INSERT INTO menuItems (ksItem,ksMenu,ksMenuParent) VALUES ( :ksItem, :ksMenu, :ksMenuParent)";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":ksItem",  $this->_ksItem, PDO::PARAM_INT);
		$sth->bindParam(":ksMenu",  $this->_ksMenu, PDO::PARAM_INT);
		$sth->bindParam(":ksMenuParent", $this->_ksMenuParent, PDO::PARAM_INT);
		$sth->execute();
		return $sth;		
	}
}