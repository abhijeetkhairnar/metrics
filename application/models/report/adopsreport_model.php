<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/******************************************************
*	FileName 	 : adopsreport_model.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report model file.
*	Version 	 : 1.0                  
******************************************************/

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
	{	
		$conn = parent::__adqConnection();		
		$reportType = "1";
		$curs = oci_new_cursor($conn);
		if($curs){
			log_info("Problem in allocating a new cursor.");
		}		
			if(!$_POST['pid']){	
				/*********************************************
				*	Fetch the parent dimensions using following proc.
				*	PKG_ADQ_DISPLAY.PRC_DISPLAY_DIMS(:pn_report_type , :pc_rc).
				*	:pn_report_type = IN parameter	- report type id.
				*	:pc_rc			= OUT parameter	- return value for (ID, LABEL, CHILDEXIST). 
				*********************************************/					
				$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_DIMS(:pn_report_type , :pc_rc); END;";
				log_info("Sql - ".$sql." ");
				$stmt = oci_parse($conn,$sql);
				if(!$stmt){
					log_info("Problem in sql stmt parsing.");
				}
				$reportTypeBind = oci_bind_by_name($stmt,":pn_report_type",$reportType);
				if(!$reportTypeBind){
					log_info("Problem in oci_bind_by_name() for :pn_report_type to ".$reportType.".");
				}
	
			}else if($_POST['pid']){
				$parentId = $_POST['pid'];
				/*********************************************
				*	Fetch the child dimensions using following proc.
				*	PKG_ADQ_DISPLAY.PRC_DISPLAY_CDIMS(:pn_report_type , :pn_prnt_id , :pc_rc);.
				*	:pn_report_type = IN parameter	- report type id.
				*	:pn_prnt_id		= IN parameter	- parent id
				*	:pc_rc			= OUT parameter	- return value for (ID, LABEL). 
				*********************************************/					
				$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_CDIMS(:pn_report_type , :pn_prnt_id , :pc_rc); END;";
				log_info("Sql - ".$sql." ");
				$stmt = oci_parse($conn,$sql);
				if(!$stmt){
					log_info("Problem in sql stmt parsing.");
				}		
				$reportTypeBind = oci_bind_by_name($stmt,":pn_report_type",$reportType);
				if(!$reportTypeBind){
					log_info("Problem in oci_bind_by_name() for :pn_report_type to ".$reportType.".");
				}			
				$parentIdBind = oci_bind_by_name($stmt,":pn_prnt_id",$parentId);		
				if(!$parentIdBind){
					log_info("Problem in oci_bind_by_name() for :pn_prnt_id to ".$parentId.".");
				}	
			}		
		$cursBind = oci_bind_by_name($stmt,":pc_rc",$curs,-1,OCI_B_CURSOR);
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc to ".$curs.".");
		}		
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt.");
		}
		$exeCurs = oci_execute($curs,OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs.");
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID'],
							'children'	=> strtoupper($row['CHILDEXIST']) == "TRUE" ? true : false);
		}		
		$freeStmt = oci_free_statement($stmt);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for stmt.");
		}		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs.");
		}			
		parent::__connectionClose($conn);
		return $data;
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get metrics model.
	*********************************************/	
	public function getMetrics()
	{	
		$conn = parent::__adqConnection();		
		$reportType = "1";
		$curs = oci_new_cursor($conn);
		if($curs){
			log_info("Problem in allocating a new cursor.");
		}

		/*********************************************
		*	Fetch the metrics using following proc.
		*	PKG_ADQ_DISPLAY.PRC_DISPLAY_METRICS(:pn_report_type , :pc_rc)
		*	:pn_report_type = IN parameter	- report type id.
		*	:pc_rc			= OUT parameter	- return value for (ID, LABEL). 
		*********************************************/		
		$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_METRICS(:pn_report_type , :pc_rc); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn,$sql);
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}
		$reportTypeBind = oci_bind_by_name($stmt,":pn_report_type",$reportType);
		if(!$reportTypeBind){
			log_info("Problem in oci_bind_by_name() for :pn_report_type to ".$reportType.".");
		}		
		$cursBind = oci_bind_by_name($stmt,":pc_rc",$curs,-1,OCI_B_CURSOR);
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc to ".$curs.".");
		}		
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt.");
		}
		$exeCurs = oci_execute($curs,OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs.");
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID'],
							'children'	=> false);
		}		
		$freeStmt = oci_free_statement($stmt);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for stmt.");
		}		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs.");
		}			
		parent::__connectionClose($conn);
		return $data;		
	}
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get filters model.
	*********************************************/		
	public function getFilters()
	{
		$conn = parent::__adqConnection();		
		$reportType = "1";
		$curs = oci_new_cursor($conn);
		if($curs){
			log_info("Problem in allocating a new cursor.");
		}		
			if(!$_POST['pid']){		
				/*********************************************
				*	Fetch the parent filters using following proc.
				*	PKG_ADQ_DISPLAY.PRC_DISPLAY_FILTERS(:pn_report_type , :pc_rc)
				*	:pn_report_type = IN parameter	- report type id.
				*	:pc_rc			= OUT parameter	- return value for (ID, LABEL, CHILDEXIST). 
				*********************************************/				
				$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_FILTERS(:pn_report_type , :pc_rc); END;";
				log_info("Sql - ".$sql." ");
				$stmt = oci_parse($conn,$sql);
				if(!$stmt){
					log_info("Problem in sql stmt parsing.");
				}
				$reportTypeBind = oci_bind_by_name($stmt,":pn_report_type",$reportType);
				if(!$reportTypeBind){
					log_info("Problem in oci_bind_by_name() for :pn_report_type to ".$reportType.".");
				}
	
			}else if($_POST['pid']){
				$parentId = $_POST['pid'];
				/*********************************************
				*	Fetch the child filters using following proc.
				*	PKG_ADQ_DISPLAY.PRC_DISPLAY_CDIMS(:pn_report_type , :pn_prnt_id , :pc_rc);.
				*	:pn_report_type = IN parameter	- report type id.
				*	:pn_prnt_id		= IN parameter	- parent id
				*	:pc_rc			= OUT parameter	- return value for (ID, LABEL). 
				*********************************************/					
				$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_DISPLAY_CFILTERS(:pn_report_type , :pn_prnt_id , :pc_rc); END;";
				log_info("Sql - ".$sql." ");
				$stmt = oci_parse($conn,$sql);
				if(!$stmt){
					log_info("Problem in sql stmt parsing.");
				}		
				$reportTypeBind = oci_bind_by_name($stmt,":pn_report_type",$reportType);
				if(!$reportTypeBind){
					log_info("Problem in oci_bind_by_name() for :pn_report_type to ".$reportType.".");
				}			
				$parentIdBind = oci_bind_by_name($stmt,":pn_prnt_id",$parentId);		
				if(!$parentIdBind){
					log_info("Problem in oci_bind_by_name() for :pn_prnt_id to ".$parentId.".");
				}
			}		
		$cursBind = oci_bind_by_name($stmt,":pc_rc",$curs,-1,OCI_B_CURSOR);
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc to ".$curs.".");
		}		
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt.");
		}
		$exeCurs = oci_execute($curs,OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs.");
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID'],
							'children'	=> strtoupper($row['CHILDEXIST']) == "TRUE" ? true : false);
		}		
		$freeStmt = oci_free_statement($stmt);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for stmt.");
		}		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs.");
		}			
		parent::__connectionClose($conn);
		return $data;
	}	
	
	/*********************************************
	*	Created By 	 : Aksahy Sardar / Amin.
	*	Created Date : 25 Aug 2012. 
	*	Description	 : Get filter input model.
	*********************************************/		
	public function getFilterInput($id = '')
	{		
		if ($id == ''){
			$id = $_REQUEST['id'];
		}
		$conn = parent::__adqConnection();
		$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_FILTER_FIELD_TYPE(:PN_ID, :PV_FILTER_FIELD_TYPE); END;";
		log_info("Sql - ".$sql);
		$stmt = oci_parse($conn, $sql);
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}		
		$filterIdBind = oci_bind_by_name($stmt,":PN_ID", $id);
		if(!$filterIdBind){
			log_info("Problem in oci_bind_by_name() for :PN_ID to ".$id.".");
		}			
		$filterFieldTypeBind = oci_bind_by_name($stmt,":PV_FILTER_FIELD_TYPE",$type, 100);		
		if(!$filterFieldTypeBind){
			log_info("Problem in oci_bind_by_name() for :PV_FILTER_FIELD_TYPE to.");
		}	
		
		$exec = oci_execute($stmt);
		
		if (!$exec){
			log_info("Problem to execute PKG_ADQ_DISPLAY.PRC_FILTER_FIELD_TYPE.");
		}
		
		return $type;
	}	
	
/*	
	public function getFilterDataOld()
	{			
		$conn = parent::__adqConnection();
		$filterID = $_REQUEST['id'];
		$sql = "select id, user_name as label from gad_user";
		log_info("Sql - ".$sql);
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt);
		while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) {
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID']);
			
		}		
		return $data;
		/*if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}		
		$filterIdBind = oci_bind_by_name($stmt,":PN_ID", $_REQUEST['id']);
		if(!$filterIdBind){
			log_info("Problem in oci_bind_by_name() for :PN_ID to ".$_REQUEST['id'].".");
		}			
		$filterFieldTypeBind = oci_bind_by_name($stmt,":PV_FILTER_FIELD_TYPE",$type, 100);		
		if(!$filterFieldTypeBind){
			log_info("Problem in oci_bind_by_name() for :PV_FILTER_FIELD_TYPE to.");
		}		
		
		$exec = oci_execute($stmt);
		
		if (!$exec){
			log_info("Problem to execute PKG_ADQ_DISPLAY.PRC_FILTER_FIELD_TYPE.");
		}
		
		
	}*/	
	
	/*****************************************************
	*	Created By 	 : Amin.
	*	Created Date : 20 Sep 2012. 
	*	Description	 : Pulling meta data for selected fitler
	*****************************************************/		
	public function getFilterData()
	{			
		$conn 		= parent::__adqConnection();
		$offSet		=	1;
		$numOfRecs	=	20;
		
		$curs = oci_new_cursor($conn);
		if(!$curs){
			log_info("Problem in allocating a new cursor.");
		}	

		$sql = "BEGIN PKG_ADQ_META.prc_get_filter_data(:pn_rep_lookup_id,  :pn_first_rec, :pn_num_rec, :p_fil_data_cur); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn,$sql);		
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}		
		
		$filterIdBind = oci_bind_by_name($stmt,":pn_rep_lookup_id", $_REQUEST['id']);
		if(!$filterIdBind){
			log_info("Problem in oci_bind_by_name() for :pn_rep_lookup_id to ".$_REQUEST['id'].".");
		}			
		
		$filterOffSetBind = oci_bind_by_name($stmt,":pn_first_rec",$offSet);		
		if(!$filterOffSetBind){
			log_info("Problem in oci_bind_by_name() for :pn_first_rec.");
		}	
		
		$filterNumOfRecsBind = oci_bind_by_name($stmt,":pn_num_rec",$numOfRecs);		
		if(!$filterNumOfRecsBind){
			log_info("Problem in oci_bind_by_name() for :pn_num_rec.");
		}	
		
		$cursBind = oci_bind_by_name($stmt,":p_fil_data_cur",$curs,-1,OCI_B_CURSOR);
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :p_fil_data_cur to ".$curs.".");
		}		
			
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt." . oci_error($stmt));
		}
		$exeCurs = oci_execute($curs,OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs." . oci_error($stmt));
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('text'		=> $row['NAME'],
							'id'		=> $row['ID'],
							'children'	=> false);
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
	
	
	/*****************************************************
	*	Created By 	 : Amin.
	*	Created Date : 25 Sep 2012. 
	*	Description	 : Pulling selected meta data for fitler
	*****************************************************/		
	public function getSelectedFilterData($id, $selectedFilterId)
	{			
		$conn 		= parent::__adqConnection();		
				
		$curs = oci_new_cursor($conn);
		if(!$curs){
			log_info("Problem in allocating a new cursor.");
		}	
		
		$sql = "BEGIN PKG_ADQ_META.prc_get_rpt_det_data(:pn_rep_lookup_id, :pv_col_val, :p_fil_data_cur); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn,$sql);		
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}		
		
		$filterIdBind = oci_bind_by_name($stmt,":pn_rep_lookup_id", $id);
		if(!$filterIdBind){
			echo "ine".__line__;
			log_info("Problem in oci_bind_by_name() for :pn_rep_lookup_id to ".$id.".");
		}			
		
		$selectedFilterIdBind = oci_bind_by_name($stmt, ":pv_col_val", $selectedFilterId);
		if(!$selectedFilterIdBind){
			echo "ine".__line__;
			log_info("Problem in oci_bind_by_name() for :pv_col_val.");
		}	
		
		$cursBind = oci_bind_by_name($stmt, ":p_fil_data_cur", $curs,-1, OCI_B_CURSOR);
		if(!$cursBind){
			echo "ine".__line__;
			log_info("Problem in oci_bind_by_name() for :p_fil_data_cur to ".$curs.".");
		}		
			
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt." . oci_error($stmt));
		}
		$exeCurs = oci_execute($curs,OCI_DEFAULT);
		if(!$exeCurs){
			log_info("Problem in oci_execute() for curs." . oci_error($stmt));
		}
		while($row = oci_fetch_array($curs)){
			$data[] = array('text'		=> $row['NAME'],
							'id'		=> $row['ID'],
							'children'	=> false);
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
	/*****************************************************
	*	Created By 	 : Amin.
	*	Created Date : 21 Sep 2012. 
	*	Description	 : Pulling meta data for selected fitler
	*****************************************************/		
	public function getLabelnIdMappingForDD($listOfIds){
	
	//	echo "<pre>"; print_r($listOfIds) ;
		$conn 		= parent::__adqConnection();				
		$curs = oci_new_cursor($conn);
		$reportType	=	1;
		if(!$curs){
			log_info("Problem in allocating a new cursor.");
		}	

		$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_LKP_IDLABEL_MAP(:pv_report_type, :pn_id, :pc_rc_map); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn, $sql);		
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}	
		
		$reportTypeBind = oci_bind_by_name($stmt, ":pv_report_type", $reportType);		
		if(!$reportTypeBind){
			log_info("Problem in oci_bind_by_name() for :pv_report_type.");
		}
	
		
		$idBind = oci_bind_array_by_name($stmt, ":pn_id", $listOfIds, count($listOfIds), -1, SQLT_CHR);
		if(!$idBind){
			log_info("Problem in oci_bind_by_name() for :pn_id.");
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
			$data[] = array('text'		=> $row['LABEL'],
							'id'		=> $row['ID'],
							'parentid'	=> ($row['RPF'] == null) ? 0 : $row['RPF']);
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