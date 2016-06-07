<?php

class Db
{
	private static $db;
	protected $dsn;
	protected $logger;
	protected $hasActiveTransaction = false;
	
	public function __construct(){
		$this->dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
		$this->logger = Logger::getLogger('backend');
		$this->init();
	}
	
	public function init()
	{
		if (!self::$db)
		{
			try {
				//$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=UTF-8';			
				self::$db = new PDO($this->dsn, DB_USER, DB_PASS, array( PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				die('Connection error: ' . $e->getMessage());
				$this->logger->error('[DBConn::accediDB()] PDO not started! Error Message: '.$e->getMessage());
			}
		}
		return self::$db;
	}
	/**
	 * Metodo che chiude la connessione distruggendo l'oggetto PDO
	 */
	public function chiudiConnessione(){
		self::$db = null;
	}
		
	public function eseguiPrepare($sql){
		try{
			$sth = self::$db->prepare($sql);
			$this->logger->debug('[DB::prepare()] Prepare Query: '.$sql);
			return $sth;
		} catch (PDOException $e) {
			$this->logger->error('[DB::prepare()] Error Message: '.$e->getMessage());
			$result = false;
		}
			
		return $result;
	}
	public function getLastID(){
		 return self::$db->lastInsertId();
	}
	
	public function beginTransaction(){
		if ( $this->hasActiveTransaction ) {
			return false;
		} else {
			$this->hasActiveTransaction = self::$db->beginTransaction();
			$this->logger->debug('[DB::***begin transaction***]');
			return $this->hasActiveTransaction;
		}	
	}
	
	public function rollback(){
		try{
			$result = self::$db->rollBack();
			$this->logger->debug('[DB:: rollback]');
			$this->hasActiveTransaction = false;
			return $result;
		} catch (PDOException $e) {
			$this->logger->error('[ERROR DB :: rollback'.$e->getMessage());
			$result = false;
		}
			
		return $result;
	}	
	
	public function commit(){
		try{
			$result = self::$db->commit();
			$this->hasActiveTransaction = false;
			$this->logger->debug('[DB:: commit]');
			return $result;
		} catch (PDOException $e) {
			$this->logger->error('[ERROR DB :: commit'.$e->getMessage());
			$result = false;
		}
			
		return $result;		
	}
 	
	public function exec($sql){
		try{
			$numrows = self::$db->exec($sql);
			$this->logger->debug('[DB:: exec] - sql:' .$sql);
			return $numrows;
		} catch (PDOException $e) {
			$this->logger->error('[ERROR DB :: exec'.$e->getMessage());
			$result = false;
		}
			
		return $result;
	}	
	
// 	public function esegui($sth){
// 		$sth->execute();
// 		$this->logger->error('[Query:excute] : '.$this->_db->_sql);
// 	}
}
