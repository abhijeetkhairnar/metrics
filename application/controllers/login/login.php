<?php 
/******************************************************
*	FileName 	 : Login.php
*	Created By 	 : Amin S.
*	Created Date     : 03 Oct 2012.
*	Description	 : Login Controller file.
*	Version 	 : 1.0                  
******************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

        function __construct(){
            
            parent::__construct();                                      
                    $this->load->helper('url');
                    $this->load->model('login/login_model', 'login_model');  
                    
         }
	
	/*********************************************
	*	Created By 	 : Amin Shah.
	*	Created Date     : 03 Oct 2012.
	*	Description	 : Default Login controller.
	*********************************************/
	function index()
	{ 
            $tempSessionArr = $this->session->userdata; 
            //echo "<pre>"; print_r($this->session); echo "</pre>";exit;
            $this->session->sess_destroy();
            if ($this->session->userdata('user_id') != ''){
                redirect('/report/adopsreport/standard');
            }
                $data   =   array();
		$data['title'] 		= 'Login';	
		$data['message']	=  '';	
		/** Code execute after Form submission  **/
		if ($_POST['frm_submit']){
			$emailID	=	trim($_POST['email']);
			$password	=	$_POST['password'];
			
			if (empty($emailID) || empty($password)){
				$message =	"<span class='msg_error'>Please enter valid Email Id & Password.</span>";
				$data['message'] = $message;
			}else{
				$this->load->helper('authentication');
				$userInfo	=  explode('@glam.com', $emailID);
				
				$ldap_server	=	$this->config->item('ldap_server');
				$app_domain_name=	$this->config->item('app_domain_name');				
				
				$userName	 =	$userInfo[0];				
				$isValidUser     =      ldapAuthentication($ldap_server, $app_domain_name, $userName, $password);	// Helper function
				                                
				if ($isValidUser  ==  1){
                                        log_info('LDAP authentication completed..');	
                                        
                                        /* Pull perticular user information */                                        
                                        $userDataList = $this->login_model->getUserData($emailID);
                                        
                                        if (count($userDataList) > 0){
                                           //  $matrics = json_encode($userDataList['matrics']);
                                          //   unset($userDataList['matrics']);
                                           //  $userDataList['matrics'] = "'" .$matrics. "'";                                                                                    
                                             
                                        }else{
                                              $preferenceTableData = $this->login_model->getPrefrenceTableData();  
                                              if (array_key_exists('master_node', $preferenceTableData)){
                                                  $userDataList = $this->login_model->setUserData($userName, $emailID);
                                              }
                                        } 
                                        //$this->session->set_userdata('Amin', array("a"=>"amin"));
                                       //
                                       // echo "<pre>"; print_r($userDataList); echo "</pre>";exit;
                                            
                                         foreach($userDataList as $key => $val){
                                               
                                                if (is_array($val)){                                                   

                                                   // for($i = 0; $i < count($val); $i++){
                                                  //$this->session->set_userdata('mertics', array("0" =>array("userid"=>"amin","email"=>"email@glam.com"),"1" =>array("userid"=>"akii","email"=>"emai2@glam.com")));
                                                   
                                                   $this->session->set_userdata('mertics',$val);

                                                }else{
                                                     $this->session->set_userdata($key, $val);
                                                }  
                                                
                                         }
                                         
                                         //echo "<pre>"; print_r($this->session); echo "</pre>";exit;
                                         
                                        /*          End of block           */                                       
                                        log_info('After succesfully login redirect to standard report');
                                       redirect('/report/adopsreport/standard');
				}else{
					$message =	"<span class='msg_error'>Invalid Email Id or Passwords.</span>";
					$data['message'] = $message;
				}
			}
		}
                
		/** End Of Form submission  **/		
		$this->load->template('login/login' , $data);						
	}
        
        
        
        function logout(){
            unset($_SESSION['user_name']);
            redirect('login/login/');
        }
}