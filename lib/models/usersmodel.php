<?php

class UsersModel extends Model
{
	private $_name;
	private $_surname;
	private $_username;
	private $_password;
	private $_created;
	private $_role;
	private $_enabled;
	
	public function getUsers()
	{
		$sql = "SELECT *
				FROM users";
		
		$this->_setSql($sql);
		$menu = $this->getAll();
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	
	public function findById($id)
	{
		$sql = "SELECT *
				FROM users
				WHERE idUser = ?";
	
		$this->_setSql($sql);
		$menu = $this->getRow(array($id));
		
		if (empty($menu))
		{
			return false;
		}
		
		return $menu;
	}
	public function checkLogin(){
		$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
	
		$this->_setSql($sql);
		$login = $this->getRow(array($this->_username,$this->_password));
		
		if (empty($login))
		{
			return false;
		}
		
		return $login;
	}
	public function save(){
		$sql = "INSERT INTO users (name,surname,username,password,role,enabled) VALUES ( :name, :surname, :username, :password, :role , :enabled)";
		$sth = $this->_db->eseguiPrepare($sql);
		$sth->bindParam(":name",     $this->_name,     PDO::PARAM_STR);
		$sth->bindParam(":surname",  $this->_surname,  PDO::PARAM_STR);
		$sth->bindParam(":username", $this->_username, PDO::PARAM_STR);
		$sth->bindParam(":password", $this->_password, PDO::PARAM_STR);
		$sth->bindParam(":role",     $this->_role,     PDO::PARAM_STR);
		$sth->bindParam(":enabled",  $this->_enabled,  PDO::PARAM_INT);
		$sth->execute();
		$this->lastID = $this->_db->getLastID();
		return $sth;		
	}

	public function get_name()
	{
	    return $this->_name;
	}

	public function set_name($_name)
	{
	    $this->_name = $_name;
	}

	public function get_surname()
	{
	    return $this->_surname;
	}

	public function set_surname($_surname)
	{
	    $this->_surname = $_surname;
	}

	public function get_username()
	{
	    return $this->_username;
	}

	public function set_username($_username)
	{
	    $this->_username = $_username;
	}

	public function get_password()
	{
	    return $this->_password;
	}

	public function set_password($_password)
	{
	    $this->_password = $_password;
	}

	public function get_created()
	{
	    return $this->_created;
	}

	public function set_created($_created)
	{
	    $this->_created = $_created;
	}

	public function get_role()
	{
	    return $this->_role;
	}

	public function set_role($_role)
	{
	    $this->_role = $_role;
	}

	public function get_enabled()
	{
	    return $this->_enabled;
	}

	public function set_enabled($_enabled)
	{
	    $this->_enabled = $_enabled;
	}
}