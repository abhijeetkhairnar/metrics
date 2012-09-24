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
		$conn = self::__adq2anywhere();		
			
		$gr_report_data_key		=	$reportInfo['reportDataKey']; 
		$gr_report_data_data	=	$reportInfo['reportDataVal'];
		$dimension				=	$reportInfo['dimension'];
		$metrics				=	$reportInfo['metrics'];
		$filtersDataKey			=	$reportInfo['filtersDataKey'];
		$filtersDataVal			=	$reportInfo['filtersDataVal'];
	
	//	$filtersDataKey			=	array(462, 225);
	//	$filtersDataVal			=	array('New york', 'ABC');
		$status					=	'';
		
		$sql	=	"BEGIN PKG_ADQ_DML.PRC_REPORT_SAVE(:pa_gr_col_name, :pa_gr_values, :pa_grd_dim_id, :pa_grd_metric_id, :pa_grd_fk, :pa_grd_fv, :pv_sf_message); END;";
		
		log_info("Sql - ".$sql." ");
		$statement = oci_parse($conn,$sql);
		if(!$statement){
			log_info("Problem in sql statement parsing.");
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
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fk", $filtersDataKey, count($filtersDataKey), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fk");
		}
		
		$bindStatus = oci_bind_array_by_name($statement, ":pa_grd_fv", $filtersDataVal, count($filtersDataVal), -1, SQLT_CHR);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pa_grd_fv");
		}
		
		$bindStatus = oci_bind_by_name($statement, ":pv_sf_message", $status, 200);
		if (!$bindStatus){
			log_info("Problem in oci_bind_by_name() for :pv_sf_message");
		}
		
		oci_execute($statement);
		
		return $status;
	}	
}
?> 