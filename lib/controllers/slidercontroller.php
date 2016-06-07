<?php
class SliderController extends Controller
{
	public function store($view){
		$threshold = isset($_POST['threshold']) ? $_POST['threshold'] : NULL;
		if(!empty($threshold)){
			$this->_model->register($view,$threshold);
		}
	}
}