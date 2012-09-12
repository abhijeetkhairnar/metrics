<?php 
/******************************************************
*	FileName 	 : adopsreport.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report Controller file.
*	Version 	 : 1.0                  
******************************************************/
error_reporting(E_ERROR | E_WARNING | E_PARSE);	// Report simple running errors
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Adopsreport extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->helper('url');	
		$this->load->model('report/adopsreport_model', 'adopsreport_model');		
    }
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Default report controller.
	*********************************************/	
	function index()
	{
		$data['title'] = 'Reports';		
		$this->load->template('report/default' , $data);						
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Updated Date : 12 Sept 2012.
	*	Description	 : Standard report controller.
	*********************************************/	
	function standard()
	{	
		$this->load->helper('log4php');
		$this->load->helper('form');
		$this->load->library('form_validation');		
		
		log_info('Into the standard report controller');
		
		$data['title'] = 'Standard report';

		$this->form_validation->set_rules('report-name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/standard' , $data);						
			}else{
				unset($_POST['dimensions_left_search']);
				unset($_POST['dimensions_right_search']);
				unset($_POST['dimensions_right_chk']);
				unset($_POST['dimensions_right_chk_chk']);
												
				unset($_POST['metrics_left_search']);				
				unset($_POST['metrics_right_search']);
				unset($_POST['metrics_right_chk']);
				unset($_POST['metrics_right_chk_chk']);
								
				unset($_POST['filters_left_search']);
				unset($_POST['filters_right_search']);
				unset($_POST['filters_right_chk']);
				unset($_POST['metrics_right_chk_chk']);
				
				echo "<pre>"; print_r($_POST); exit;
			}	
	}
	
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Campaign report controller.
	*********************************************/		
	function campaign()
	{
		$this->load->helper('log4php');
		log_info('amin for testing...');
		$this->load->helper('form');
		$this->load->library('form_validation');		
		$this->load->helper('autocomplete');
		
		/*---------------------------------------------------------------------------------------------*/		
		if ($_POST){
			//echo "<pre>"; print_r($_POST); echo "</pre>";
			$dimCamp 		=	array ('ADM_ADS.AD_ID' => 'Ad ID', 'DATE_ID AS DAILY' => 'Day');
			$FilterCamp		=   array(array('ADM_ADS.AD_ID' => 5000065339));
			$MetCamp		=	array('impression' => 'Impressions');
			$date_range_desc	=	'CUSTOM';
			$isrunreport		=   0;
			$date_range_num		=	'';
			$deliverymethod		=	0;
			$CampReportType		= 	6;
			
			$tempStartDateArr	=  explode('/', substr($_POST['startDate'], 0, 10));
			$startDateRange		= $tempStartDateArr[2] . $tempStartDateArr[1] . $tempStartDateArr[0];		
			
			$tempEndDateArr		=  explode('/', substr($_POST['endDate'], 0, 10));
			$endDateRange		= $tempEndDateArr[2] . $tempEndDateArr[1] . $tempEndDateArr[0];
						
			$date_range = array($startDateRange, $endDateRange);
			
			$report_insert  = array ('sReportName' 		=> $_POST['report-name'],
									 'iReportSharing' 	=> 0,					//$_POST['report_sharing']
									 'iReportType' 		=> $CampReportType,		//$CampReportType
									 'iUpdatedBy' 		=> 104,					//$this->getUser()->getAttribute("userid")
									 'isShared' 		=> '',
									 'DIMENSION_LIST' 	=> $dimCamp,
									 'REPORT_TYPE_VAL' 	=> $CampReportType,
									 'FILTER_LIST' 		=> $FilterCamp,
									 'METRIC_LIST' 		=> $MetCamp,
									 'isrunreport' 		=> $isrunreport,
									 'startDate' 		=> date('c', strtotime('now')),
									 'endDate' 			=> date('c', strtotime('now')),
									 'active' 			=> '1',
									 'frequencyType' 	=> 'now',
									 'frequencyValue' 	=> '',
									 'RUN_TYPE' 		=> $CampReportType,
									 'TIME_ZONE' 		=> 'CAMPAIGN_EST',
									 'DATE_RANGE_DESC' 	=> $date_range_desc,
									 //'DATE_RANGE_NUM' 	=> $date_range_num,
									 'is_scheduled' 	=> '0',
									 'INCLUDE_HEADER' 	=> '0',
									 'REPORT_DESC' 		=>$_POST['description'],
									 'deliverymethod' 	=> $deliverymethod,
									 'emails' 			=> '', // trim($_POST['deliverymethod_emaillist']) -1/3
									 'DATE_RANGE_LIST'	=> $date_range
								);
		
		
		//echo "<pre>"; print_r($report_insert);
		$this->load->library('reportutilsclass');
		//exit;
		$processed_data[] = $this->reportutilsclass->dataProcess($report_insert, 'insert');
		echo "<pre>"; print_r($processed_data);
		$aRecordDataOut = $this->reportutilsclass->ReportThroughAPI($processed_data,'createReports');;
		echo "<pre>"; print_r($aRecordDataOut);
		}
		/***************************************************************************************/		
		
		$advertiser_name_autocomplete 		= autocomplete_widget('gad_advertiser', 'advertiser_name');		
		$order_name_autocomplete 			= autocomplete_widget('gad_order', 'order_name');
		$ad_name_autocomplete 				= autocomplete_widget('gad_ad', 'ad_name');
		$creative_name_autocomplete 		= autocomplete_widget('gad_creative', 'creative_name');
		$creative_format_autocomplete 		= autocomplete_widget('creative', 'creative_format');
		$ad_size_autocomplete 				= autocomplete_widget('gad_ad_size', 'ad_size');
		
		$data['title'] = 'Campaign report';
		$data['advertiser_name_autocomplete']	=	$advertiser_name_autocomplete;
		$data['order_name_autocomplete']		=	$order_name_autocomplete;
		$data['ad_name_autocomplete']			=	$ad_name_autocomplete;
		$data['creative_name_autocomplete']		=	$creative_name_autocomplete;
		$data['creative_format_autocomplete']	=	$creative_format_autocomplete;
		$data['ad_size_autocomplete']			=	$ad_size_autocomplete;
					
		$this->form_validation->set_rules('report_name', 'Report name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		
			if ($this->form_validation->run() === FALSE){
				$this->load->template('report/campaign' , $data);						
			}else{
				echo "successfully added the record";
			}			
				
	}
	
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Autocomplete call controller.
	*********************************************/		
	function autocomplete(){
		$table_name = $_GET['table_name'];
		$term 		= $_GET['term'];		
		$this->load->model('report/campaign_model', 'campaign_model');		
		echo $autocompleteData = json_encode($this->campaign_model->getAutoCompleteResultSet($table_name, $term));
		exit;				
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get dimensions in json format for DD creation.
	*********************************************/		
	function getdimensions(){
		echo $json_dimensions = json_encode($this->adopsreport_model->getDimensions());		
		exit;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get metrics in json format for DD creation.
	*********************************************/	
	function getmetrics(){
		echo $json_metrics = json_encode($this->adopsreport_model->getMetrics());		
		exit;
	}	
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get filters in json format for DD creation.
	*********************************************/	
	function getfilters(){
		echo $json_filters = json_encode($this->adopsreport_model->getFilters());		
		exit;
	}

	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Get Ad ids controller.
	*********************************************/
	function getAdIDs(){
		$this->load->model('report/campaign_model', 'campaign_model');	
		echo $autocompleteData = json_encode($this->campaign_model->getAdIds());
		exit;
	}
}