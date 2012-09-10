<?php

class Campaign_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
	}
		
	public function getAutoCompleteResultSet($table_name, $term)
	{		
			$conn = parent::__adeConnection();
			
			switch($table_name){
			
			case 'gad_advertiser':
			$sql = 	"Select 
							GAD_ADVERTISER_ID,  NAME 
					From $table_name 
					Where upper(NAME) like ('".strtoupper($term)."%') order by NAME asc";
					break;
					
			case 'gad_order':
			$sql = 	"Select 
							GAD_ORDER_ID,  NAME 
					From $table_name 
					Where upper(NAME) like ('".strtoupper($term)."%') order by NAME asc";
					break;
					
			case 'gad_ad':
			$sql = 	"Select 
							GAD_AD_ID,  NAME 
					From $table_name 
					Where upper(NAME) like ('".strtoupper($term)."%') order by NAME asc";
					break;
					
			case 'gad_creative':
			$sql = 	"Select 
							GAD_CREATIVE_ID,  NAME 
					From $table_name 
					Where upper(NAME) like ('".strtoupper($term)."%') order by NAME asc";
					break;
			
			case 'gad_ad_size':
			$sql = 	"Select 
							GAD_AD_SIZE_ID,  SIZE_NAME 
					From $table_name 
					Where upper(NAME) like ('".strtoupper($term)."%') order by NAME asc";
					break;			
			}		
			$st = oci_parse($conn,$sql);
			oci_execute($st);
			$data = array();
			while($row = oci_fetch_array($st)){
				$data[] = array('id'=>$row[0],'label'=>$row[1]);
			}
		oci_free_statement($st);
		$conn = parent::__adeConnectionClose($conn);
		return $data;
	}
	
	
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
			$data[] = array('id'=>$row['SRNO'],'select'=> '<input type="radio" class="radio_btn" id = '.$row['SRNO'].' value = '.$row['SRNO'].' name="radio_btn">', 'advertiser_name'=>$row['ADVERTISER_NAME'] ,'ad_name'=>$row['AD_NAME'],'ad_id'=>$row['AD_ID'],'ad_size'=>$row['AD_SIZE'], 'start_date'=>$row['START_DATE'], 'end_date'=>$row['END_DATE']);
		}
		oci_free_statement($st);
		$conn = parent::__adeConnectionClose($conn);
		return $data;
	}

}

?>