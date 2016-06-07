<?php

class Calls2Model extends Model
{
	private $_dateStart;
	private $_dateEnd;
	private $_msisdn;
	private $_cosid;
	private $_ussi;

	public function setDateStart($datestart){
		$this->_dateStart = $datestart;
	}
	public function setDateEnd($dateend){
		$this->_dateEnd = $dateend;
	}
	public function setMsisdn($msisdn){
		$this->_msisdn = $msisdn;
	}	
	public function setCosid($cosid){
		$this->_cosid = $cosid;
	}
	public function setUssi($ussi){
		$this->_ussi = $ussi;
	}		
	public function findByDate()
	{
		$where="";
		if($this->_cosid != "-1") $where.=" AND cosid=".$this->_cosid;
		$sql = "SELECT * FROM calls2 WHERE date_start>='".$this->_dateStart."' AND date_end<='".$this->_dateEnd."' AND events !='' ".$where;
		
		$this->_setSql($sql);
		$events = $this->getAll();
		
		if (empty($events))
		{
			return false;
		}
		
		return $events;
	}
	public function countMsisdn($event)
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
		$sql = "SELECT COUNT(DISTINCT c.msisdn) as numMsisdn FROM calls2 as c 
				INNER JOIN events as e on c.USSI=e.USSI 
				WHERE e.event = '$event' and c.date_start >= '".$this->_dateStart."' and c.date_end <= '".$this->_dateEnd."' ".$where;
	
		$this->_setSql($sql);
		$events = $this->getAll();
	
		if (empty($events))
		{
			return false;
		}
	
		return $events;
	}
	public function findByMsisdn($event)
	{
		$where="";
		if($this->_cosid != "-1") $where.=" AND cosid=".$this->_cosid;
		$sql = "SELECT DISTINCT c.msisdn FROM calls2 as c
		INNER JOIN events as e on c.USSI=e.USSI
		WHERE e.event = '$event' and c.date_start >= '".$this->_dateStart."' and c.date_end <= '".$this->_dateEnd."' ".$where;
	
		$this->_setSql($sql);
		$events = $this->getAll();
	
		if (empty($events))
		{
			return false;
			}
	
			return $events;
	}
}