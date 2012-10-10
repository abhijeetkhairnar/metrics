<?php 
/******************************************************
*	FileName 	 : dashboard.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 30 Aug 2012.
*	Description	 : Dashboard Controller file.
*	Version 	 : 1.0                  
******************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends My_Controller {

	function __construct(){
            parent::__construct();
            $this->load->helper('url');	
	}

	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 30 Aug 2012.
	*	Description	 : Default dashboard controller.
	*********************************************/		
	function index()
	{
		$data['title'] = 'Dashbord page';
		$resultSet		=	0;
		$user_id = 104;
		$this->load->model('dashboard/dashboard_model', 'dashboard_model');
		$resultSet = $this->dashboard_model->getReportList($user_id);
		$data['resultSet']	=	count($resultSet);	
		
		$this->load->template('dashboard/dashboard', $data);	
		
	}
	
	/*********************************************
	*	Created By 	 : Amin S.
	*	Created Date : 26-Sep-2012.
	*	Description	 : List of Report 
	*********************************************/		
	function getReportList()
	{		
		$user_id = 104;
		$this->load->model('dashboard/dashboard_model', 'dashboard_model');
		$rec = json_encode($this->dashboard_model->getReportList($user_id));	
		echo $rec;
		exit;
	}
	
}
