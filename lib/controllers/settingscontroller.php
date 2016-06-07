<?php

class SettingsController extends Controller
{
	public function index()
	{
		$this->_view->set('title', 'Settings');
		$result = $this->_model->loadSetting();
		if(!empty($result))
			$this->_view->set('userData', $result);
		return $this->_view->output();
	}

	public function save()
	{
		if (!isset($_POST['settingsFormSubmit']))
		{
			header('Location: /settings/index');
		}
		
		$errors = array();
		$check = true;
			
		$contentRowFilter = isset($_POST['content_row_filter']) ? trim($_POST['content_row_filter']) : NULL;
		$separationChar   = isset($_POST['separation_char'])    ? trim($_POST['separation_char']) : NULL;
		$datePosition     = isset($_POST['date_position'])      ? trim($_POST['date_position']) : "";
		$timePosition     = isset($_POST['time_position'])      ? trim($_POST['time_position']) : "";
			
		if (empty($contentRowFilter))
		{
			$check = false;
			array_push($errors, "Content row filter is required!");
		}
		/*
		else if (!filter_var( $email, FILTER_VALIDATE_EMAIL ))
		{
			$check = false;
			array_push($errors, "Invalid E-mail!");
		}
		*/
		if (empty($separationChar))
		{
			$check = false;
			array_push($errors, "Separation char is required!");
		}

		if (!$check)
		{
			$this->_setView('index');
			$this->_view->set('title', 'Invalid form data!');
			$this->_view->set('errors', $errors);
			$this->_view->set('formData', $_POST);
			return $this->_view->output();
		}
			
		try {
					
			$setting = new SettingsModel();
			$setting->setContentRowFilter($contentRowFilter);
			$setting->setSeparationChar($separationChar);
			$setting->setDatePosition($datePosition);
			$setting->setTimePosition($timePosition);
			$setting->store();
					
			$this->_setView('success');
			$this->_view->set('title', 'Store success!');
			$this->_view->set('message', 'Store success!');
					
			$data = array(
				'contentRowFilter' => $contentRowFilter,
				'separationChar'   => $separationChar,
				'datePosition'     => $datePosition,
				'timePosition'     => $timePosition
			);
					
			$this->_view->set('userData', $data);
					
		} catch (Exception $e) {
			$this->_setView('index');
			$this->_view->set('title', 'There was an error saving the data!');
			$this->_view->set('formData', $_POST);
			$this->_view->set('saveError', $e->getMessage());
		}

		return $this->_view->output();
	}
}