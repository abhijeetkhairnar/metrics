<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * /application/core/MY_Model.php
 *
 */
class MY_Model extends CI_Model {
 
	function MY_Model() {
		parent::Model();
	}
	
	function ade_connection(){
		$conn = oci_connect('ade_data', 'ade_data', 'ADQ');
		if (!$conn) {
			$e = oci_error();
			//trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			return 0;
		}else{
			return 1;
		}
	}
	
	function create() {
		//do insert data into database
	}
 
	function read() {
		//do get data into database
	}
 
	function update() {
		//do update data into database
	}
 
	function delete() {
		//do delete data from database
	}
}