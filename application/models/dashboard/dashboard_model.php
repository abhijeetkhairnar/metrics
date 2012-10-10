<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/******************************************************
*	FileName 	 : adopsreport_model.php
*	Created By 	 : Amin Shah.
*	Created Date : 26 Sep 2012.
*	Description	 : Dashborad model file.
*	Version 	 : 1.0                  
******************************************************/

class Dashboard_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function getReportList($userId){
	
		$conn 		= parent::__adqConnection();				
		$curs = oci_new_cursor($conn);
		
		if(!$curs){
			log_info("Problem in allocating a new cursor.");
		}	

		$sql = "BEGIN PKG_ADQ_DISPLAY.prc_disp_user_reports(:pn_user_id, :pc_rc_map); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn, $sql);		
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}	
		
		$userIdBind = oci_bind_by_name($stmt, ":pn_user_id", $userId);		
		if(!$reportTypeBind){
			log_info("Problem in oci_bind_by_name() for :pn_user_id.");
		}
	
		$cursBind = oci_bind_by_name($stmt, ":pc_rc_map", $curs, -1, OCI_B_CURSOR);
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc_map to ".$curs.".");
		}		
			
		$exeStmt = oci_execute($stmt, OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt." . oci_error($stmt));
		}
		$exeCurs = oci_execute($curs, OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs." . oci_error($stmt));
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('report_id'			=> $row['REPORT_ID'],
							'report_type'		=> $row['REPORT_TYPE'],
							'report_name'		=> $row['REPORT_NAME']);
		}		
		$freeStmt = oci_free_statement($stmt);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for stmt." . oci_error($stmt));
		}		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs." . oci_error($stmt));
		}			
		parent::__connectionClose($conn);
		return $data;
	}
}