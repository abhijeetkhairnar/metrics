<?php
/******************************************************
*	FileName 	 : campaign_model.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 30 Aug 2012.
*	Description	 : campaign model file.
*	Version 	 : 1.0                  
******************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
`
class Campaign_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	/*********************************************
	*	Created By 	 : Amin S / Abhijeet K.
	*	Created Date : 30 Aug 2012. 
	*	Description	 : Get auto complete result set.
	*********************************************/			
	public function getAutoCompleteResultSet($table_name, $term)
	{		
			$conn = parent::__adeConnection();

			$conn = oci_connect('adq2anywhere','adq2anywhere','GADEVDB');
			
			$sqlFetchData = "BEGIN PKG_ADQ_DISPLAY.PRC_DISP_SPLASHMETADATA (:pv_label_name, :pv_filter, :pn_start_row,:pn_end_row, :pc_rc); END;";
			
			switch($table_name){
			case 'gad_advertiser':
				$input = 'advertiser_name';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;
					
			case 'gad_order':
				$input = 'order_name';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;
					
			case 'gad_ad':
				$input = 'ad_name';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;
					
			case 'gad_creative':
				$input = 'creative_name';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;
			
			case 'gad_ad_size':
				$input = 'ad_size';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;		
			
			case 'creative':
				$input = 'creative_type';
				$filter  = $term;
				$start_row  = null;
				$end_row  = null;
			break;		
			}		

			$stmt_query = oci_parse($conn, $sqlFetchData);
			$oci_flag = oci_bind_by_name($stmt_query,":pv_label_name",$input);
			$oci_flag = oci_bind_by_name($stmt_query,":pv_filter",$filter);
			$oci_flag = oci_bind_by_name($stmt_query,":pn_start_row",$start_row);
			$oci_flag = oci_bind_by_name($stmt_query,":pn_end_row",$end_row);
			$returnResultLabel = oci_new_cursor($conn);
			$oci_flag = oci_bind_by_name($stmt_query,":pc_rc",$returnResultLabel,-1,OCI_B_CURSOR);
			$oci_flag = oci_execute($stmt_query,OCI_DEFAULT);
			$oci_flag = oci_execute($returnResultLabel,OCI_DEFAULT);
			$data = array();
			while($rowFetchData = oci_fetch_array($returnResultLabel))
			{
				$data[] = array('id'=>$rowFetchData[0],'label'=>$rowFetchData[1]);
			}

		
				
		oci_free_statement($returnResultLabel);
		//$conn = parent::__adeConnectionClose($oConn_proc);
		return $data;
	}
	
	/*********************************************
	*	Created By 	 : Amin S / Abhijeet K.
	*	Created Date : 30 Aug 2012. 
	*	Description	 : Get Ad Ids.
	*********************************************/		
	function getAdIds(){
		$conn = $this->__adeConnection();
		$sql = "SELECT ROWNUM AS srno,
				  A.*
				FROM
				  ( SELECT DISTINCT GAD_ADVERTISER.NAME AS advertiser_name,
					GAD_AD.NAME                         AS ad_name,
					GAD_AD.GAD_AD_ID					AS ad_id,
					GAD_AD.GAD_AD_SIZE_ID				AS ad_size,
					TO_CHAR(GAD_AD.START_DATE,'mm/dd/yyyy HH24:MI')START_DATE,
					TO_CHAR(GAD_AD.END_DATE,'mm/dd/yyyy HH24:MI')END_DATE
				  FROM GAD_CREATIVE
				  INNER JOIN GAD_ADVERTISER
				  ON GAD_ADVERTISER.GAD_ADVERTISER_ID = GAD_CREATIVE.GAD_ADVERTISER_ID
				  LEFT OUTER JOIN GAD_AD_CREATIVE
				  ON GAD_AD_CREATIVE.GAD_CREATIVE_ID = GAD_CREATIVE.GAD_CREATIVE_ID
				  LEFT OUTER JOIN GAD_AD
				  ON GAD_AD.GAD_AD_ID = GAD_AD_CREATIVE.GAD_AD_ID
				  LEFT OUTER JOIN GAD_ORDER
				  ON GAD_ORDER.GAD_ORDER_ID = GAD_AD.GAD_ORDER_ID
				  LEFT OUTER JOIN GAD_OPTION
				  ON UPPER(GAD_OPTION.KEY_NAME)  = 'CREATIVE TYPE'
				  AND TO_CHAR(GAD_CREATIVE.TYPE) = GAD_OPTION.STR_VALUE_ID
				  LEFT OUTER JOIN GAD_AD_SIZE
				  ON GAD_AD_SIZE.GAD_AD_SIZE_ID = GAD_CREATIVE.CREATIVE_SIZE_ID
				  LEFT OUTER JOIN GAC_DATA.GAC_ADPRODUCT
				  ON GAC_DATA.GAC_ADPRODUCT.ID        = GAD_CREATIVE.EXT_GAC_ADPRODUCT
				  WHERE GAD_AD_CREATIVE.ACTIVE         <> 9
				  AND GAD_AD_CREATIVE.ACTIVE         <> -1
				  AND GAD_AD_CREATIVE.ACTIVE         <> 0
				  AND GAD_AD.GAD_AD_ID               IS NOT NULL
				  AND rownum                          < 500
				  ) A";
		
		$st = oci_parse($conn,$sql);
		oci_execute($st);
		$data = array();
		while($row = oci_fetch_assoc($st)){
			$data[] = array('id'=>$row['SRNO'],
							'select'=> '<input type="radio" class="radio_btn" id = '.$row['SRNO'].' value = '.$row['SRNO'].' name="radio_btn">', 
							'advertiser_name'=>$row['ADVERTISER_NAME'], 
							'ad_name'=>$row['AD_NAME'], 
							'ad_id'=>$row['AD_ID'], 
							'ad_size'=>$row['AD_SIZE'], 
							'start_date'=>$row['START_DATE'], 
							'end_date'=>$row['END_DATE']);
		}
		oci_free_statement($st);
		$conn = parent::__adeConnectionClose($conn);
		return $data;
	}

}

?>