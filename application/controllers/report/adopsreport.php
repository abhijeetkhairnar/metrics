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
		
		$this->load->helper('autocomplete');
		$advertiser_name_autocomplete 		= autocomplete_widget('advertiser_name');
		$advertiser_id_autocomplete			= autocomplete_widget('advertiser_id');
		$order_name_autocomplete 			= autocomplete_widget('order_name');
		$order_id_autocomplete 				= autocomplete_widget('order_id');
		$ad_name_autocomplete 				= autocomplete_widget('ad_name');
		$ad_id_autocomplete 				= autocomplete_widget('ad_id');
		$creative_name_autocomplete 		= autocomplete_widget('creative_name');
		$creative_id_autocomplete 			= autocomplete_widget('creative_id');
		$ad_size_autocomplete 				= autocomplete_widget('ad_size');
	
		$data['title'] = ucfirst('Camapign report'); // Capitalize the first letter
		$data['advertiser_name_autocomplete']	=	$advertiser_name_autocomplete;
		$data['advertiser_id_autocomplete']		=	$advertiser_id_autocomplete;
		$data['order_name_autocomplete']		=	$order_name_autocomplete;
		$data['order_id_autocomplete']			=	$order_id_autocomplete;
		$data['ad_name_autocomplete']			=	$ad_name_autocomplete;
		$data['ad_id_autocomplete']				=	$ad_id_autocomplete;
		$data['creative_name_autocomplete']		=	$creative_name_autocomplete;
		$data['creative_id_autocomplete']		=	$creative_id_autocomplete;
		$data['ad_size_autocomplete']			=	$ad_size_autocomplete;
					
		$this->form_validation->set_rules('report-name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/campaign' , $data);						
			}else{
				echo "successfully added the record";
			}			
				
	}
	
}
