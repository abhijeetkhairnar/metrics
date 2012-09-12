<?php 
/******************************************************
*	FileName 	 : adopsreport_model.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report model file.
*	Version 	 : 1.0                  
******************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adopsreport_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get dimensions model.
	*********************************************/		
	public function getDimensions()
	{	 /*
			$conn = parent::__adeConnection();			
			if(!$_POST['pid']){		
			}else if($_POST['pid']){
			}
			$conn =  parent::__adeConnectionClose($conn);
		*/
		$conn = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');		
		if(!$_POST['pid']){		
			$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_DIMS(:pn_report_type , :pc_rc); END;";
			$stmt = oci_parse($conn,$sql);
			$report_type = "1";
			oci_bind_by_name($stmt,":pn_report_type",$report_type);
			$result = oci_new_cursor($conn);
			oci_bind_by_name($stmt,":pc_rc",$result,-1,OCI_B_CURSOR);
			oci_execute($stmt,OCI_DEFAULT);
			oci_execute($result,OCI_DEFAULT);
			while($row = oci_fetch_array($result)){
				$data[] = array('text'		=> $row['LABEL'],
								'id'		=> $row['ID'],
								'children'	=> true);
			}
		}else if($_POST['pid']){
			$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_CDIMS(:pn_report_type , :pn_prnt_id , :pc_rc); END;";
			$stmt = oci_parse($conn,$sql);
			$report_type = "1";
			$prnt_id = $_POST['pid'];
			oci_bind_by_name($stmt,":pn_report_type",$report_type);
			oci_bind_by_name($stmt,":pn_prnt_id",$prnt_id);
			$result = oci_new_cursor($conn);
			oci_bind_by_name($stmt,":pc_rc",$result,-1,OCI_B_CURSOR);
			oci_execute($stmt,OCI_DEFAULT);
			oci_execute($result,OCI_DEFAULT);
			while($row = oci_fetch_array($result)){
				$data[] = array('text'		=> $row['LABEL'],
								'id'		=> $row['ID'],
								'children'	=> false);
			}		
		}
		oci_close($conn);
		return $data;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get metrics model.
	*********************************************/	
	public function getMetrics()
	{
		$conn = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');
		$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_METRICS(:pn_report_type , :pc_rc); END;";
		$stmt = oci_parse($conn,$sql);
		$report_type = "1";
		oci_bind_by_name($stmt,":pn_report_type",$report_type);
		$result = oci_new_cursor($conn);
		oci_bind_by_name($stmt,":pc_rc",$result,-1,OCI_B_CURSOR);
		oci_execute($stmt,OCI_DEFAULT);
		oci_execute($result,OCI_DEFAULT);
		while($row = oci_fetch_array($result)){
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID'],
							'children'	=> false);
		}
		oci_close($conn);
		return $data;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get filters model.
	*********************************************/		
	public function getFilters()
	{
		$conn = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');
		if(!$_POST['pid']){		
			$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_FILTERS(:pn_report_type , :pc_rc); END;";
			$stmt = oci_parse($conn,$sql);
			$report_type = "1";
			oci_bind_by_name($stmt,":pn_report_type",$report_type);
			$result = oci_new_cursor($conn);
			oci_bind_by_name($stmt,":pc_rc",$result,-1,OCI_B_CURSOR);
			oci_execute($stmt,OCI_DEFAULT);
			oci_execute($result,OCI_DEFAULT);
			while($row = oci_fetch_array($result)){
				$data[] = array('text'		=> $row['LABEL'],
								'id'		=> $row['ID'],
								'children'	=> true);
			}
		}else if($_POST['pid']){
		
		}
		oci_close($conn);
		return $data;
	}		
}