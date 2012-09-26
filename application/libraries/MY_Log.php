<?php  
/******************************************************
*	FileName 	 : MY_Log.php
*	Created By 	 : Amin S.
*	Created Date : 30 Aug 2012.
*	Description	 : Log library file.             
******************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'third_party/ci_log4php/Logger.php';
class MY_Log extends CI_Log {
	
	protected $logger;
	protected $initialized = false;
	
	public function __construct() {
		parent::__construct();
		
		if ($this->initialized === false) {
			$this->initialized = true;
			$config_file = APPPATH . 'config/log4php.properties';
			if ( defined('ENVIRONMENT') && file_exists( APPPATH . 'config/' . ENVIRONMENT . '/log4php.properties' ) ) {
				$config_file = APPPATH . 'config/' . ENVIRONMENT . '/log4php.properties';
			}
			Logger::configure($config_file);
			$this->logger = Logger::getRootLogger();
		}
	}
	
	public function write_log($level = 'error', $msg, $php_error = FALSE) {
		
		if ($this->_enabled === FALSE) {
			return FALSE;
		}
		
		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold) ) {
			return FALSE;
		}
		
		$epoc 	= 	time();
		$txtnid =	$epoc ."-". $_SERVER["HTTP_HOST"] ."-".rand (1, 1000);	
		
		switch ($level) {
			case 'ERROR':				
				$msg = '"host":"'.$_SERVER["HTTP_HOST"].'", "date":"'.$epoc.'", "app_name":"adq", "log_type":"error", "txnid":"'.$txtnid.'", "msg":"'.stripslashes($msg).'"';		
				$this->logger->error($msg);
				break;
			case 'INFO':
				$msg = '"host":"'.$_SERVER["HTTP_HOST"].'", "date":"'.$epoc.'", "app_name":"adq", "log_type":"info", "txnid":"'.$txtnid.'", "msg":"'.stripslashes($msg).'"';
				$this->logger->info($msg);
				break;
			case 'DEBUG':
				$msg = '"host":"'.$_SERVER["HTTP_HOST"].'", "date":"'.$epoc.'", "app_name":"adq", "log_type":"debug", "txnid":"'.$txtnid.'", "msg":"'.stripslashes($msg).'"';
				$this->logger->debug($msg);
				break;
			default:
				$msg = '"host":"'.$_SERVER["HTTP_HOST"].'", "date":"'.$epoc.'", "app_name":"adq", "log_type":"warning", "txnid":"'.$txtnid.'", "msg":"'.stripslashes($msg).'"';
				$this->logger->debug($msg);
				break;
		}
		
		return TRUE;
	}
}
