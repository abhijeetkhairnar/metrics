<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends My_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper('url');		    
	}
		
	function index()
	{
		$data['title'] = ucfirst('Dashbord page'); // Capitalize the first letter
		$this->load->template('dashboard/dashboard', $data);	
		
	}
}
