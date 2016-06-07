<?php

class SettingsModel extends Model
{
	private $_contentRowFilter;
	private $_separationChar;
	private $_datePosition;
	private $_timePosition;
	
	public function setContentRowFilter($contentRowFilter)
	{
		$this->_contentRowFilter = $contentRowFilter;
	}
	
	public function setSeparationChar($separationChar)
	{
		$this->_separationChar = $separationChar;
	}
	
	public function setDatePosition($datePosition)
	{
		$this->_datePosition = $datePosition;
	}

	public function setTimePosition($timePosition)
	{
		$this->_timePosition = $timePosition;
	}
	
	public function loadSetting()
	{
		$sql = "SELECT * FROM settings ORDER BY idSettings DESC LIMIT 1";
		
		$this->_setSql($sql);
		$settings = $this->getAll();
		
		if (empty($settings))
		{
			return false;
		}
		
		return $settings[0];
	}
		
	public function store()
	{
		$sql = "INSERT INTO settings 
					(content_row_filter, separation_char, date_position, time_position)
 				VALUES 
 					(?, ?, ?, ?)";
		
		$data = array(
			$this->_contentRowFilter,
			$this->_separationChar,
			$this->_datePosition,
			$this->_timePosition
		);
		
		$sth = $this->_db->prepare($sql);
		return $sth->execute($data);
	}
}