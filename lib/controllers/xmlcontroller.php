<?php
session_start();
class XmlController extends Controller
{
	protected $xml=null;
	
	public function index()
	{
		$this->_view->set('title', 'Read Schema Menu');
		
// 		$result = $this->_model->loadSetting();
// 		if(!empty($result))
// 			$this->_view->set('userData', $result);
 		return $this->_view->output();
	}
	
	public function restrictAccess(){
		$this->_setView("restrictedaccess");
		$this->_view->set('title', 'Restricted access');
		$this->_view->set('view', 'restricted');
		$this->_view->output();		
	}
	
	public function upload($xmlfile){
		if(defined('_LOGIN')){
			$this->_view->set('title', 'Upload Schema Menu');
			$this->_view->set('view', "upload");
			$this->_view->set('xmlLoaded',$this->_model->getXMLUploaded());
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}
	
	public function defaultXML($idMenu){
		try {
			$this->_model->setDefault($idMenu);
		}
		catch (PDOException $e){
			$this->logger->debug($e);
			return false;
		}
		
	}
		
	public function readXML($xmlfile){
		$path = UP.DS."uploads".DS."xml".DS;
		$this->xml = $this->_model->loadXml($path.$xmlfile);
	}
	
	public function view($xmlfile){
		$this->_view->set('title', 'View Menu Overview');
		$result = $this->save($xmlfile);

		if($result)
			$this->_view->set('xmlData', $this->xml);	
		$this->_view->output();
	}
	public function delete($idMenu){
		try{
			$this->_model->deleteMenu($idMenu);
		}
		catch (PDOException $e){
			$this->logger->debug("ERROR delete Menu overview: ".$e);
		}
	}
	public function export($idMenu){
		$mapEvents=array();
		$countersModel = new CountersMenuOverviewModel();
		
		$result = $this->_model->getMenuFromDb($idMenu);
		$d1         = isset($_POST['date1']) ? trim($_POST['date1']) : NULL;
		$d2         = isset($_POST['date2']) ? trim($_POST['date2']) : NULL;
		$cosid      = isset($_POST['cosid']) ? $_POST['cosid'] : NULL;
		$typeExport = isset($_POST['typeExport']) ? trim($_POST['typeExport']) : NULL;
		$outExport  = isset($_POST['outExport']) ? trim($_POST['outExport']) : NULL;
		$days = floor(abs(strtotime($d2) - strtotime($d1)) / 86400);
		$dailyMin   = isset($_SESSION[$typeExport]['threshold'][0]) ? $_SESSION[$typeExport]['threshold'][0] : (isset($_POST['dailyMin']) ? trim($_POST['dailyMin']) : DAILY_MIN);
		$dailyMax   = isset($_SESSION[$typeExport]['threshold'][1]) ? $_SESSION[$typeExport]['threshold'][1] : (isset($_POST['dailyMax']) ? trim($_POST['dailyMax']) : DAILY_MAX);		
		$this->_setView('menu2'.$outExport);

		$this->_view->set('d1', $d1);
		$this->_view->set('d2', $d2);
		$this->_view->set('cosid', $cosid);
		$this->_view->set('dailyMinThreshold', $dailyMin);
		$this->_view->set('dailyMaxThreshold', $dailyMax);
		$this->_view->set('dailyMin', $dailyMin*$days);
		$this->_view->set('dailyMax', $dailyMax*$days);
		
		switch($typeExport){
			case 'hits': 
				$title = "All Hits";
				$typeProcess="processLog";
				break;
			case 'call':
				$title = "Hits x call";
				$typeProcess="processLog4Calls";
				break;
			case 'msisdn':
				$countersModel = new CountersMenuOverviewModel();
				$calls2Model = new Calls2Model();
				$title = "Hits x Msisdn";
				$calls2Model->setDateStart($d1);
				$calls2Model->setDateEnd($d2);
				$calls2Model->setCosid($cosid);
				try {
					$columns = $countersModel->getColumnNames($idMenu);
					foreach ($columns as $eventStruct){
						$event=$eventStruct['Field'];
						$numCalls4Msisdn = $calls2Model->countMsisdn($event);
						list($idsubmenu,$iditem) = explode("_",$event);
						foreach ($numCalls4Msisdn as $msisdn){
							$mapEvents[$idsubmenu][$iditem] += $msisdn['numMsisdn'];
						}
					}
				}
				catch (PDOException $e) {
					$this->logger->debug('Error:', $e->getMessage());
				}				
				break;				
		}
		if($typeExport=="hits"||$typeExport=="call"){
			try {
				if($outExport!='xls'){
					$events = $this->_model->$typeProcess($idMenu,$d1,$d2,$cosid);
					if(!empty($events)){
						$columns = $countersModel->getColumnNames($idMenu);
						foreach($events as $hits){
							$start=0;
							foreach ($columns as $eventStruct){
								$event=$eventStruct['Field'];
								list($idsubmenu,$iditem) = explode("_",$event);
			
								if(!empty($mapEvents[$idsubmenu][$iditem])){
									$mapEvents[$idsubmenu][$iditem] += $hits[$event];
								}
								else{
									$mapEvents[$idsubmenu][$iditem] = $hits[$event];
								}
							}
						}
					}
				}
				else{//export XLS
					$cosModel = new CosModel();
					$listCos = $cosModel->getListCos($idMenu);
					if(!empty($listCos))
						$this->_view->set('listCos', $listCos);
					foreach ($listCos as $cos){
						$mapEvents[$cos['cosid']]=array();
						$events = $this->_model->$typeProcess($idMenu,$d1,$d2,array($cos['cosid']));
						if(!empty($events)){
							$columns = $countersModel->getColumnNames($idMenu);
							foreach($events as $hits){
								$start=0;
								foreach ($columns as $eventStruct){
									$event=$eventStruct['Field'];
									list($idsubmenu,$iditem) = explode("_",$event);
										
									if(!empty($mapEvents[$cos['cosid']][$idsubmenu][$iditem])){
										$mapEvents[$cos['cosid']][$idsubmenu][$iditem] += $hits[$event];
									}
									else{
										$mapEvents[$cos['cosid']][$idsubmenu][$iditem] = $hits[$event];
									}
								}
							}
						}
					}
					//all
					
					$events = $this->_model->$typeProcess($idMenu,$d1,$d2,array("-1"));
					if(!empty($events)){
						$columns = $countersModel->getColumnNames($idMenu);
						foreach($events as $hits){
							$start=0;
							foreach ($columns as $eventStruct){
								$event=$eventStruct['Field'];
								list($idsubmenu,$iditem) = explode("_",$event);
					
								if(!empty($mapEvents['all'][$idsubmenu][$iditem])){
									$mapEvents['all'][$idsubmenu][$iditem] += $hits[$event];
								}
								else{
									$mapEvents['all'][$idsubmenu][$iditem] = $hits[$event];
								}
							}
						}
					}
					
					$this->logger->debug("mapEvents:".print_r($mapEvents,true));
				}
			}
			catch (PDOException $e) {
				$this->logger->debug('Error:', $e->getMessage());
			}		
		}
		$this->_view->set('title', $title);
		$this->_view->set('events', $mapEvents);
		if($result)
			$this->_view->set('result', $result);
		$this->_view->output();

	}
	
	public function menu2csv($idMenu){
		$mapEvents=array();
		$countersModel = new CountersMenuOverviewModel();
		$this->_setView('menu2csv');
		$result = $this->_model->getMenuFromDb($idMenu);
		$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : NULL;
		$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : NULL;
		$cosid = isset($_POST['cosid']) ? trim($_POST['cosid']) : NULL;
		$this->_view->set('d1', $d1);
		$this->_view->set('d2', $d2);
		try {
			$events = $this->_model->processLog($idMenu,$d1,$d2,$cosid);
			
			if(!empty($events)){
				foreach($events as $hits){
					$start=0;
					$columns = $countersModel->getColumnNames($idMenu);
					foreach ($columns as $eventStruct){
						$event=$eventStruct['Field'];
						list($idsubmenu,$iditem) = explode("_",$event);
						
						if(!empty($mapEvents[$idsubmenu][$iditem])){
							$mapEvents[$idsubmenu][$iditem] += $hits[$event];
						}
						else{
							$mapEvents[$idsubmenu][$iditem] = $hits[$event];
						}	
					}
				}
			}
	
			$this->_view->set('events', $mapEvents);
			if($result)
				$this->_view->set('result', $result);
		}
		catch (Exception $e) {
			$this->_setView('viewmenu');
			$this->_view->set('title', 'There was an error saving the data!');
		}
		$this->_view->output();
	}
	
	public function menu2xlsMsisdn($idMenu){
		set_time_limit(0);
		$mapMSISDN = array();
		$countersModel  = new CountersMenuOverviewModel();
		$calls2Model    = new Calls2Model();
		$menuItemsModel = new MenuItemsModel();
		$itemsModel     = new ItemsModel();
		$menuModel      = new MenuModel();
		$cosModel       = new CosModel();
	
		$this->_setView('msisdn2xls');
	
		$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : NULL;
		$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : NULL;
		//$cosid = isset($_POST['cosid']) ? trim($_POST['cosid']) : NULL;
		$this->_view->set('d1', $d1);
		$this->_view->set('d2', $d2);
		
		$calls2Model->setDateStart($d1);
		$calls2Model->setDateEnd($d2);
		
		$listCos = $cosModel->getListCos($idMenu);
		if(!empty($listCos))
			$this->_view->set('listCos', $listCos);
		foreach ($listCos as $cos){
			$calls2Model->setCosid($cos['cosid']);
			$mapMSISDN[$cos['cosid']]=array();
			try {
				$columns = $countersModel->getColumnNames($idMenu);
				$checkedItems = explode('|',$_POST['chkMsisdn']);
		
				foreach ($checkedItems as $check){
					$tmp = explode("_",$check);
					//$idItems[]=$tmp[1];
					$event =$tmp[0]."_".$tmp[2];
					//$idsubmenu = $tmp[0];
					$iditem    = $tmp[2];
					$calls = $calls2Model->findByMsisdn($event);
					$menu = $menuModel->getMenuByIdMenu($tmp[0]);
					$item = $itemsModel->getItemById($tmp[1]);
					$this->logger->debug("menu name: ".$menu->name);
					if(isset($calls) && !empty($calls)){
						foreach ($calls as $call){
							$mapMSISDN[$cos['cosid']][]= array(
									'msisdn'   => $call['msisdn'],
									//'date'     => $call['date_start'],
									'menu'     => $menu->name,
									'label'    => $item->label,
									'keypress' => $iditem
							);
						}
					}
					else{
						$mapMSISDN[$cos['cosid']][]= array(
								'msisdn'   => '',
								//'date'     => $call['date_start'],
								'menu'     => $menu->name,
								'label'    => $item->label,
								'keypress' => $iditem		
						);
					}
				}
		
			}
			catch (Exception $e) {
				$this->_setView('msisdn2xls');
				$this->_view->set('title', 'There was an error saving the data!');
			}		
		}
				$this->logger->trace("listCOS: ".print_r($listCos,true));
				$this->logger->trace("mapMSISDN: ".print_r($mapMSISDN,true));
				if(isset($mapMSISDN))
					$this->_view->set('data', $mapMSISDN);
		$this->_view->output();
	}
	
	public function menu2csvMsisdn($idMenu){
		$mapMSISDN = array();
		$countersModel = new CountersMenuOverviewModel();
		$calls2Model = new Calls2Model();
		$menuItemsModel = new MenuItemsModel();
		$itemsModel = new ItemsModel();
		$menuModel = new MenuModel();
		
		$this->_setView('msisdn2csv');

		$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : NULL;
		$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : NULL;
		$cosid = isset($_POST['cosid']) ? trim($_POST['cosid']) : NULL;
		$calls2Model->setDateStart($d1);
		$calls2Model->setDateEnd($d2);
		$calls2Model->setCosid($cosid);
		try {
			$columns = $countersModel->getColumnNames($idMenu);
			$checkedItems = explode('|',$_POST['chkMsisdn']);

			foreach ($checkedItems as $check){
				$tmp = explode("_",$check);
				//$idItems[]=$tmp[1];
				$event =$tmp[0]."_".$tmp[2];
				//$idsubmenu = $tmp[0];
				$iditem    = $tmp[2];
				$calls = $calls2Model->findByMsisdn($event);
				if(isset($calls) && !empty($calls)){
					$menu = $menuModel->getMenuByIdMenu($tmp[0]);
					$item = $itemsModel->getItemById($tmp[1]);
					foreach ($calls as $call){
						$mapMSISDN[]= array(
								'msisdn'   => $call['msisdn'],
								'date'     => $call['date_start'],
								'menu'     => $menu->name,
								'label'    => $item->label,
								'keypress' => $iditem
						);
					}
				}			
			}
			//$this->logger->debug("mapMSISDN: ".print_r($mapMSISDN,true));
			if(isset($mapMSISDN))
				$this->_view->set('msisdn', $mapMSISDN);

		}
		catch (Exception $e) {
			$this->_setView('msisdn2csv');
			$this->_view->set('title', 'There was an error saving the data!');
		}
		$this->_view->output();
	}	
	
	/**
	 * 
	 * @param unknown $idMenu
	 */
	public function viewdb($idMenu){
		$menuOverviewModel = new MenuOverviewModel();
		$menuOverview = $menuOverviewModel->getMenuOverviewById($idMenu);
		if(defined('_LOGIN')){
			$this->_setView('viewmenu');
			$this->_view->set('title', 'View Menu Overview: '.$idMenu);
			$this->_view->set('nameMenu', $menuOverview->name);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('menu', false);
			$this->_view->set('toolExport', false);
			$result = $this->_model->getMenuFromDb($idMenu);
			//$this->logger->debug("MENU:".print_r($result,true));
			if($result)
				$this->_view->set('result', $result);
			$data = array();
			
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if(isset($data))
				$this->_view->set('data', $data);
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}

	public function hitscall($idMenu){
		if(defined('_LOGIN')){
			$this->_setView('hitscall');
			$this->_view->set('title', 'View Hits for Calls: '.$idMenu);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('menu', true);
			$result = $this->_model->getMenuFromDb($idMenu);
			//$this->logger->debug("MENU:".print_r($result,true));
			if($result)
				$this->_view->set('result', $result);
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if($data)
				$this->_view->set('data', $data);
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}

	public function hitsMsisdn($idMenu){
		if(defined('_LOGIN')){
			$this->_setView('hitsMsisdn');
			$this->_view->set('title', 'View Hits for MSISDN: '.$idMenu);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('menu', true);
			$result = $this->_model->getMenuFromDb($idMenu);
			//$this->logger->debug("MENU:".print_r($result,true));
			if($result)
				$this->_view->set('result', $result);
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if($data)
				$this->_view->set('data', $data);
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}	

	public function ajaxHitsMsisdn($idMenu){
		$menuOverviewModel = new MenuOverviewModel();
		$menuOverview = $menuOverviewModel->getMenuOverviewById($idMenu);
		if(defined('_LOGIN')){
			$this->_setView('ajaxHitsMsisdn');
			$this->_view->set('title', 'View Hits for MSISDN: '.$idMenu);
			$this->_view->set('nameMenu', $menuOverview->name);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('view', 'msisdn');
			$this->_view->set('menu', true);
			$this->_view->set('toolExport', true);
			$this->_view->set('dailyMin', DAILY_MIN);
			$this->_view->set('dailyMax', DAILY_MAX);
			$this->_view->set('dailyLimit', DAILY_LIMIT);
			$result = $this->_model->getMenuFromDb($idMenu);
			//$this->logger->debug("MENU:".print_r($result,true));
			if($result)
				$this->_view->set('result', $result);
			$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : (isset($_SESSION['date1']) ? $_SESSION['date1'] : NULL);
			$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : (isset($_SESSION['date2']) ? $_SESSION['date2'] : NULL);
			$cosid = isset($_POST['cosid']) ? $_POST['cosid'] : (isset($_SESSION['cosid']) ? $_SESSION['cosid'] : NULL);
				
			$_SESSION['date1'] = $d1;
			$_SESSION['date2'] = $d2;
			$_SESSION['cosid'] = $cosid;
						
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			
			if($data)
				$this->_view->set('data', $data);
			
			$userData = array(
					'd1'    => $d1,
					'd2'    => $d2,
					'cosid' => $cosid
			);
			$this->_view->set('userData', $userData);
			
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}
	public function process($idMenu){
		if(defined('_LOGIN')){
			//$_SESSION['idMenuOverview']=$idMenu;
			$mapEvents = array();
			$mapClick4Hours = array();
			$countersModel = new CountersMenuOverviewModel();
			$menuOverviewModel = new MenuOverviewModel();
			$menuOverview = $menuOverviewModel->getMenuOverviewById($idMenu);
			$this->_setView('viewmenu');
			$this->_view->set('title', 'View Menu Overview: '.$idMenu);
			$this->_view->set('nameMenu', $menuOverview->name);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('view', 'hits');
			$this->_view->set('menu', true);
			$this->_view->set('toolExport', true);
			$this->_view->set('dailyMin', DAILY_MIN);
			$this->_view->set('dailyMax', DAILY_MAX);		
			$this->_view->set('dailyLimit', DAILY_LIMIT);
			
			$result = $this->_model->getMenuFromDb($idMenu);
			if($result)
				$this->_view->set('result', $result);
			$errors = array();
			$check = true;
			$d1           = isset($_POST['date1']) ? trim($_POST['date1']) : (isset($_SESSION['date1']) ? $_SESSION['date1'] : NULL);
			$d2           = isset($_POST['date2']) ? trim($_POST['date2']) : (isset($_SESSION['date2']) ? $_SESSION['date2'] : NULL);
			$cosid        = isset($_POST['cosid']) ? $_POST['cosid'] : (isset($_SESSION['cosid']) ? $_SESSION['cosid'] : NULL);
						
			$_SESSION['date1'] = $d1;
			$_SESSION['date2'] = $d2;
			$_SESSION['cosid'] = $cosid;

			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if(isset($data))
				$this->_view->set('data', $data);
			
			if (empty($d1))
			{
				$check = false;
				array_push($errors, "Date 1 is required!");
			}
			if (empty($d2))
			{
				$check = false;
				array_push($errors, "Date 2 is required!");
			}
			if (empty($cosid))
			{
				$check = false;
				array_push($errors, "CosId is required!");
			}		
			if (!$check)
			{
				$this->_setView('viewmenu');
				$this->_view->set('title', 'Invalid form data!');
				$this->_view->set('idmenu', $idMenu);
				$this->_view->set('menu', true);
				if($result)
					$this->_view->set('result', $result);
				$this->_view->set('errors', $errors);
				$this->_view->set('formData', $_POST);
			}		
			
			try {
				$events = $this->_model->processLog($idMenu,$d1,$d2,$cosid);
				
				if(!empty($events)){
					$columns = $countersModel->getColumnNames($idMenu);
					foreach($events as $hits){
						$start=0;
						foreach ($columns as $eventStruct){
							$event=$eventStruct['Field'];
							list($idsubmenu,$iditem) = explode("_",$event);
							
							if(!empty($mapEvents[$idsubmenu][$iditem])){
								$mapEvents[$idsubmenu][$iditem] += $hits[$event];
							}
							else{
								$mapEvents[$idsubmenu][$iditem] = $hits[$event];
							}	
							$mapClick4Hours[sprintf("%02d",$hits['hour'])][$event] += $hits[$event];
						}
						asort($mapClick4Hours);
						$_SESSION['dataHistogram']=$mapClick4Hours;
					}
				}
				//$this->logger->debug("EVENTS MAP:".print_r($mapEvents,true));
				$this->_view->set('events', $mapEvents);				
				$this->_view->set('formData', $_POST);
	
				$userData = array(
						'd1'    => $d1,
						'd2'    => $d2,
						'cosid' => $cosid
				);				
				$this->_view->set('userData', $userData);					
			}
			catch (Exception $e) {
				$this->_setView('viewmenu');
				$this->_view->set('title', 'No data!');
				$this->_view->set('idmenu', $idMenu);
				$this->_view->set('formData', $_POST);
				$this->_view->set('saveError', $e->getMessage());
			}
			$this->_view->output();
		}		
		else{
			$this->restrictAccess();
		}
	}

	public function processCalls($idMenu){
		if(defined('_LOGIN')){
			$mapEvents=array();
			$countersModel = new CountersCallsModel();
			$menuOverviewModel = new MenuOverviewModel();
			$menuOverview = $menuOverviewModel->getMenuOverviewById($idMenu);
			$this->_setView('hitscall');
			$this->_view->set('toolExport', true);
			$this->_view->set('title', 'View Hits for Calls: '.$idMenu);
			$this->_view->set('nameMenu', $menuOverview->name);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('view', 'call');
			$this->_view->set('menu', true);
			$this->_view->set('dailyMin', DAILY_MIN);
			$this->_view->set('dailyMax', DAILY_MAX);
			$this->_view->set('dailyLimit', DAILY_LIMIT);
			$result = $this->_model->getMenuFromDb($idMenu);
			if($result)
				$this->_view->set('result', $result);
			$errors = array();
			$check = true;
			$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : (isset($_SESSION['date1']) ? $_SESSION['date1'] : NULL);
			$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : (isset($_SESSION['date2']) ? $_SESSION['date2'] : NULL);
			$cosid = isset($_POST['cosid']) ? $_POST['cosid'] : (isset($_SESSION['cosid']) ? $_SESSION['cosid'] : NULL);
			
			$_SESSION['date1'] = $d1;
			$_SESSION['date2'] = $d2;
			$_SESSION['cosid'] = $cosid;
			
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if(isset($data))
				$this->_view->set('data', $data);
			
			if (empty($d1))
			{
				$check = false;
				array_push($errors, "Date 1 is required!");
			}
			if (empty($d2))
			{
				$check = false;
				array_push($errors, "Date 2 is required!");
			}
			if (empty($cosid))
			{
				$check = false;
				array_push($errors, "CosId is required!");
			}
			if (!$check)
			{
				$this->_setView('hitscall');
				$this->_view->set('title', 'Invalid form data!');
				$this->_view->set('idmenu', $idMenu);
				if($result)
					$this->_view->set('result', $result);
				$this->_view->set('errors', $errors);
				$this->_view->set('formData', $_POST);
			}
		
			try {
				$events = $this->_model->processLog4Calls($idMenu,$d1,$d2,$cosid);
					
				if(!empty($events)){
					foreach($events as $hits){
						$count=0;
						$columns = $countersModel->getColumnNames($idMenu);
						$this->logger->debug("ora: ".sprintf("%02d",$hits['hour']));
						foreach ($columns as $eventStruct){
							$event=$eventStruct['Field'];
							list($idsubmenu,$iditem) = explode("_",$event);
				
							if(!empty($mapEvents[$idsubmenu][$iditem])){
								$mapEvents[$idsubmenu][$iditem] += $hits[$event];
							}
							else{
								$mapEvents[$idsubmenu][$iditem] = $hits[$event];
							}
							$mapClick4Hours[sprintf("%02d",$hits['hour'])][$event] += $hits[$event];
						}
						asort($mapClick4Hours);
						$_SESSION['dataHistogram']=$mapClick4Hours;
					}
				}			
				$this->logger->error("Stop loop");
				//$this->logger->debug("EVENTS MAP:".print_r($mapEvents,true));
				$this->_view->set('events', $mapEvents);
				//$_SESSION['events'] = $events;
					
			
				$this->_view->set('formData', $_POST);
			
				$data = array(
						'd1'    => $d1,
						'd2'    => $d2,
						'cosid' => $cosid
				);
				$this->_view->set('userData', $data);
			}
			catch (Exception $e) {
				$this->_setView('hitscall');
				$this->_view->set('title', 'There was an error saving the data!');
				$this->_view->set('formData', $_POST);
				$this->_view->set('saveError', $e->getMessage());
			}
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}	
	
	public function processMsisdn($idMenu){
		if(defined('_LOGIN')){
			//$_SESSION['idMenuOverview']=$idMenu;
			$mapEvents=array();
			$countersModel     = new CountersMenuOverviewModel();
			$menuOverviewModel = new MenuOverviewModel();
			$calls2Model       = new Calls2Model();
			$menuOverview = $menuOverviewModel->getMenuOverviewById($idMenu);
			$this->_setView('hitsMsisdn');
			$this->_view->set('toolExport', true);
			$this->_view->set('title', 'View Hits for MSISDN: '.$idMenu);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('menu', true);
			$this->_view->set('dailyMin', DAILY_MIN);
			$this->_view->set('dailyMax', DAILY_MAX);
			$this->_view->set('dailyLimit', DAILY_LIMIT);
			$result = $this->_model->getMenuFromDb($idMenu);
			if($result)
				$this->_view->set('result', $result);
			$errors = array();
			$check = true;
			$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : (isset($_SESSION['date1']) ? $_SESSION['date1'] : NULL);
			$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : (isset($_SESSION['date2']) ? $_SESSION['date2'] : NULL);
			$cosid = isset($_POST['cosid']) ? trim($_POST['cosid']) : (isset($_SESSION['cosid']) ? $_SESSION['cosid'] : NULL);
			$_SESSION['date1'] = $d1;
			$_SESSION['date2'] = $d2;
			$_SESSION['cosid'] = $cosid;
			
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if(isset($data))
				$this->_view->set('data', $data);
			$calls2Model->setDateStart($d1);
			$calls2Model->setDateEnd($d2);
			$calls2Model->setCosid($cosid);
			
			if (empty($d1))
			{
				$check = false;
				array_push($errors, "Date 1 is required!");
			}
			if (empty($d2))
			{
				$check = false;
				array_push($errors, "Date 2 is required!");
			}
			if (empty($cosid))
			{
				$check = false;
				array_push($errors, "CosId is required!");
			}
			if (!$check)
			{
				$this->_setView('hitsMsisdn');
				$this->_view->set('title', 'Invalid form data!');
				$this->_view->set('idmenu', $idMenu);
				if($result)
					$this->_view->set('result', $result);
				$this->_view->set('errors', $errors);
				$this->_view->set('formData', $_POST);
			}
		
			try {
				$columns = $countersModel->getColumnNames($idMenu);
				foreach ($columns as $eventStruct){
					$event=$eventStruct['Field'];
					$numCalls4Msisdn = $calls2Model->countMsisdn($event);
					list($idsubmenu,$iditem) = explode("_",$event);	
					foreach ($numCalls4Msisdn as $msisdn){
						$mapEvents[$idsubmenu][$iditem] += $msisdn['numMsisdn'];							
					}
				}			
				$this->_view->set('events', $mapEvents);
				$this->_view->set('formData', $_POST);
		
				$data = array(
						'd1'    => $d1,
						'd2'    => $d2,
						'cosid' => $cosid
				);
				$this->_view->set('userData', $data);
			}
			catch (Exception $e) {
				$this->_setView('hitsMsisdn');
				$this->_view->set('title', 'There was an error saving the data!');
				$this->_view->set('formData', $_POST);
				$this->_view->set('saveError', $e->getMessage());
			}
			$this->_view->output();
		}
		else{
			$this->restrictAccess();
		}
	}

	public function processAjaxMsisdn($idMenu){
			//$_SESSION['idMenuOverview']=$idMenu;
			$mapEvents=array();
			$countersModel = new CountersMenuOverviewModel();
			$calls2Model = new Calls2Model();
			$this->_setView('ajaxMsisdn');
			$this->_view->set('toolExport', true);
			$this->_view->set('title', 'View Hits for MSISDN: '.$idMenu);
			$this->_view->set('nameMenu', $menuOverview->name);
			$this->_view->set('idmenu', $idMenu);
			$this->_view->set('menu', true);
			$this->_view->set('view', 'msisdn');
			$this->_view->set('dailyMin', DAILY_MIN);
			$this->_view->set('dailyMax', DAILY_MAX);
			$this->_view->set('dailyLimit', DAILY_LIMIT);
			$result = $this->_model->getMenuFromDb($idMenu);
			if($result)
				$this->_view->set('result', $result);
			$errors = array();
			$check = true;
			$d1    = isset($_POST['date1']) ? trim($_POST['date1']) : (isset($_SESSION['date1']) ? $_SESSION['date1'] : NULL);
			$d2    = isset($_POST['date2']) ? trim($_POST['date2']) : (isset($_SESSION['date2']) ? $_SESSION['date2'] : NULL);
			$cosid = isset($_POST['cosid']) ? $_POST['cosid'] : (isset($_SESSION['cosid']) ? $_SESSION['cosid'] : NULL);
			
			$_SESSION['date1'] = $d1;
			$_SESSION['date2'] = $d2;
			$_SESSION['cosid'] = $cosid;
			
			$data = array();
			if(isset($_SESSION['date1'])){
				$data['date1']= $_SESSION['date1'];
			}
			if(isset($_SESSION['date2'])){
				$data['date2']= $_SESSION['date2'];
			}
			if(isset($_SESSION['cosid'])){
				$data['cosid']= $_SESSION['cosid'];
			}
			if($data)
				$this->_view->set('data', $data);
			$calls2Model->setDateStart($d1);
			$calls2Model->setDateEnd($d2);
			$calls2Model->setCosid($cosid);
				
			if (empty($d1))
			{
				$check = false;
				array_push($errors, "Date 1 is required!");
			}
			if (empty($d2))
			{
				$check = false;
				array_push($errors, "Date 2 is required!");
			}
			if (empty($cosid))
			{
				$check = false;
				array_push($errors, "CosId is required!");
			}
			if (!$check)
			{
				$this->_setView('hitsMsisdn');
				$this->_view->set('title', 'Invalid form data!');
				$this->_view->set('idmenu', $idMenu);
				if($result)
					$this->_view->set('result', $result);
				$this->_view->set('errors', $errors);
				$this->_view->set('formData', $_POST);
			}
	
			try {
				$columns = $countersModel->getColumnNames($idMenu);
				foreach ($columns as $eventStruct){
					$event=$eventStruct['Field'];
					$numCalls4Msisdn = $calls2Model->countMsisdn($event);
					list($idsubmenu,$iditem) = explode("_",$event);
					foreach ($numCalls4Msisdn as $msisdn){
						$mapEvents[$idsubmenu][$iditem] += $msisdn['numMsisdn'];
					}
				}
				$this->_view->set('events', $mapEvents);
				$this->_view->set('formData', $_POST);
	
				$data = array(
						'd1'    => $d1,
						'd2'    => $d2,
						'cosid' => $cosid
				);
				$this->_view->set('userData', $data);
			}
			catch (Exception $e) {
				$this->_setView('ajaxMsisdn');
				$this->_view->set('title', 'There was an error saving the data!');
				$this->_view->set('formData', $_POST);
				$this->_view->set('saveError', $e->getMessage());
			}
			$this->_view->output();
	
	}	
	
	public function viewMenu($dataMenu){
		$this->_view->set('title', 'View Menu Overview');
		$result = $this->_model->getMenu();
		if(!empty($result))
			$this->_view->set('dataMenu', $result);
		$this->_view->output();
	}
	public function addnote($idMenu){
		try {
			$menuOverview = new MenuOverviewModel();
			$note = isset($_POST['note']) ? trim(strip_tags($_POST['note'])) : NULL;
			if($note){
				$menuOverview->setNote($note);
				$menuOverview->updateNote($idMenu);
			}
		}
		catch (PDOException $e){
			$this->logger->debug($e);
			return false;
		}
	}
	public function save($xmlfile = 'menu.xml'){
		$idmenu=0;
		$this->readXML($xmlfile);
		//$this->logger->debug("XML:".print_r($this->xml,true));
		if(!empty($this->xml)){
			try {
				$idmenu = $this->_model->store($this->xml,$xmlfile, $note);
				$host  = $_SERVER['HTTP_HOST'];
				header("Location: http://$host/xml/viewdb/".$idmenu,false);
				return true;
			}
			catch (PDOException $e){
				$this->logger->debug($e);
				return false;
			}
		}
	}
}