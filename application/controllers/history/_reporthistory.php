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
		// $this->load->model('report/adopsreport_model', 'adopsreport_model');		
    }
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar.
	*	Created Date : 25 Aug 2012.
	*	Description	 : Default report history controller.
	*********************************************/	
	function index()
	{
		$data['title'] = 'Report history';
		
		/**************************************************************************************/
		/**************************************************************************************/		
		$this->load->library('reportutilsclass');
		$api_report_history = $this->reportutilsclass->ReportThroughAPI(" userId = 104 ORDER BY TIME_STAMP DESC",'getHistory');
		$tempCharacter  = '';
		
		foreach ($api_report_history as $keysReport=>$valsReport)
		{
			$tempCharacter .= "[";
			$tempCharacter .= "'".$valsReport->generatedOn."',";
			//$tempCharacter .= "'".str_replace("'","",$valsReport->reportName)."',";
			$tempCharacter .= "'".htmlentities($valsReport->reportName, ENT_QUOTES)."',";
			$path =	"http://10.0.8.246/metrics.glam.com/index.php/history/reporthistory/";
			$valsReport->showTemplate = 0;
			
			if($valsReport->status=='COMPLETE')
			{
				$valsReport->status = 'READY';
			}

			$tempCharacter .="'".$valsReport->status."',";

			if($valsReport->recordCount>0 && $valsReport->recordCount<=3000)
			{
				$tempCharacter_viewonscreen = '"View on Screen"';
				$tempCharacter_csv = 'csv';
				$tempCharacter_email = base64_encode($valsReport->fileName)."||".base64_encode($valsReport->reportName).'||email';
			}
			elseif($valsReport->recordCount>3000)
			{
				$tempCharacter_viewonscreen = '"Not Available"';
				$tempCharacter_csv = 'csv';
				$tempCharacter_email = base64_encode($valsReport->fileName)."||".base64_encode($valsReport->reportName).'||email';
			}
			else
			{
				$tempCharacter_viewonscreen = '"This report returned no available data"';
				$tempCharacter_csv = '';
				$tempCharacter_email = '';
			}
			

			if($valsReport->status =='IN QUEUE' || $valsReport->status =='RUNNING' || $valsReport->status =='FAILED')
			{
				$tempCharacter_viewonscreen = '';
				$tempCharacter_csv = '';
				$tempCharacter_email = '';
			}

			
			
			$tempCharacter .= $tempCharacter_viewonscreen .",'".$path."||".base64_encode($valsReport->reportId)."||".base64_encode($valsReport->reportName)."||". $tempCharacter_csv."','".$tempCharacter_email."',";
			$tempCharacter .= "'".base64_encode($valsReport->reportId)."|Edit Report"."',";
			$tempCharacter .= $valsReport->reportId.",";
			$tempCharacter .= $valsReport->reportId.",";
			$tempCharacter .= $valsReport->sequenceNumber.",";
			$tempCharacter .= "'".$valsReport->format."',";
			$tempCharacter .= $valsReport->recordCount.",";
			$tempCharacter .= "'REPORT FAILED',";
			$tempCharacter .= "'".$valsReport->fileName."',";
			$tempCharacter .= "'".$valsReport->generatedOn."',";
			$tempCharacter .= "'".$valsReport->data."',";
			$tempCharacter .= "'".base64_encode($valsReport->fileName)."',";
			$tempCharacter .= $valsReport->recordCount.",";
			$tempCharacter .= $valsReport->reportType.",";
			$tempCharacter .= $valsReport->showTemplate;
			$tempCharacter .= "],";
		}
		
		$sNoresultmessage = 'You have no Data in report History.';
			
		$ouput = "[".trim($tempCharacter, ",")."]";
		$data['grid_data'] = $ouput;
		$data['base_path'] = $path;
		/**************************************************************************************/	
		/**************************************************************************************/
		
		
		
		$this->load->template('history/reporthistory' , $data);						
	}

}
