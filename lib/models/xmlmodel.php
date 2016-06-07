<?php
class XmlModel extends Model
{
	public function loadXml($fileXml)
	{
		$xml = simplexml_load_file($fileXml);
		
		$outData = $xml->menuoverview;
		//$this->logger->debug("XML:".print_r($outData,true));
// 		if(isset($outData)){
// 			try {
// 				$this->store($outData);
// 			}
// 			catch (Exception $e){
// 				$this->logger->debug($e);
// 			}
// 		}
		return $outData;
	}
	
	public function setDefault($idMenu){
		$menuModel    = new MenuOverviewModel();
		$menuModel->updateDefault($idMenu);
	}
	
	public function deleteMenu($idMenu){
		$menuOverviewModel = new MenuOverviewModel();
		$countersModel     = new CountersMenuOverviewModel();
		$countersCallModel = new CountersCallsModel();
		$menuOverviewModel->delete($idMenu);
		$countersModel->delete($idMenu);
		$countersCallModel->delete($idMenu);
		
	}
	public function store($xmlData,$xmlFile, $note="")
	{
		$menuModel    = new MenuOverviewModel();
		$submenuModel = new MenuModel();
		$itemModel    = new ItemsModel();
		$menuItem     = new MenuItemsModel();
		$cosModel     = new CosModel();
		$itemsCos     = new ItemsCosModel();
		$countersMenuOverview = new CountersMenuOverviewModel();
		
		$mapMenu = array();
		$mapLink = array();
		$fields  = array();
		
		$md5_xmlfile = (string) md5_file(UPLOADSXML.$xmlFile);
		$isLoad = $menuModel->findByMd5XmlFile($md5_xmlfile);
		$this->logger->debug("isLoad ".$md5_xmlfile.": ".$isLoad);
		if($isLoad != false){//is already load
			$idMenu = $isLoad;
		}
		else{
			foreach($xmlData as $menuoverview){
				//insert MenuOverview in table
				$menuModel->setName($menuoverview['name']);
				$menuModel->setFilename($xmlFile);
				$menuModel->setMd5XmlFile($md5_xmlfile);
				$menuModel->setNote($note);
				$menuModel->save();
				$idMenuOverview = $menuModel->lastID;
	
				//insert Menu in table
				if(isset($menuoverview->menu)){
					foreach ($menuoverview->menu as $submenu){
						$submenuModel->setName($submenu->title);
						$submenuModel->setLevel($submenu->level);
						$submenuModel->setOrder($submenu->order);
						$submenuModel->setEntrypoint($submenu->entrypoint);
						$submenuModel->setKsMenuOverview($idMenuOverview);
						$submenuModel->save();
						$id = (int)$submenu['id']; 
						$mapLink[$id] = $submenuModel->lastID;
					}
					//$this->logger->debug(print_r($mapLink,true));
					foreach ($menuoverview->menu as $submenu){
						$id = (int)$submenu['id'];
						//insert voci menu in tabella
						if(isset($submenu->items)){
							foreach ($submenu->items->item as $item){
								$this->logger->debug("label:".$item->label);
								$link = $mapLink[(int)$item->link];
								//$this->logger->debug("link:".$link);
								$itemModel->setLabel($item->label);
								$itemModel->setKey($item->key);
								$itemModel->setOrderView($item->order);
								$itemModel->setKsMenu($mapLink[$id]);
								$itemModel->save();	
								//tabella relazione molti a molti
								$menuItem->setKsItem($itemModel->lastID);
								$menuItem->setksMenuParent($mapLink[$id]);
								$menuItem->setksMenu($link);
								$menuItem->save();								
								//insert voci cos in tabella
								if(isset($item->cos)){
									foreach ($item->cos->active as $cosid){
										//verify if the cosid already insert in table
										$idcosExist = $cosModel->isExist($cosid);
										//$this->logger->debug("XML cosid:".$cosid);
										//$this->logger->debug("isExist cosid:".$idcosExist);
										if($idcosExist==false){
											$cosModel->setCosid($cosid);
											$cosModel->setKsMenuOverview($idMenuOverview);
											$cosModel->save();
											$idcosExist=$cosModel->lastID;
										}
										$itemsCos->setksCos($idcosExist);
										$itemsCos->setKsItem($itemModel->lastID);
										$itemsCos->save();
									}
								}
								$fields[] = $mapLink[$id]."_".$item->key;
							}
						}				
					}
				}	
			}
			$countersMenuOverview->create($idMenuOverview, $fields);
			
			$idMenu = $menuModel->lastID;
		}
		return $idMenu;
	}
	
	public function getXMLUploaded(){
		$menuModel      = new MenuOverviewModel();
		$result = $menuModel->getMenuOverview();
	
		if (empty($result))
		{
			return false;
		}
	
		return $result;
	
	}
		
	public function getMenuFromDb($idMenu){
		$result = array();
		$menuModel      = new MenuOverviewModel();
		$submenuModel   = new MenuModel();
		$itemModel      = new ItemsModel();
		$menuItemsModel = new MenuItemsModel();
		$itemsCosModel  = new ItemsCosModel();
		//Find Menu
		$submenu = $submenuModel->getMenuById($idMenu);
		$result['submenu'] = $submenu;
		$menu = $menuItemsModel->getMenu($idMenu);
		$result['menu'] = $menu;
		//Find Cos
		$cos = $itemsCosModel->getCosActive($idMenu);
		$result['cos'] = $cos;
		
		return $result;	
	}
	
	public function processLog($idMenuOverview, $dateStart, $dateEnd, $cosid=null){
		$countersModel = new CountersMenuOverviewModel();
		$countersModel->setIdMenuOverview($idMenuOverview);
		$countersModel->setCosid($cosid);
		$events = $countersModel->findByDate($idMenuOverview, $dateStart, $dateEnd);			

		return $events;
	}	
	public function processLog4Calls($idMenuOverview, $dateStart, $dateEnd, $cosid=null){
		$countersModel = new CountersCallsModel();
		$countersModel->setIdMenuOverview($idMenuOverview);
		$countersModel->setCosid($cosid);
		$events = $countersModel->findByDate($idMenuOverview, $dateStart, $dateEnd);
		
		return $events;
	}
	public function processLog4MSISDN($idMenuOverview, $dateStart, $dateEnd, $cosid=null){
		$callsModel = new Calls2Model();
		$callsModel->setDateStart($dateStart);
		$callsModel->setDateEnd($dateEnd);
		$callsModel->setCosid($cosid);
		$events = $callsModel->findByDate();
		return $events;
	}	
}