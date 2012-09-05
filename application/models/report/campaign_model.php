<?php

class Campaign_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	public function __adeConnection(){
		$conn = oci_connect('ade_data', 'ade_data', 'ADQ');
		if ($conn){
			return $conn;
		}else{
			return false;
		}
	}

	public function __adeConnectionClose($conn){
		oci_close($conn);
	}	
	
	
	public function getAutoCompleteResultSet($table_name, $term)
	{		
			$conn = $this->__adeConnection();
			
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
		$conn = $this->__adeConnectionClose($conn);
		return $data;
	}

}

?>