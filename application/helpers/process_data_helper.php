<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| @Author: Amin S.
| @Description : Create report_data, dimension, metrics and fitlers array
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
function dataProcess_helper($form_data = array()){

	if (! empty($form_data)){
				
		$dimensions	 = array();
		$metrics	 = array();
		$filters	 = array();

		if (count($form_data['dimensions']) > 0){			
			foreach($form_data['dimensions'] as $key => $val){
				if (is_array($val)){
					foreach($val as $keyInner => $valInner){
						array_push($dimensions, $valInner);
					}
				}else{
					array_push($dimensions, $val);
				}	
			}
		}
		unset($form_data['dimensions']);
		
		
		if (count($form_data['metrics']) > 0){			
			foreach($form_data['metrics'] as $key => $val){
				array_push($metrics, $val);
			}
		}	
		unset($form_data['metrics']);
		
		if (count($form_data['filters']) > 0){			
			foreach($form_data['filters'] as $key => $val){
				if (is_array($val)){
					foreach($val as $keyInner => $valInner){
						array_push($filters, $valInner);
					}
				}else{
					array_push($filters, $val);
				}	
			}
		}
		unset($form_data['filters']);

		if (is_array($filters)){
			foreach($filters as $key => $val){
				if (isset($form_data[$val])){
					$filterData[$val] = implode("," , $form_data[$val]); 
					unset($form_data[$val]);									
				}	
			}
		}

		
		
		/* -- For time being fitler call set blank -- */
		$filtersDataKey = array();
		$filtersDataVal = array();	

		$filtersDataKey = array_keys($filterData);
		$filtersDataVal = array_values($filterData);	
		
		$form_data = __setDateRange($form_data);
		unset($form_data['date_range']);
				
		foreach($form_data as $key => $val){
			$reportData[$key] = $val;
		}
		
		
		unset($reportData['metrics']);
		unset($reportData['filters']);
		
		$reportDataKey = array_keys($reportData);
		$reportDataVal = array_values($reportData);
			
		$final_data = array("reportDataKey"	 => $reportDataKey, 
							"reportDataVal"		 => $reportDataVal, 
							"dimension" 		=> $dimensions, 
							"metrics" 			=> $metrics, 
							"filtersDataKey" 	=> $filtersDataKey,
							"filtersDataVal"	=> $filtersDataVal
							);
		return $final_data;
		
	}else{
		log_info('Post form data is empty');
	}
	
}

/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| @Author: Amin S.
| @Description : Set date range selected by user from date range block...
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/

function __setDateRange($form_data = array()){
	$dateRangeType = $form_data['date_range'];
	switch($dateRangeType){	
		case 'predefined_radio_checked':
											$predefined_value = strtolower($form_data['predefined']);
											switch($predefined_value){
												case 'yesterday':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
																$end_date_range 	= "";
																$date_range_desc	= "Yesterday";	
																break;
												case 'week to date':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(NEXT_DAY(SYSDATE - 7, 'Sunday')),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),''YYYYMMDD''))";
																$date_range_desc	= "Week to date";
																break;
												case 'past 7 days':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 7),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
																$date_range_desc	= "Past 7 days";
																break;
												case 'last week':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(NEXT_DAY(SYSDATE - 14, 'Sunday'),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(NEXT_DAY(SYSDATE - 14, 'Sunday') + 6,''YYYYMMDD''))";
																$date_range_desc	= "Last week";
																break;
												case 'month to date':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE, 'MM'),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),''YYYYMMDD''))";
																$date_range_desc	= "Month to date";
																break;
												case 'past 30 days':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 30),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
																$date_range_desc	= "Past 30 days";
																break;
												case 'last month':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 30),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
																$date_range_desc	= "Last month";
																break;
																			
												case 'year to date':
																$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE, 'YY'),''YYYYMMDD''))";
																$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE),''YYYYMMDD''))";
																$date_range_desc	= "Year to date";
																break;
											}											
											__unsetFormData($form_data, 'predefined');
											break;
		case 'last_radio_check':	
									if ( strtolower($form_data['frequency_type']) == 'days'){
										$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 7),''YYYYMMDD''))";
										$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
										$date_range_desc	= "Days";										
									}else if (strtolower($form_data['frequency_type']) == 'weeks'){
										$start_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 7 * 7),''YYYYMMDD''))";
										$end_date_range 	= "TO_NUMBER(TO_CHAR(TRUNC(SYSDATE - 1),''YYYYMMDD''))";
										$date_range_desc	= "Weeks";
									}
									$date_range_num		= $form_data['frequency_val'];
									__unsetFormData($form_data, 'last');
									break;										
		case 'custom_radio_check':
									$start_date_range 	= $_POST['start_date_range'];
									$end_date_range 	= $_POST['end_date_range'];
									$date_range_desc	= '';
									$date_range_num		= '';
									__unsetFormData($form_data, 'custom');
									break;	
									
	}
	
	$form_data['start_date_range']	=	$start_date_range;
	$form_data['end_date_range']	=	$end_date_range;
	$form_data['date_range_desc']	=	$date_range_desc;
	$form_data['date_range_num']	=	$date_range_num;
	
	return $form_data;
}


/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
| @Author: Amin S.
| @Description : Unset unwanted data from post object
--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/

function __unsetFormData(& $form_data = array(), $selectedDateRange){
	
	if ($selectedDateRange == 'predefined'){
		unset($form_data['frequency_val']);
		unset($form_data['frequency_type']);
		unset($form_data['start_date_range']);
		unset($form_data['end_date_range']);
		unset($form_data['predefined']);
	}else if ($selectedDateRange == 'last'){
		unset($form_data['predefined']);
		unset($form_data['start_date_range']);
		unset($form_data['end_date_range']);
	} else if ($selectedDateRange == 'custom'){
		unset($form_data['predefined']);
		unset($form_data['frequency_val']);
		unset($form_data['frequency_type']);		
	}
}
?>