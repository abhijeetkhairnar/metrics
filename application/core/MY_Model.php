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
			return $conn;
		}else{
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
			return $conn;
		}else{
			return false;
		}
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-28-2012
	| @Description 	close connection
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/	
	public function __adeConnectionClose($conn){
		oci_close($conn);
	}	
		
	/////////////////////////////////////////
	/**
	  * Method To generate lookup array for dimentions , filters and metrics  .
	  * @param <report_type> which identifies report type.
	  * @return  array(columns fetched from DB.),
	*/
	/////////////////////////////////////////	
	public function generateLookUP($report_type)
	{
		$getReportDetailsSql ="SELECT 
									display_name, REPORT_TYPE, table_field_name, field_type
							  FROM 
									GAD_REPORT_LOOKUP
							  WHERE 
									  active          = 1 
									  AND table_field_name IS NOT NULL
									  AND field_type <> 'F'
									  AND report_type = $report_type
							  
							  UNION
							  
							  SELECT display_name,
							  REPORT_TYPE,
								table_field_name,
								field_type
							  FROM GAD_REPORT_LOOKUP
							  WHERE active          = 1
							  AND table_field_name IS NOT NULL
							  AND field_type = 'F'  -- AND REPORT_PARENT_FIELD_ID is not NULL
							  AND report_type = $report_type";


				$oracleConn = $this->__adeConnection();
				$stmt_rpt_details = oci_parse($oracleConn, $getReportDetailsSql);
				oci_execute($stmt_rpt_details);
				$output_arr = array();
				while($row_details = oci_fetch_array($stmt_rpt_details))
				{
					$output_arr[$row_details['REPORT_TYPE']][$row_details['FIELD_TYPE']][$row_details['DISPLAY_NAME']] = $row_details['TABLE_FIELD_NAME'];
				}

				

			 // Fetch data from DB for T types and add it to the Lookup array as hard coded indexes $output_arr['TIMEZONE']['T'].
			  $query ="SELECT 
							DISPLAY as DISPLAY_NAME,
							STR_VALUE_ID as TABLE_FIELD_NAME
					   FROM 
							ADM_OPTION_LOOKUP
					   WHERE 
						ACTIVE = 1 AND UPPER(KEY_NAME)=UPPER('timezone')";

				$oracleConn = $this->__campapignConnection();
				$stmt_rpt_details = oci_parse($oracleConn, $query );
				oci_execute($stmt_rpt_details);
				while($row_details = oci_fetch_array($stmt_rpt_details))
				{				
					$output_arr['TIMEZONE']['T'][$row_details['DISPLAY_NAME']] = $row_details['TABLE_FIELD_NAME'];

				}

					return $output_arr;	
		}
	////////////////////////////////////////	
}
?>