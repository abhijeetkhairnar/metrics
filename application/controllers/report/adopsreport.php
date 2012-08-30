<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adopsreport extends My_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper('url');		
    }
	
	function index()
	{
		$data['title'] = ucfirst('Reports'); // Capitalize the first letter
		$this->load->template('report/default' , $data);						
	}

	function standard()
	{	
		$this->load->helper('form');
		$this->load->library('form_validation');		
	
		$data['title'] = ucfirst('Standard report'); // Capitalize the first letter
	
		$this->form_validation->set_rules('report-name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/standard' , $data);						
			}else{
				echo "successfully added the record";
			}	
	}
	
	function campaign()
	{	
		$this->load->helper('form');
		$this->load->library('form_validation');		
	
		//$this->load->model('CampaignModel');
		//$this->CampaignModel->advertiserName();
	
	
		$data['title'] = ucfirst('Camapign report'); // Capitalize the first letter
		$this->form_validation->set_rules('report-name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/campaign' , $data);						
			}else{
				echo "successfully added the record";
			}			
				
	}
	
}
