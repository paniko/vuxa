<?php

class MenuOverviewModel extends Model
{
	private $_name;
	private $_filename;
	private $_md5_xmlfile;
	private $_defaultXML;
	private $_note;
	
	public function setName($name){
		$this->_name = $name;
	}
	public function setFilename($filename){
		$this->_filename = $filename;
	}
	public function setMd5XmlFile($md5){
		$this->_md5_xmlfile = $md5;
	}
	public function setDefaultXML($default){
		$this->_defaultXML = $default;
	}
	public function setNote($note){
		$this->_note = $note;
	}	
	public function getMenuOverview()
	{
		$sql = "SELECT *
				FROM MenuOverview";
		
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function getMenuOverviewById($id)
	{
		$sql = "SELECT * FROM MenuOverview WHERE idMenuOverview = ?";
		
		$this->_setSql($sql);
		$menu = $this->getRow(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function getMenuOverviewByName($name)
	{
		$sql = "SELECT * FROM MenuOverview 
				WHERE name = '?'";
	
		$this->_setSql($sql);
		$menu = $this->getRow(array($name));
	
		if (empty($menu))
		{
			return false;
		}
	
		return $menu;
	}
	
/**
 * Find is already load xml file in table
 * @param String $md5
 * @return boolean or idMenuOverview
 */	
	public function findByMd5XmlFile($md5)
	{
		$sql = "SELECT * FROM MenuOverview
				WHERE md5_xmlfile = ?";
	
		$this->_setSql($sql);
		$menu = $this->getRow(array($md5));
	
		if (empty($menu))
		{
			return false;
		}
	
		return $menu->idMenuOverview;
	}	

	public function resetDefault(){
		$this->setDefaultXML(0);
		$sql = "UPDATE MenuOverview SET defaultXML = ?";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute(array($this->_defaultXML));
		return $sth;
	}
	
	public function updateDefault($idMenu){
		$this->resetDefault();
		$this->setDefaultXML(1);
		$sql = "UPDATE MenuOverview SET defaultXML = ? WHERE idMenuOverview = ?";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute(array($this->_defaultXML,$idMenu));
		return $sth;
	}
	public function updateNote($idMenu){
		$sql = "UPDATE MenuOverview SET note = ? WHERE idMenuOverview = ?";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute(array($this->_note,$idMenu));
		return $sth;
	}	
	public function save(){
		$sql = "INSERT INTO MenuOverview
					(name,filename,md5_xmlfile,note)
 				VALUES
 					(:name,:filename,:md5_xmlfile,:note)";
		//$data = array($this->_name);
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":name", $this->_name, PDO::PARAM_STR);
		$sth->bindParam(":filename", $this->_filename, PDO::PARAM_STR);
		$sth->bindParam(":md5_xmlfile", $this->_md5_xmlfile, PDO::PARAM_STR);
		$sth->bindParam(":note", $this->_note, PDO::PARAM_STR);
		$sth->execute();		
		$this->lastID = $this->_db->getLastID();
		$this->logger->debug("lastID: ". $this->lastID);
		return $sth;		
	}
	public function delete($idMenu){
		$sql = "DELETE FROM MenuOverview WHERE idMenuOverview = ?";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute(array($idMenu));
		return $sth;
	}
}