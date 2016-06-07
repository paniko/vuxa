<?php

class StatisticController extends Controller
{

	public function histogram($idItem)
	{
		$this->_view->set('title', 'Read Schema Menu');
		$this->_setView('histogram');
		$result = $this->_model->statistic4hours($idItem);
		if(!empty($result))
			$this->_view->set('histogram', $result);
		return $this->_view->output();
	}
}