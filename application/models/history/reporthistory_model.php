<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/******************************************************
*	FileName 	 : reporthistory_model.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 10 Oct 2012.
*	Description	 : Report History model file.
*	Version 	 : 1.0                  
******************************************************/

class Reporthistory_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get report history model.
	*********************************************/	
	public function getReportHistory()
	{	

			$conn = parent::__adqConnection();		
			$curs = oci_new_cursor($conn);
			if(!$curs){
				log_info("Problem in allocating a new cursor for report history.");
			}
						
			/*********************************************
			*	Fetch the metrics using following proc.
			*	PKG_ADQ_REPORT.PRC_GET_REPORT_HISTORY(:p_rpt_hist_cur , :pv_err_code , :pv_err_msg)
			*	:p_rpt_hist_cur			= OUT parameter	- return value for (ID, LABEL). 
			*	:pv_err_code			= OUT parameter	- return value for (ID, LABEL). 
			*	:pv_err_msg			= OUT parameter	- return value for (ID, LABEL). 
			*********************************************/		
	
			$sql = "BEGIN PKG_ADQ_REPORT.PRC_GET_REPORT_HISTORY(:p_rpt_hist_cur , :pv_err_code , :pv_err_msg); END;";
			log_info("Sql - ".$sql." ");
			$stmt = oci_parse($conn,$sql);
			if(!$stmt){
				log_info("Problem in sql stmt parsing.");
			}

			$cursBind = oci_bind_by_name($stmt,":p_rpt_hist_cur",$curs,-1,OCI_B_CURSOR);
			if(!$cursBind){
				log_info("Problem in oci_bind_by_name() for :p_rpt_hist_cur to ".$curs.".");
			}
			$errCodeBind = oci_bind_by_name($stmt, ":pv_err_code", $errCode, 2000);
			if(!$errCodeBind){
				log_info("Problem in oci_bind_by_name() for :pv_err_msg to ".$errCode.".");
			}
			$errMsgBind = oci_bind_by_name($stmt, ":pv_err_msg", $errMsg, 2000);
			if (!$errMsgBind){
				log_info("Problem in oci_bind_by_name() for :pv_err_msg to ".$errMsg.".");
			}
			
			$exeStmt = oci_execute($stmt,OCI_DEFAULT);
			if(!$exeStmt){
				log_info("Problem in oci_execute() for stmt.");
			}
			
			$exeCurs = oci_execute($curs,OCI_DEFAULT);
			if(!$exeCurs){
				log_info("Problem in oci_execute() for curs.");
			}
			echo "<br> ------------------------------------------- <br>";
			while($row = oci_fetch_array($curs)){
				/*
				$data[] = array('text'		=> $row['LABEL'],
								'id'		=> $row['ID'],
								'children'	=> false);
				*/
				print_r($row);
			}		
			echo "<br> ------------------------------------------- <br>";
			$freeStmt = oci_free_statement($stmt);
			if(!$freeStmt){
				log_info("Problem in oci_free_statement() for stmt.");
			}		
			$freeCurs = oci_free_statement($curs);
			if(!$freeCurs){
				log_info("Problem in oci_free_statement() for curs.");
			}			
			parent::__connectionClose($conn);
			//return $data;		

			
		}

}