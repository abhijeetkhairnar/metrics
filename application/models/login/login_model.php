<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/******************************************************
*	FileName 	 : adopsreport_model.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : Report model file.
*	Version 	 : 1.0                  
******************************************************/

class Login_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
	}

        /*********************************************
	*	Created By 	 : Amin S.
	*	Created Date     : 08 Oct 2012. 
	*	Description	 : Get User Info.
	*********************************************/	
	public function getUserData($emailID = ''){
                $data  = array();
                $conn = parent::__adqConnection();		
		$curs = oci_new_cursor($conn);
		if(!$curs){
			log_info("Problem in allocating a new cursor.");
		}		
		
                if(!empty($emailID)){	
                    /*********************************************
                   
                    *********************************************/					
                    $sql = "BEGIN PKG_ADQ_META.PRC_GET_USER_AUTH_DATA(:pv_email , :p_usr_cur); END;";
                    log_info("Sql - ".$sql." ");
                    $stmt = oci_parse($conn,$sql);
                    if(!$stmt){
                            log_info("Problem in sql stmt parsing.");
                    }
                    $emailBind = oci_bind_by_name($stmt,":pv_email",$emailID);
                    if(!$emailBind){
                            log_info("Problem in oci_bind_by_name() for :pv_email to ".$emailID.".");
                    }
                    
                    $cursBind = oci_bind_by_name($stmt,":p_usr_cur",$curs,-1,OCI_B_CURSOR);
                    if(!$cursBind){
                            log_info("Problem in oci_bind_by_name() for :p_usr_cur to ".$curs.".");
                    }		
                    $exeStmt = oci_execute($stmt,OCI_DEFAULT);
                    if(!$exeStmt){
                            log_info("Problem in oci_execute() for stmt.");
                    }
                    $exeCurs = oci_execute($curs,OCI_DEFAULT);
                    if(!$exeCurs){
                            log_info("Problem in oci_execute() for curs.");
                    }
                    $i = 0;
                    while($row = oci_fetch_array($curs)){
                        ++$i;
                        if ($i == 17){
                         //break;
                        }
                        if(!array_key_exists('user_id', $data)){
                            $data['user_id']    =   $row['USER_ID'];
                        }
                        
                        if(!array_key_exists('user_name', $data)){
                            $data['user_name']    =   $row['USER_NAME'];
                        }
                        
                        if(!array_key_exists('emailId', $data)){
                            $data['emailId']    =   $emailID;
                        }
                        
                            $data['matrics'][] = array(
                                            'role_id'        => $row['ROLE_ID'],
                                          //  'role_name'      => $row['ROLE_NAME'],
                                            'report_type_id' => $row['REPORT_TYPE_ID'],
                                          //  'report_type'    => $row['REPORT_TYPE']
                                            
                                           );
                            
                    }		
                    $freeStmt = oci_free_statement($stmt);
                    if(!$freeStmt){
                            log_info("Problem in oci_free_statement() for stmt.");
                    }		
                    $freeCurs = oci_free_statement($curs);
                    if(!$freeCurs){
                            log_info("Problem in oci_free_statement() for curs.");
                    }			
                    parent::__connectionClose($conn);
                    return $data;
                    }
        }
        
         /*********************************************
	*	Created By 	 : Amin S.
	*	Created Date     : 09 Oct 2012. 
	*	Description	 : Set User Info.
	*********************************************/	
	public function setUserData($userName='', $emailID = ''){
                
                $conn   = parent::__adqConnection();	
                $status =   '';
            /*********************************************

            *********************************************/
            
            $sql = "BEGIN PKG_ADQ_META.PRC_SAVE_USER(:pv_user_name, :pv_email, :pv_sf_message); END;";
            log_info("Sql - ".$sql." ");
            $stmt = oci_parse($conn,$sql);
            if(!$stmt){
                    log_info("Problem in sql stmt parsing.");
            }
            $userNameBind = oci_bind_by_name($stmt,":pv_user_name",$userName);
            if(!$userNameBind){
                    log_info("Problem in oci_bind_by_name() for :pv_user_name to ".$userName.".");
            }
            
            $emailBind = oci_bind_by_name($stmt,":pv_email",$emailID);
            if(!$emailBind){
                    log_info("Problem in oci_bind_by_name() for :pv_email to ".$emailID.".");
            }
            
            $statusBind = oci_bind_by_name($stmt,":pv_sf_message", $status, 500);
            if(!$statusBind){
                    log_info("Problem in oci_bind_by_name() for :pv_sf_message.");
            }
            	
            $exeStmt = oci_execute($stmt,OCI_DEFAULT);
            if(!$exeStmt){
                    log_info("Problem in oci_execute() for stmt.");
            }
            parent::__connectionClose($conn);
            
            return $status;

        }
       
        /*********************************************
	*	Created By 	 : Amin S.
	*	Created Date     : 09 Oct 2012. 
	*	Description	 : Get Prefrence Table Data.
	*********************************************/	
	public function getPrefrenceTableData(){
            $data   = array();    
            $conn   = parent::__adqConnection();             
            $curs = oci_new_cursor($conn);
            if(!$curs){
                    log_info("Problem in allocating a new cursor.");
            }
            /*********************************************

            *********************************************/					
            $sql = "BEGIN PKG_NODE_DATA.PRC_GET_PREF_DATA(:p_cur); END;";
            log_info("Sql - ".$sql." ");
            $stmt = oci_parse($conn,$sql);
            if(!$stmt){
                    log_info("Problem in sql stmt parsing.");
            }
           
            $cursBind = oci_bind_by_name($stmt,":p_cur",$curs,-1,OCI_B_CURSOR);
            if(!$cursBind){
                    log_info("Problem in oci_bind_by_name() for :p_cur to ".$curs.".");
            }		
            $exeStmt = oci_execute($stmt,OCI_DEFAULT);
            if(!$exeStmt){
                    log_info("Problem in oci_execute() for stmt.");
            }
            $exeCurs = oci_execute($curs,OCI_DEFAULT);
            if(!$exeCurs){
                    log_info("Problem in oci_execute() for curs.");
            }
            while($row = oci_fetch_array($curs)){
                $data[] = $row;
                if ($row['KEY_NAME'] == 'Master Node' && $row['KEY_VALUE'] == 'Yes'){
                    $data['master_node'] = true;
                }

            }		
            $freeStmt = oci_free_statement($stmt);
            if(!$freeStmt){
                    log_info("Problem in oci_free_statement() for stmt.");
            }		
            $freeCurs = oci_free_statement($curs);
            if(!$freeCurs){
                    log_info("Problem in oci_free_statement() for curs.");
            }			
            parent::__connectionClose($conn);
            
            return $data;
       }
}