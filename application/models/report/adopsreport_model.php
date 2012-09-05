<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adopsreport_model extends CI_Model {

	public function __construct()
	{
		//$this->load->database();
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
	
	public function getDimensions()
	{		
			$conn = $this->__adeConnection();
			$sql = 	"SELECT DISPLAY_NAME,ID
					FROM gad_report_lookup
					WHERE field_type            = 'D'
					AND active                  = 1
					AND report_parent_field_id IS NULL
					AND report_type             = 1
					ORDER BY report_type_display_order ASC";
			$st = oci_parse($conn,$sql);
			oci_execute($st);
			$data = array();
			while($row = oci_fetch_assoc($st)){
				$data[] = array('text'=>$row['DISPLAY_NAME'],'id'=>$row['ID'],'hasChildren'=>'false','selectable'=>'false');
			}
		oci_free_statement($st);
		$conn = $this->__adeConnectionClose($conn);
		return $data;
	}
	
	public function getMetrics()
	{
			$conn = $this->__adeConnection();
			$sql = 	"SELECT DISPLAY_NAME,ID
					FROM gad_report_lookup
					WHERE field_type            = 'M'
					AND active                  = 1
					AND report_parent_field_id IS NULL
					AND report_type             = 1
					ORDER BY report_type_display_order ASC";
			$st = oci_parse($conn,$sql);
			oci_execute($st);
			$data = array();
			while($row = oci_fetch_assoc($st)){
				$data[] = array('text'=>$row['DISPLAY_NAME'],'id'=>$row['ID'],'hasChildren'=>'false','selectable'=>'false');
			}
		oci_free_statement($st);
		$conn = $this->__adeConnectionClose($conn);
		return $data;
	}
	
	public function getFilters()
	{
			$conn = $this->__adeConnection();
			$sql = 	"SELECT DISPLAY_NAME,ID
					FROM gad_report_lookup
					WHERE field_type            = 'F'
					AND active                  = 1
					AND report_parent_field_id IS NULL
					AND report_type             = 1
					ORDER BY report_type_display_order ASC";
			$st = oci_parse($conn,$sql);
			oci_execute($st);
			$data = array();
			while($row = oci_fetch_assoc($st)){
				$data[] = array('text'=>$row['DISPLAY_NAME'],'id'=>$row['ID'],'hasChildren'=>'false','selectable'=>'false');
			}
		oci_free_statement($st);
		$conn = $this->__adeConnectionClose($conn);
		return $data;
	}		
}