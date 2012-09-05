<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adopsreport extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper('url');	
		$this->load->model('report/adopsreport_model', 'adopsreport_model');		
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
		$advertiser_name_autocomplete 		= autocomplete_widget('gad_advertiser', 'advertiser_name');		
		$order_name_autocomplete 			= autocomplete_widget('gad_order', 'order_name');
		$ad_name_autocomplete 				= autocomplete_widget('gad_ad', 'ad_name');
		$creative_name_autocomplete 		= autocomplete_widget('gad_creative', 'creative_name');
		$creative_format_autocomplete 		= autocomplete_widget('creative', 'creative_format');
		$ad_size_autocomplete 				= autocomplete_widget('gad_ad_size', 'ad_size');
			
	
		$data['title'] = ucfirst('Camapign report'); // Capitalize the first letter
		$data['advertiser_name_autocomplete']	=	$advertiser_name_autocomplete;
		$data['order_name_autocomplete']		=	$order_name_autocomplete;
		$data['ad_name_autocomplete']			=	$ad_name_autocomplete;
		$data['creative_name_autocomplete']		=	$creative_name_autocomplete;
		$data['creative_format_autocomplete']	=	$creative_format_autocomplete;
		$data['ad_size_autocomplete']			=	$ad_size_autocomplete;
					
		$this->form_validation->set_rules('report-name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/campaign' , $data);						
			}else{
				echo "successfully added the record";
			}			
				
	}
	
	function autocomplete(){
		$table_name = $_GET['table_name'];
		$term 		= $_GET['term'];
		
		$this->load->model('report/campaign_model', 'campaign_model');		
		echo $autocompleteData = json_encode($this->campaign_model->getAutoCompleteResultSet($table_name, $term));
		exit;		
		
	}
	
	function getdimensions(){
		echo $json_dimensions = json_encode($this->adopsreport_model->getDimensions());		
		exit;
	}
	function getmetrics(){
		echo $json_metrics = json_encode($this->adopsreport_model->getMetrics());		
		exit;
	}	
	function getfilters(){
		echo $json_filters = json_encode($this->adopsreport_model->getFilters());		
		exit;
	}

	function getAdIDs(){
		echo "true";
	}
}
