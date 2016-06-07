<?php
session_start();
class SliderModel extends Model
{
	public function register($view,$threshold){
		if(!empty($threshold)){
			$_SESSION[$view]['threshold'] = $threshold;
		}
		else{
			unset($_SESSION[$view]['threshold']);
		}
	}
}