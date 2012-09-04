<?php

class Campaign_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function getAutoCompleteResultSet(){	
		return  '["ActionScript","AppleScript","Asp","BASIC","C","C++","Clojure","COBOL","ColdFusion","Erlang","Fortran","Groovy","Haskell","Java","JavaScript","Lisp","Perl","PHP","Python","Ruby","Scala","Scheme"]';
		
	}
}

?>