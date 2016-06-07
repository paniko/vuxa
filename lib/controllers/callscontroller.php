<?php
session_start();
class CallsController extends Controller
{
	
	public function histogram($idItem)
	{
		$outHistogram = array();
		$timeHistogram = array();
		$this->_view->set('title', 'Read Schema Menu');
		$this->_setView('histogram_json');
		$idMenu    = isset($_POST['idMenu']) ? trim($_POST['idMenu']) : NULL;
		$keypress  = isset($_POST['keypress']) ? trim($_POST['keypress']) : NULL;
		$data = array(
				'idMenu'    => $idMenu,
				'keypress'  => $keypress
		);
		//init array map
		for($i=0 ; $i <= 24 ; $i++ ){
			$hTxt = sprintf("%02s", $i);
			$timeHistogram[$hTxt] = 0;
		}
		$strMatch = $idMenu."_".$keypress;
		foreach($timeHistogram as $time => $value){
			if(!empty($_SESSION['dataHistogram'][$time])){
				$outHistogram[] = (object) array( 'y' => $time, 'a' => $_SESSION['dataHistogram'][$time][$strMatch] );
			}
			else{
				$outHistogram[] = (object) array( 'y' => $time, 'a' => 0 );
			}
		}	
		$this->_view->set('dataHistogram', $outHistogram);
		return $this->_view->output();
	}
}