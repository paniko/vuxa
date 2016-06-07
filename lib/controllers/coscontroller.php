<?php

class CosController extends Controller
{
	protected $xml=null;
	
	
	public function listcos($idMenuOverview)
	{
		$this->_view->set('title', 'Read Schema Menu');
		$this->_setView('list');
 		$result = $this->_model->getListCos($idMenuOverview);
 		if(!empty($result))
 			$this->_view->set('listCos', $result);
 		return $this->_view->output();
	}

}