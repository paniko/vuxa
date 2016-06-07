<?php
session_start();
class UsersController extends Controller
{
	
	public function index()
	{
		if(isset($_SESSION['username'])){
			header("Location: /home/index",false);
		}
		else{
			$this->_view->set('title', 'Login');
			$this->_view->set('view', 'login');
			return $this->_view->output();
		}
	}
	
	public function login()
	{
		$errors = array();
		$username  = isset($_POST['username']) ? trim($_POST['username']) : NULL;
		$password  = isset($_POST['password']) ? trim($_POST['password']) : NULL;
		
		if (empty($username))
		{
			$check = false;
			array_push($errors, "Username is required!");
		}
		if (empty($password))
		{
			$check = false;
			array_push($errors, "Password is required!");
		}		
		$this->_setView("index");
		$this->_view->set('view', 'login');
		$this->_model->set_username($username);
		$this->_model->set_password(md5($password));
		//$this->logger->debug("md5: ".$this->_model->get_password());
		$usersCheck = $this->_model->checkLogin();
		if(isset($usersCheck) && $usersCheck!=null){
			try {
				$dataUser = array(
					'name'     => $usersCheck->name,
					'surname'  => $usersCheck->surname,
					'username' => $usersCheck->username
				);
				$_SESSION['user']     = $dataUser;
				$_SESSION['username'] = $usersCheck->username;
				header("Location: /home/index",false);
			} catch (PDOException $e) {
				$this->logger->debug($e);
				return false;
			}
		}
		else{
			$this->_setView("index");
			$this->_view->set('view', 'login');
			if(empty($errors))
				array_push($errors, "Check username or password!");
			$this->_view->set('errors', $errors);
			return $this->_view->output();
		}
	}
	
	public function logout(){
		session_unset();
		session_destroy();
		$this->_setView("index");
		$this->_view->set('view', 'login');
		return $this->_view->output();
	}
}