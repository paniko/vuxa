<?php

class CallsModel extends Model
{
	private $_dateStart;
	private $_dateEnd;
	private $_events;
	private $_cosid;

	public function setDateStart($datestart){
		$this->_dateStart = $datestart;
	}
	public function setDateEnd($dateend){
		$this->_dateEnd = $dateend;
	}
	public function setEvents($events){
		$this->_events = $events;
	}	
	public function setCosid($cosid){
		$this->_cosid = $cosid;
	}
	public function findByDate()
	{
		$where="";
		if($this->_cosid != "-1") $where.=" AND cosid=".$this->_cosid;
		$sql = "SELECT * FROM calls WHERE date_start>='".$this->_dateStart."' AND date_end<='".$this->_dateEnd."' AND events !='' ".$where ;
		
		$this->_setSql($sql);
		$events = $this->getAll();
		
		if (empty($events))
		{
			return false;
		}
		
		return $events;
	}
	public function statistic4hours($idMenuOverview){
		$sql = "SELECT * FROM calls WHERE date_start>='".$this->_dateStart."' AND date_end<='".$this->_dateEnd."'".$where ;
		$this->_setSql($sql);
		$cos = $this->getAll();
	
		if (empty($cos))
		{
			return false;
		}
	
		return $cos;
	
	}
}