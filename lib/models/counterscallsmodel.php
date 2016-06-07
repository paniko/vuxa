<?php

class CountersCallsModel extends Model
{
	private $_fields;
	private $_date;
	private $_hour;
	private $_cosid;
	private $_idMenu;

	public function setIdMenuOverview($idMenuOverview){
		$this->_idMenu = $idMenuOverview;
	}

	public function setCosid($cosid){
		$this->_cosid = $cosid;
	}

	public function findByDate($idMenuOverview, $date_start, $date_end)
	{
		$where="";
		if(!empty($this->_cosid) && !in_array("-1", $this->_cosid)){
			$where.=" AND cosid IN (";
			foreach($this->_cosid as $cosid){
				if($cosid!="-1")
					$where.= "'".$cosid."',";
			}
			$where = substr($where, 0, -1);
			$where.=") ";
		}		
		//if($this->_cosid != "-1") $where.=" AND cosid=".$this->_cosid;
		$sql = "SELECT * FROM countersCalls$idMenuOverview WHERE date>='".$date_start."' AND date<='".$date_end."' ".$where ;

		$this->_setSql($sql);
		$events = $this->getAll();

		if (empty($events))
		{
			return false;
		}

		return $events;
	}

	public function getColumnNames($idMenuOverview){
			
		$sql = "SHOW COLUMNS FROM countersCalls$idMenuOverview LIKE '%\_%'" ;

		$this->_setSql($sql);
		$columns = $this->getAll();
		
		$this->logger->debug("Columns: ".print_r($columns,true));
		
		if (empty($columns))
		{
			return false;
		}

		return $columns;
	}
	
	public function delete($idMenu){
		try{
			$sth = $this->_db->exec("DROP TABLE IF exists countersCalls".$idMenu);
			return $sth;
		}
		catch (PDOException $e) {
			$this->logger->error('[ERROR DB :: delete counterCalls table'.$e->getMessage());
			$result = false;
		}
	}
}