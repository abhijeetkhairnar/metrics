<?php

	class Standard extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	function __adeConnection(){
		$conn = oci_connect('ade_data', 'ade_data' , 'ADQ');
		if ($conn){
			echo "connected"; return
		}else{
			echo "not connected"; return;
		}
	}
}

?>