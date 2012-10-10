<?php 
/******************************************************
*	FileName 	 : dashboard.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : report history view file.       
******************************************************/


error_reporting(E_ALL ^ E_NOTICE);
?>
	<link rel="stylesheet" href="<?php echo $this->config->base_url(); ?>public/css/ui.jqgrid.css" />
	<script src="<?php echo $this->config->base_url(); ?>public/js/grid_locale-en.js"></script>
	<script src="<?php echo $this->config->base_url(); ?>public/js/jqgrid.min.js"></script>
	<script language="javascript" type="text/javascript"> 
	
	</script>	

	<div id="report-history-main">
		<h1 class="page-title">History</h1>
			<div id="middle-page-container">
				<table id="list"><tr><td></td></tr></table>
				<div id="pager"></div>
			</div>
	</div>
				