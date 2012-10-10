<?php 
/******************************************************
*	FileName 	 : adopsreport.php
*	Created By 	 : Amin S/Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report Controller file.
*	Version 	 : 1.0                  
******************************************************/
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
	*	Created By 	 : Amin S
	*	Created Date : 25 Aug 2012.
	*	Updated Date : 12 Sept 2012.
	*	Description	 : Standard report controller.
	*********************************************/	
	function standard()
	{	
               //echo "<pre>"; print_r($this->session); echo "</pre>";exit;
                
		$this->load->helper('log4php');
		$this->load->helper('form');
		$this->load->library('form_validation');		
			
		log_info('Into the standard report controller');
		$data['title'] = 'Standard report';
		//-- Hard Code entry for testing only --//
		$data['report_type'] = '1';				
		
		if (isset($_POST['save'])){
			$type	=	'Save';
		}else if (isset($_POST['update'])){
			$type	=	'Update';
		}else if (isset($_GET['edit'])){
			$type	=	'Edit';
		}
		echo $type;
		
					
		switch($type){		
				case 'Edit':
						// Report Edit case
						/*************************************************************************/
			
						$editData = $this->adopsreport_model->editReport($_GET['id']);				
						$postArray = array( 
										"header" => array( 
															"report_name" 		=> $editData['header']['REPORT_NAME'],
															"report_desc" 		=> $editData['header']['REPORT_DESC'],
															"is_shared" 		=> $editData['header']['IS_SHARED'],
															"is_inc_header" 	=> $editData['header']['IS_INC_HEADER'],
															"date_range_num" 	=> $editData['header']['DATE_RANGE_NUM'],
															"date_range_desc" 	=> $editData['header']['DATE_RANGE_DESC'],
															"start_date"		=> $editData['header']['START_DATE'],
															"frequency_type"	=> $editData['header']['FREQUENCY_TYPE'],
															"frequency_num"		=> $editData['header']['FREQUENCY_NUM'],
															"end_date"              => $editData['header']['END_DATE'],
															"start_date_range" 	=> $editData['header']['START_DATE_RANGE'],
															"end_date_range" 	=> $editData['header']['END_DATE_RANGE']
													)
										);
				
							for($i = 0 ; $i < count($editData['dimensions']) ; $i++){
								$arrDem[] = $editData['dimensions'][$i]['COLUMN_ID'];
							}
							$postArray['dimensions'] =  $arrDem;
							
							for($i = 0 ; $i < count($editData['metrics']) ; $i++){
								$arrMtrx[] = $editData['metrics'][$i]['COLUMN_ID'];
							}
							$postArray['metrics'] =  $arrMtrx;
							
							for($i = 0 ; $i < count($editData['filters']) ; $i++){
								$arrFil[] = $editData['filters'][$i]['COLUMN_ID'];
							}
							$postArray['filters'] =  $arrFil;
							
							for($i = 0 ; $i < count($editData['filters']) ; $i++){
								$arrFilVal[] = $editData['filters'][$i]['COLUMN_VAL'];
							}
							$postArray['filtersVal'] =  $arrFilVal;
							
							if (is_array($postArray['filters'])){
							
								$reportFilterBody = array_combine($postArray['filters'], $postArray['filtersVal']);	
							
								
								foreach($reportFilterBody as $key => $val){			
									$filterFieldType = $this->adopsreport_model->getFilterInput($key);				
									if (strtolower($filterFieldType) == 'textbox'  ||  strtolower($filterFieldType) == 'search'){										
										$reportFiltersBody[$key] = '"' .$val. '"';
									}else if (strtolower($filterFieldType) == 'listbox'){
										//$selectedFilterArr = explode(',', $val);
										$reportFiltersBody[$key] = json_encode($this->adopsreport_model->getSelectedFilterData($key, $val));
									}
									$reportDetailsFilters			= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($postArray['filters']));
								}
							}
							$reportHeader					= $postArray['header'];
							$reportDetailsDimensions		= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($postArray['dimensions']));
							$reportDetailsMetrics			= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($postArray['metrics']));
						
							self::__setDateRangeOnPage($reportHeader);			
									
							$reportHeader['report_id']	=	$_GET['id'];
							$data = array ("reportHeader"		 =>$reportHeader, 
											 "dimensions" 		 =>$reportDetailsDimensions, 
											 "metrics" 			 =>$reportDetailsMetrics, 
											 "filtersDataKey" 	 =>$reportDetailsFilters,
											 "reportFiltersBody" => $reportFiltersBody);

							break;
					
			
			case 'Save':
								// Report Save case
							
							$_POST = self::__unsetPostData($_POST);
							
							$this->load->helper('process_data');
							log_info("Add process data helper");
							
							$tempArr = dataProcess_helper($_POST);
							
							$_POST['is_run_report'] = 0;
							$_POST['start_date']	=  date('d-M-y');
							$_POST['end_date']		=  date('d-M-y');
							unset($_POST['save']);
							
							$data  =  self::__setFormData($tempArr);
						
							if (count($tempArr['error_msg']) > 0){
								$data['error_msg'] = $tempArr['error_msg'];
							}else{
							
								$savedInfo = $this->adopsreport_model->saveReport($tempArr);
								
								if ($savedInfo['validation_message'][0] == 'Validation success' && is_numeric($savedInfo['status'])){
									$data['error_msg'] = array('Report saved successfully...');					
								}else if ($savedInfo['validation_message'] == 'Validation success' && !is_numeric($savedInfo['status'])){
									$data['error_msg'] = array($savedInfo['status']);
								}else if ($savedInfo['validation_message'] != 'Validation success'){
									$data['error_msg'] = $savedInfo['validation_message'];
								}				
							}	
							break;
			
			case 'Update':
								// Report Update case
							
							$_POST = self::__unsetPostData($_POST);
							unset($_POST['update']);
							$this->load->helper('process_data');
							log_info("Add process data helper");
							
							$tempArr = dataProcess_helper($_POST);
							$_POST['is_run_report'] = 0;
							$_POST['start_date']	=  date('d-M-y');
							$_POST['end_date']		=  date('d-M-y');
							
							
							$data  =  self::__setFormData($tempArr);
						
							if (count($tempArr['error_msg']) > 0){
								$data['error_msg'] = $tempArr['error_msg'];
							}else{
							
								$savedInfo = $this->adopsreport_model->updateReport($tempArr);
								
								if ($savedInfo['validation_message'][0] == 'Validation success' && is_numeric($savedInfo['status'])){
									$data['error_msg'] = array('Report saved successfully...');					
								}else if ($savedInfo['validation_message'] == 'Validation success' && !is_numeric($savedInfo['status'])){
									$data['error_msg'] = array($savedInfo['status']);
								}else if ($savedInfo['validation_message'] != 'Validation success'){
									$data['error_msg'] = $savedInfo['validation_message'];
								}				
							}	
							break;
			
		}			
		
		if (is_array($data['reportHeader'])){
			self::__setDateRangeOnPage($data['reportHeader']);
		}		
		$this->load->template('report/standard' , $data);	
}

	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 20 Sep 2012.
	*	Description	 : Remove unwanted Information from 
						post variable
	*********************************************/	
	
	function __unsetPostData(& $postData){	
		foreach($postData as $key => $val){
			
			if(preg_match('/_left_search$/', $key)){	
				unset($postData[$key]);
			}if(preg_match('/_right_search$/', $key)){			
				unset($postData[$key]);
			}if(preg_match('/_left_chk$/', $key)){			
				unset($postData[$key]);
			}
		}
		unset($postData['dimensions_left_chk']);
		unset($postData['metrics_left_chk']);
		unset($postData['filters_left_chk']);	 
		return $postData;
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
	function getDimensions(){
		$json_dimensions = json_encode($this->adopsreport_model->getDimensions());		
		if($json_dimensions){
			echo $json_dimensions;
		}else{
			echo "[]";
		}
		exit;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get metrics in json format for DD creation.
	*********************************************/	
	function getMetrics(){
		$json_metrics = json_encode($this->adopsreport_model->getMetrics());		
		if($json_metrics){
			echo $json_metrics;
		}else{
			echo "[]";
		}
		exit;
	}	
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get filters in json format for DD creation.
	*********************************************/	
	function getFilters(){
		$json_filters = json_encode($this->adopsreport_model->getFilters());		
		if($json_filters){
			echo $json_filters;
		}else{
			echo "[]";
		}
		exit;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : 
	*********************************************/	
	function getFilterInput(){
		$filters_type = $this->adopsreport_model->getFilterInput();		
		
		if (strtolower($filters_type) == 'search'){
			echo 'Search';
		}
		else if (strtolower($filters_type) == 'listbox'){			
			echo "ListBox";
		}else {
			echo $html = '<input type="text" name="'.$_REQUEST['id'].'" id="'.$_REQUEST['id'].'" value="" size="106">';			
		}
	}	
	
	function getFilterData(){
		$content =	$this->adopsreport_model->getFilterData();		
	}

	function getFilterDataJson(){
		echo $json_content = json_encode($this->adopsreport_model->getFilterData());		
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
	
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 21 Sep 2012.
	*	Description	 : set Form Data
	*********************************************/
	function __setFormData($tempArr = array()){
		
		
		$reportFiltersBody;
		$reportHeader = array_combine($tempArr['reportDataKey'], $tempArr['reportDataVal']);
		if (count($tempArr['dimension']) > 0){
		 $reportDetailsDimensions		= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($tempArr['dimension']));
		}
		if (count($tempArr['metrics']) > 0){
		 $reportDetailsMetrics			= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($tempArr['metrics']));
		}
		if (count($tempArr['filtersDataKey']) > 0){
		 $reportDetailsFilters			= json_encode($this->adopsreport_model->getLabelnIdMappingForDD($tempArr['filtersDataKey']));
		}	
				
		if (is_array($tempArr['filtersDataKey']) && count($tempArr['filtersDataKey']) > 0){
					
			$reportFilterBody = array_combine($tempArr['filtersDataKey'], $tempArr['filtersDataVal']);	
			
			foreach($reportFilterBody as $key => $val){			
				$filterFieldType = $this->adopsreport_model->getFilterInput($key);				
				if (strtolower($filterFieldType) == 'textbox'  ||  strtolower($filterFieldType) == 'search'){
					$reportFiltersBody[$key] = '"' .$val . '"';
				}else if (strtolower($filterFieldType) == 'listbox'){
					//$selectedFilterArr = explode(',', $val);
					$reportFiltersBody[$key] = json_encode($this->adopsreport_model->getSelectedFilterData($key, $val));
				}
			}
		}
		
		/*--------------------------------------------------------------------------------
		| Scheduled form objects set
		---------------------------------------------------------------------------------*/
		
		if (is_array($reportHeader)){
			switch ($reportHeader['schedule_popup_criteria']){
			
				case '1':
							
							$reportHeader['frequency_type'] = $reportHeader['schedule_popup_type'];
							$reportHeader['frequency_val']	= 0;
							
							unset($reportHeader['schedule_popup_type']);
							break;
				
				case '2':
							$reportHeader['frequency_type'] 	= $reportHeader['schedule_popup_date_range_dsc'];
							$reportHeader['frequency_val']		= $reportHeader['schedule_popup_date_range_num'];
							
							unset($reportHeader['schedule_popup_date_range_dsc']);
							unset($reportHeader['schedule_popup_date_range_num']);
							break;				
				case '3':
							$reportHeader['start_date'] 	= date('d-M-Y', strtotime($reportHeader['schedule_popup_start_date']));
							$reportHeader['end_date']		= date('d-M-Y', strtotime($reportHeader['schedule_popup_end_date']));
							
							unset($reportHeader['schedule_popup_start_date']);
							unset($reportHeader['schedule_popup_end_date']);
							break;
			}
			
			$reportData['email'] 	= $reportHeader['schedule_popup_emails'];
		}	
		
		/*-------------------------------------------------------------------------------*/
		
		$reportData = array ("reportHeader" => $reportHeader, 
							 "dimensions" 	=>$reportDetailsDimensions, 
							 "metrics" 		=>$reportDetailsMetrics, 
							 "filtersDataKey" =>$reportDetailsFilters,
							 "reportFiltersBody" => $reportFiltersBody);
							 
		return $reportData;
	}
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 27 Sep 2012.
	*	Description	 : set Date Range Opetion
	*********************************************/
	
	function __setDateRangeOnPage(& $tempReportHeader){
		/*
		if (isset($_GET['id'])){		
			$selectedTerm = $tempReportHeader['date_range_dsc'];
		}else{
			$selectedTerm = $tempReportHeader['date_range_desc'];
		}
		*/
		$selectedTerm = $tempReportHeader['date_range_desc'];
		
		$predefinedDateRangeList = $this->config->item('predefined_date_range');		
		$lastDateRangeList = $this->config->item('last_date_range');
		
		if (in_array($selectedTerm, $predefinedDateRangeList)){
			$tempReportHeader['date_range'] = 'predefined_radio';
		}else if (in_array($selectedTerm, $lastDateRangeList)){
			$tempReportHeader['date_range'] = 'last_radio';
		}else{
			$tempReportHeader['date_range'] = 'custom_radio';
		}
		
		/*-----------------------------------------------
		| To set scheduled information...
		-----------------------------------------------*/
		
		echo "<pre>"; print_r($tempReportHeader); echo "</pre>";
		if (isset($tempReportHeader['start_date']) && isset($tempReportHeader['end_date']) && !empty($tempReportHeader['start_date']) && !empty($tempReportHeader['end_date'])){
			$tempReportHeader['scheduler_date_range'] = 'custom_radio';
		}else if(isset($tempReportHeader['frequency_val']) && isset($tempReportHeader['frequency_num']) && !empty($tempReportHeader['frequency_val']) && !empty($tempReportHeader['frequency_num'])){
			$tempReportHeader['scheduler_date_range'] = 'last_radio';
		}else{
			$tempReportHeader['scheduler_date_range'] = 'predefined_radio';
		}		
		
		/*---------------------------------------------*/
		
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Controller to get filters textbox autocomplete value in json format for autocomplete.
	*********************************************/	
	/****************** Demo Code ***************/
	/*
	function filtersAutoComplete(){
		//echo $json_metrics = json_encode($this->adopsreport_model->getMetrics());		
		echo $availableTags = json_encode(array("ActionScript","AppleScript","Asp","BASIC","C","C++","Clojure","COBOL","ColdFusion","Erlang","Fortran","Groovy","Haskell","Java","JavaScript","Lisp","Perl","PHP","Python","Ruby","Scala","Scheme"));
		exit;
	}	
	*/	
	
	
}