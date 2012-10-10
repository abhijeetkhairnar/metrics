<?php
/******************************************************
*	FileName 	 : header.php
*	Created By 	 : Akshay Sardar.
*	Created Date     : 25 AUG 2012
*	Description	 : header view file included for all pages.               
******************************************************/ 
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title ?> - metrics.glam.com</title>
	<script type="text/javascript" src="<?php echo base_url();?>public/js/jquery-1.8.0.min.js"> </script>
	<script type="text/javascript" src="<?php echo base_url();?>public/js/jquery-ui-1.8.23.custom.min.js"> </script>
	<script type="text/javascript" src="<?php echo base_url();?>public/js/common.js"> </script>
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo base_url();?>public/css/style.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>public/css/menu.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/css/smoothness/jquery-ui-1.8.23.custom.css" />
</head>
<body>
<?php 
    $objCI =& get_instance();
    $objCI->load->library('session');
   ?>    
<!---------------- MAIN - START ---------------->
	<div id="main-container">
		<!---------------- HEADER - START ---------------->
		<div id="header">
			<div id="inner-header">
				<div id="header-top-left">
						<h1><a href="" id="logo">METRICS <span>GLAM TECHNOLOGIES</span></a></h1>					
				</div>
				<div id="header-top-right">                                    
                                    <?php if ($_SESSION['user_name'] != ''){?>                                    
					<a href="" class="bold-cls">Tools</a> 
                                            <span class="separator">|</span> 
                                            <span class="header-user-name"><?php echo $_SESSION['user_name'];?></span> 
                                        <a href="<?php echo base_url();?>index.php/login/login/logout" class="bold-cls">logout</a>
                                    <?php } ?>    
				</div>
			</div>	
		</div>
		<!---------------- HEADER - END ---------------->
      
		<!---------------- MIDDLE - START ---------------->
		<div id="middle">	
			<!---------------- MIDDLE-BODY - START ---------------->			
			<div id="middle-body">
				<!---------------- MENU - START ---------------->
		        <?php // if ($_SESSION['user_name'] != ''){?> 
				<div id="menu-div">																						
					<ul id="menu">    
						<li class="main-menu-1"><a href="<?php echo base_url();?>index.php/dashboard/dashboard">Dashboard</a></li>
						<!-- Begin Reports Item -->
						<li class="main-menu-2"><a href="javascript:;">Reports</a>
							<!-- Begin Reports container -->
							<div class="dropdown_5columns">
								<h3>Sales Reports.</h3>
									<ul class="listUL">								
										<li><a href="<?php echo base_url();?>index.php/report/adopsreport/campaign">Splash</a></li>
										<li><a href="<?php echo base_url();?>index.php/report/adopsreport/standard">Standard</a></li>
										<li><a href="#">Intra-day URL Report</a></li>
										<li><a href="#">Filtered Clicks Report</a></li>
										<li><a href="#">URL Report</a></li>
										<li><a href="#">Combined Log</a></li>
										<li><a href="#">instant reports</a></li>
										<li><a href="#">IPushdown Report*</a></li>
										<li><a href="#">Pushdown Takeover Report*</a></li>								
									</ul>
									<a href="#" class="simple-link">All Reports >></a>
							</div>
							<!-- End Reports container -->
						</li>
						<!-- End Reports Item -->
						<li class="main-menu-1"><a href="<?php echo base_url();?>index.php/history/reporthistory">History</a></li>
					</ul>
				</div>
		        <?php // } ?> 
				<!---------------- MENU - END ---------------->
                
				<!---------------- MIDDLE-CONTAINER - START ---------------->			
				<div id="middle-container">
				