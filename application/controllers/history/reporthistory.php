<?php
/******************************************************
*	FileName 	 : reporthistory.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report History Controller file.
*	Version 	 : 1.0                  
******************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reporthistory extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper('url');	
        $this->load->model('history/reporthistory_model', 'reporthistory_model');            
    }
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 10 Oct 2012
	*	Description	 : Default report history controller.
	*********************************************/	
	function index()
	{
		$data['title'] = 'Report history';
		
		
		
		/**************************************************************************************/

			$reportHistory = $this->reporthistory_model->getReportHistory();

		/**************************************************************************************/
		
		$this->load->template('history/reporthistory' , $data);						
	}

}
