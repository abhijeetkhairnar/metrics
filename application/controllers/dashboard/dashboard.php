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
		$this->load->template('dashboard/dashboard', $data);	
		
	}
}
