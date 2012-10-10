<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 * Wrapper class 
 *
 * @author		Amin S
 * @UpdatedON	Aug-24-2-2012
 * @Description 
 */
// ------------------------------------------------------------------------

class My_Model extends CI_Model {

    function __construct(){
		$data = array();
        parent::__construct();
    }

	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-28-2012
	| @Description 	ade connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __adeConnection(){	
		$conn = oci_connect('ade_data', 'ade_data', 'ADQ');
		if ($conn){
			log_info("DB connected successfully to ade_data - ADQ.");
			return $conn;
		}else{
			log_info("Problem in DB connecction to ade_data - ADQ.");
			return false;
		}
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Sep-20-2012
	| @Description 	ade connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __adqConnection(){	
		$conn = oci_connect('adq_data', 'adq_data', 'ADQ');
		if ($conn){
			log_info("DB connected successfully to adq_data - ADQ.");
			return $conn;
		}else{
			log_info("Problem in DB connecction to adq_data - ADQ.");
			return false;
		}
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-28-2012
	| @Description 	campapign connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __campapignConnection(){
		$conn = oci_connect('campaign', 'campaign', 'ADQ');
		if ($conn){
			log_info("DB connected successfully to campaign - ADQ.");
			return $conn;
		}else{
			log_info("Problem in DB connecction to campaign - ADQ.");
			return false;
		}
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-28-2012
	| @Description 	close connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __connectionClose($conn){
		$connClose = oci_close($conn);
		if($connClose){
			log_info("DB connection closed successfully.");
		}else{
			log_info("Problem in DB close connection.");
		}
	}	
	
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Sep-13-2012
	| @Description 	campapign connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __adq2anywhere(){			
		$conn = oci_connect('adq2anywhere', 'adq2anywhere', 'GADEVDB');
		if ($conn){
			log_info("DB connected successfully to adq2anywhere - GADEVDB.");
			return $conn;
		}else{
			log_info("Problem in DB connecction to adq2anywhere - GADEVDB.");
			return false;
		}
	}	
	
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 15 Sep 2012. 
	*	Description	 : Save report.
	*********************************************/		
	public function saveReport(& $reportInfo = array()){
	
		$conn = self::__adqConnection();		
		$reportType				=   1;
		$gr_report_data_key		=	$reportInfo['reportDataKey']; 
		$gr_report_data_data	=	$reportInfo['reportDataVal'];
		$dimension				=	$reportInfo['dimension'];
		$metrics				=	$reportInfo['metrics'];
				
		$tempArr	=	array_flip($gr_report_data_key);		
		if (in_array('report_id', $gr_report_data_key)){
			$report_id_index = $tempArr['report_id'];
			$report_id = $gr_report_data_data[$report_id_index]; 
			
			unset($gr_report_data_key[$report_id_index]);
			unset($gr_report_data_data[$report_id_index]);
		}
		
		if (is_array($reportInfo['filtersDataKey']) && count($reportInfo['filtersDataKey']) > 0){
			$filtersDataKey		=	$reportInfo['filtersDataKey'];
			$filtersDataKeyCnt	= 	count($reportInfo['filtersDataKey']);
		}else{
			$filtersDataKey		= 	array(-1);
			$filtersDataKeyCnt	= 	1;
		}
		
		if (is_array($reportInfo['filtersDataVal']) && count($reportInfo['filtersDataVal']) > 0){
			$filtersDataVal		=	$reportInfo['filtersDataVal'];
			$filtersDataValCnt	=	count($reportInfo['filtersDataVal']);
		}else{
			$filtersDataVal		=	array(-1);
			$filtersDataValCnt	=	1;
		}	

	//	$filtersDataKey			=	array(462, 225);
	//	$filtersDataVal			=	array('New york', 'ABC');
		$status;
		
					
		$sql	=	"BEGIN PKG_ADQ_DML.PRC_REPORT_SAVE(:pn_report_type, :pa_gr_col_name, :pa_gr_values, :pa_grd_dim_id, :pa_grd_metric_id, :pa_grd_fk, :pa_grd_fv, :pv_sf_message, :pc_validation_msg); END;";
		
		log_info("Sql - ".$sql." ");
		$statement = oci_parse($conn,$sql);
		
		if(!$statement){
			log_info("Problem in sql statement parsing.");
		}	
		
		$bingReportType = oci_bind_by_name($statement,":pn_report_type", $reportType);
		if (!$bingReportType){
			log_info("Problem in oci_bind_by_name() for :pn_report_type");
		}
	
		$bindStatus = oci_bind_array_by_name($statement, ":pa_gr_col_name", $gr_report_data_key, count($gr_report_data_key), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_gr_col_name");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_gr_values", $gr_report_data_data, count($gr_report_data_data), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_gr_values");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_dim_id", $dimension, count($dimension), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_dim_id");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_metric_id", $metrics, count($metrics), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_metric_id");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fk", $filtersDataKey, $filtersDataKeyCnt, -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fk");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fv", $filtersDataVal, $filtersDataValCnt, -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fv");
		}
		
		$bindStatus = oci_bind_by_name($statement, ":pv_sf_message", $status, 200);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pv_sf_message");
		}

		echo "status--".$status;
		
		$curs = oci_new_cursor($conn);
		if(!$curs)
		{
			log_info("Problem in allocating a new cursor.");
		}

		
		$cursBind = oci_bind_by_name($statement, ":pc_validation_msg", $curs, -1, OCI_B_CURSOR);
				
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_validation_msg to ".$curs.".");
		}

		$exeStmt = oci_execute($statement, OCI_DEFAULT);
		if(!$exeStmt)
		{
			log_info("Problem in oci_execute() for statement." . oci_error($statement));
		}
		
		
		$exeCurs = oci_execute($curs);
		if(!$exeCurs)
		{
			
			print_r(oci_error($curs));
			log_info("Problem in oci_execute() for curs." . oci_error($curs));
		}
		
		
		while($row = oci_fetch_array($curs, OCI_DEFAULT)){
			$data[] = $row['ERROR_MESSAGES'];
		}		
	
		$freeStmt = oci_free_statement($statement);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for statement." . oci_error($statement));
		}	
		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs." . oci_error($statement));
		}		
		
		self::__connectionClose($conn);
			
		$result	=	array("status" => $status, "validation_message" => $data);			
		return $result;		
	}	
	
	
		
	/*********************************************
	*	Created By 	 : Amin.
	*	Created Date : 03 Oct 2012. 
	*	Description	 : Update report.
	*********************************************/		
	public function updateReport(& $reportInfo = array()){
			
		$conn = self::__adqConnection();		
		$reportType				=   1;
		$gr_report_data_key		=	$reportInfo['reportDataKey']; 
		$gr_report_data_data	=	$reportInfo['reportDataVal'];
		$dimension				=	$reportInfo['dimension'];
		$metrics				=	$reportInfo['metrics'];
		
		$tempArr	=	array_flip($gr_report_data_key);		
		if (in_array('report_id', $gr_report_data_key)){
			$report_id_index = $tempArr['report_id'];
			$report_id = $gr_report_data_data[$report_id_index]; 
			
			unset($gr_report_data_key[$report_id_index]);
			unset($gr_report_data_data[$report_id_index]);
		}
			
		
		if (is_array($reportInfo['filtersDataKey']) && count($reportInfo['filtersDataKey']) > 0){
			$filtersDataKey		=	$reportInfo['filtersDataKey'];
			$filtersDataKeyCnt	= 	count($reportInfo['filtersDataKey']);
		}else{
			$filtersDataKey		= 	array(-1);
			$filtersDataKeyCnt	= 	1;
		}
		
		if (is_array($reportInfo['filtersDataVal']) && count($reportInfo['filtersDataVal']) > 0){
			$filtersDataVal		=	$reportInfo['filtersDataVal'];
			$filtersDataValCnt	=	count($reportInfo['filtersDataVal']);
		}else{
			$filtersDataVal		=	array(-1);
			$filtersDataValCnt	=	1;
		}	

	//	$filtersDataKey			=	array(462, 225);
	//	$filtersDataVal			=	array('New york', 'ABC');
		$status;
		
					
		$sql	=	"BEGIN PKG_ADQ_DML.PRC_REPORT_UPDATE(:pn_report_id, :pn_report_type, :pa_gr_col_name, :pa_gr_values, :pa_grd_dim_id, :pa_grd_metric_id, :pa_grd_fk, :pa_grd_fv, :pv_sf_message, :pc_validation_msg); END;";
		
		log_info("Sql - ".$sql." ");
		$statement = oci_parse($conn,$sql);
		
		if(!$statement){
			log_info("Problem in sql statement parsing.");
		}	
		
		$bindReportId = oci_bind_by_name($statement,":pn_report_id", $report_id);
		if (!$bindReportId){
			log_info("Problem in oci_bind_by_name() for :pn_report_id");
		}
		
		$bingReportType = oci_bind_by_name($statement,":pn_report_type", $reportType);
		if (!$bingReportType){
			log_info("Problem in oci_bind_by_name() for :pn_report_type");
		}
	
		$bindStatus = oci_bind_array_by_name($statement, ":pa_gr_col_name", $gr_report_data_key, count($gr_report_data_key), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_gr_col_name");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_gr_values", $gr_report_data_data, count($gr_report_data_data), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_gr_values");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_dim_id", $dimension, count($dimension), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_dim_id");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_metric_id", $metrics, count($metrics), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_metric_id");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fk", $filtersDataKey, $filtersDataKeyCnt, -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fk");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fv", $filtersDataVal, $filtersDataValCnt, -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fv");
		}
		
		$bindStatus = oci_bind_by_name($statement, ":pv_sf_message", $status, 200);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pv_sf_message");
		}

		$curs = oci_new_cursor($conn);
		if(!$curs)
		{
			log_info("Problem in allocating a new cursor.");
		}

		
		$cursBind = oci_bind_by_name($statement, ":pc_validation_msg", $curs, -1, OCI_B_CURSOR);
				
		if(!$cursBind){
			log_info("Problem in oci_bind_by_name() for :pc_validation_msg to ".$curs.".");
		}

		$exeStmt = oci_execute($statement, OCI_DEFAULT);
		if(!$exeStmt)
		{
			log_info("Problem in oci_execute() for statement." . oci_error($statement));
		}
		
		
		$exeCurs = oci_execute($curs);
		if(!$exeCurs)
		{
			
			print_r(oci_error($curs));
			log_info("Problem in oci_execute() for curs." . oci_error($curs));
		}
		
		
		while($row = oci_fetch_array($curs, OCI_DEFAULT)){
			$data[] = $row;
		}		
	
		$freeStmt = oci_free_statement($statement);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for statement." . oci_error($statement));
		}	
		
		$freeCurs = oci_free_statement($curs);
		if(!$freeCurs){
			log_info("Problem in oci_free_statement() for curs." . oci_error($statement));
		}		
		
		self::__connectionClose($conn);
			
		$result	=	array("status" => $status, "validation_message" => $data);		
		return $result;		
	}	
	
	/*********************************************
	*	Created By 	 : Akshay S.
	*	Created Date : 15 Sep 2012. 
	*	Description	 : Edit report.
	*********************************************/		
	public function editReport($id){
		$conn = self::__adqConnection();	
		
		
		$reportId 	= $id;
		$cursGr 	= oci_new_cursor($conn);
		if($cursGr){
			log_info("Problem in allocating a new cursor for Gad report.");
		}		
		$cursDim	= oci_new_cursor($conn);
		if($cursDim ){
			log_info("Problem in allocating a new cursor for dimensions.");
		}		
		$cursMtrx	= oci_new_cursor($conn);
		if($cursMtrx){
			log_info("Problem in allocating a new cursor for metrics.");
		}		
		$cursFil 	= oci_new_cursor($conn);
		if($cursFil){
			log_info("Problem in allocating a new cursor for filters.");
		}		

		/*********************************************
		*	Fetch the save report using following proc.
		*	PKG_ADQ_DISPLAY.PROCEDURE PRC_REPORT_EDIT (:pn_report_id , :pc_rc_gr , :pc_rc_grd_dim , :pc_rc_grd_mtrx , :pc_rc_grd_fil , :pv_sf_msg);
		*	:pn_report_id 	= IN parameter	- report id.
		*	:pc_rc_gr 		= OUT parameter	- return gad report values.
		*	:pc_rc_grd_dim	= OUT parameter	- return dimensions values.
		*	:pc_rc_grd_mtrx	= OUT parameter	- return metrics value.
		*	:pc_rc_grd_fil	= OUT parameter	- return filters value.
		*	:pv_sf_msg		= OUT parameter	- return msg.
		
		*********************************************/		
		
		$sql = "BEGIN PKG_ADQ_DISPLAY.PRC_REPORT_EDIT (:pn_report_id , :pc_rc_gr , :pc_rc_grd_dim , :pc_rc_grd_mtrx , :pc_rc_grd_fil , :pv_sf_msg); END;";
		log_info("Sql - ".$sql." ");
		$stmt = oci_parse($conn,$sql);
		if(!$stmt){
			log_info("Problem in sql stmt parsing.");
		}

		$reportIdBind = oci_bind_by_name($stmt,":pn_report_id",$reportId);
		if(!$reportIdBind){
			log_info("Problem in oci_bind_by_name() for :pn_report_id to ".$reportId.".");
		}		
		
		$cursGrBind = oci_bind_by_name($stmt,":pc_rc_gr",$cursGr,-1,OCI_B_CURSOR);
		if(!$cursGrBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc_gr to ".$cursGr.".");
		}
		$cursDimBind = oci_bind_by_name($stmt,":pc_rc_grd_dim",$cursDim,-1,OCI_B_CURSOR);
		if(!$cursDimBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc_grd_dim to ".$cursDim.".");
		}
		$cursMtrxBind = oci_bind_by_name($stmt,":pc_rc_grd_mtrx",$cursMtrx,-1,OCI_B_CURSOR);
		if(!$cursMtrxBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc_grd_mtrx to ".$cursMtrx.".");
		}
		$cursFilBind = oci_bind_by_name($stmt,":pc_rc_grd_fil",$cursFil,-1,OCI_B_CURSOR);
		if(!$cursFilBind){
			log_info("Problem in oci_bind_by_name() for :pc_rc_grd_fil to ".$cursFil.".");
		}
		
		$bindStatus = oci_bind_by_name($stmt, ":pv_sf_msg", $status, 200);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pv_sf_msg");
		}
		
				
		$exeStmt = oci_execute($stmt,OCI_DEFAULT);
		if(!$exeStmt){
			log_info("Problem in oci_execute() for stmt.");
		}
		
		$exeGrCurs = oci_execute($cursGr,OCI_DEFAULT);
		if(!$exeGrCurs){
			log_info("Problem in oci_execute() for gad report cursor.");
		}
		$exeDimCurs = oci_execute($cursDim,OCI_DEFAULT);
		if(!$exeDimCurs){
			log_info("Problem in oci_execute() for gad report dimensions cursor.");
		}
		$exeMtrxCurs = oci_execute($cursMtrx,OCI_DEFAULT);
		if(!$exeMtrxCurs){
			log_info("Problem in oci_execute() for gad report metrics cursor.");
		}
		$exeFilCurs = oci_execute($cursFil,OCI_DEFAULT);
		if(!$exeFilCurs){
			log_info("Problem in oci_execute() for gad report filters cursor.");
		}
		
		$data 		= array();
		$dataGr		= array();
		$dataDim 	= array();
		$dataMtrx	= array();
		$dataFil	= array();
		
		//echo "<pre>";
		//echo "<br>--------------------- Gr -------------------------- <br>";
				
		$data['header'] = oci_fetch_assoc($cursGr);
		
		//echo "<br>--------------------- Dem ------------------------- <br>";
		while($row = oci_fetch_assoc($cursDim)){
			$dataDim[] = $row;
		}
		$data['dimensions'] = $dataDim;
		//echo "<br>--------------------- Mtrx ------------------------ <br>";
		while($row = oci_fetch_assoc($cursMtrx)){
			$dataMtrx[] = $row;
		}
		$data['metrics'] = $dataMtrx;
		//echo "<br>--------------------- Fil ------------------------- <br>";
		while($row = oci_fetch_assoc($cursFil)){
			$dataFil[] = $row;
		}
		$data['filters'] = $dataFil;
		//echo "<br>--------------------- Status ---------------------- <br>";
		$data['status'] = $status;
		//echo "</pre>";
		
		
		$freeStmt = oci_free_statement($stmt);
		if(!$freeStmt){
			log_info("Problem in oci_free_statement() for stmt.");
		}
		$freeCursGr = oci_free_statement($cursGr);
		if(!$freeCursGr){
			log_info("Problem in oci_free_statement() for gad report cursor.");
		}
		$freeCursDim = oci_free_statement($cursDim);
		if(!$freeCursDim){
			log_info("Problem in oci_free_statement() for gad report dimensions cursor.");
		}
		$freeCursMtrx = oci_free_statement($cursMtrx);
		if(!$freeCursMtrx){
			log_info("Problem in oci_free_statement() for gad report metrics cursor.");
		}
		$freeCursFil = oci_free_statement($cursFil);
		if(!$freeCursFil){
			log_info("Problem in oci_free_statement() for gad report filters cursor.");
		}
		
		
		
		
		
		
		
		
		
		
		self::__connectionClose($conn);
		return $data;
		
	}	
}
?> 