<?php
	/******************************************************
	*	FileName 	 : authentication helper.php
	*	Created By 	 : Amin S.
	*	Created Date : 03 Oct 2012.
	*	Description	 : Helper file for user authentication check.
	*	Version 	 : 1.0                  
	******************************************************/
	
	function ldapAuthentication($pServer, $pDomain, $pUser, $pPassword) 
	{
	  // using ldap bind
	  $ldaprdn = $pDomain . '\\' . $pUser; 
	  $ldappass = $pPassword;  // associated password                       
	  
	  // connect to ldap server
	   
	  
	  $ldapconn = ldap_connect($pServer);
	  //exit;
	  if ($ldapconn === false) 
	  {
		log_info('ldap server connection failed...');
	  }
	
	  //set timeout
	  //ldap_set_option($ldapconn, LDAP_OPT_NETWORK_TIMEOUT, 10);
	    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
	
	  //if primary ldap connection failed then trying to connect to alternate ldap  server.
	  if (!$ldapbind) 
	  {	 
		log_info('connection failed...');
	  }
	    
	  // verify binding
	  if ($ldapbind) { 
	       ldap_close($ldapconn);       
			return TRUE;
	  } 
	  ldap_close($ldapconn);
	  return false;
	}

?>