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

error_reporting(E_ERROR | E_WARNING | E_PARSE);	// Report simple running errors

class My_Controller extends CI_Controller {

    function __construct(){
		$data = array();
        parent::__construct();        
    }
	
	function __destructor(){
		$data = array();
        parent::__destructor();		
    }
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-24-2-2012
	| @Description 	set session data
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
	
	function __setSession($session_data){
		if (is_array($session_data)){
			$this->session->set_userdata($session_data);
			return true;
		}else{
			return false;
		}
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-24-2-2012
	| @Description 	get session data
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
	
	function __getSession($keyName){
		 return $this->session->userdata($keyName);		
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-24-2-2012
	| @Description 	unset session data
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
	
	function __unsetSession($keyName){
		$this->session->unset_userdata($keyName);
	}
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Amin S
	| @UpdatedON	Aug-24-2-2012
	| @Description 	As per the privileges header layout display
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
	
	function __layout($position){
		$data = array();
		if ($position == 'header'){
			//$this->load->view($position, $data);
			$this->load->view('templates/header', $data);
		}
		if ($position == 'footer'){
			//$this->load->view($position, $data);
			$this->load->view('templates/footer', $data);
		}
	}
	
	
	/** --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- 
	| @author		Akshay S
	| @UpdatedON	Aug-31-2012
	| @Description 	
	 --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- **/
	
	function __autocomplete($model_called, $model_name, $table_name, $term){
				
		$this->load->model('report/campaign_model');
	
	return  '["ActionScript","AppleScript","Asp","BASIC","C","C++","Clojure","COBOL","ColdFusion","Erlang","Fortran","Groovy","Haskell","Java","JavaScript","Lisp","Perl","PHP","Python","Ruby","Scala","Scheme"]';
	
	$this->campaign_model->getAutoCompleteResultSet();
	}
}

?>