<?php
ini_set('memory_limit', '-1');
class Model 
{
	protected $_db;
	protected $_sql;
	protected $logger;
	protected $lastID;
	protected $table;
	
	public function __construct($table = null)
	{
		$this->_db = new Db();
		$this->logger = Logger::getLogger('backend');
	}
	
	protected function _setSql($sql)
	{
		$this->_sql = $sql;
		$this->logger->debug("SQL: ".$sql);
	}
	
	public function getAll($data = null)
	{
		if (!$this->_sql)
		{
			throw new Exception("No SQL query!");
		}
		
		$sth = $this->_db->eseguiPrepare($this->_sql);
		$sth->execute($data);
		return $sth->fetchAll();
	}
	
	public function getRow($data = null)
	{
		if (!$this->_sql)
		{
			throw new Exception("No SQL query!");
		}
		
		$sth = $this->_db->eseguiPrepare($this->_sql);
		$sth->execute($data);
		$result = $sth->fetch(PDO::FETCH_OBJ);
		return $result;
	}
	
	public function drop($data = null){
		if (!$this->_sql)
		{
			throw new Exception("No SQL query!");
		}
			
		$sth = $this->_db->eseguiPrepare($this->_sql);
		$sth->execute($data);

		return $sth;		
	}
}