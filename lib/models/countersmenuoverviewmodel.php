<?php

class CountersMenuOverviewModel extends Model
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

	public function create($idMenuOverview,$fields){

		$sql = "CREATE TABLE IF NOT EXISTS `countersMenuOverview$idMenuOverview` (";
		$sql.= "`id` int(11) NOT NULL AUTO_INCREMENT,";
		$sql.= "`cosid` varchar(20) NOT NULL,";
		$sql.= "`date` date NOT NULL,";
		$sql.= "`hour` int(11) NOT NULL,";

		$insert_fields = '';

		foreach($fields as $field)
		{
			$insert_fields .= "`$field` int(11) DEFAULT '0',";
		}

		$sql .= $insert_fields;

		$sql .= "PRIMARY KEY (`id`),
				UNIQUE KEY `timekey` (`cosid`,`date`,`hour`)
				) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute();
		$sql = "CREATE TABLE IF NOT EXISTS `countersCalls$idMenuOverview` (";
		$sql.= "`id` int(11) NOT NULL AUTO_INCREMENT,";
		$sql.= "`cosid` varchar(20) NOT NULL,";
		$sql.= "`date` date NOT NULL,";
		$sql.= "`hour` int(11) NOT NULL,";

		$insert_fields = '';

		foreach($fields as $field)
		{
			$insert_fields .= "`$field` int(11) DEFAULT '0',";
		}

		$sql .= $insert_fields;

		$sql .= "PRIMARY KEY (`id`),
				UNIQUE KEY `timekey` (`cosid`,`date`,`hour`)
				) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->execute();
		return $sth;
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
		$sql = "SELECT * FROM countersMenuOverview$idMenuOverview WHERE date>='".$date_start."' AND date<='".$date_end."' ".$where ;

		$this->_setSql($sql);
		$events = $this->getAll();

		if (empty($events))
		{
			return false;
		}

		return $events;
	}

	public function getColumnNames($idMenuOverview){
			
		$sql = "SHOW COLUMNS FROM countersMenuOverview$idMenuOverview LIKE '%\_%'" ;

		$this->_setSql($sql);
		$columns = $this->getAll();
		
		//$this->logger->debug("Columns: ".print_r($columns,true));
		
		if (empty($columns))
		{
			return false;
		}

		return $columns;
	}
	public function delete($idMenu){
		try{
			$sth = $this->_db->exec("DROP TABLE IF exists countersMenuOverview".$idMenu);
			return $sth;
		}
		catch (PDOException $e) {
			$this->logger->error('[ERROR DB :: delete countersMenuOverview table'.$e->getMessage());
			$result = false;
		}
	}	
}