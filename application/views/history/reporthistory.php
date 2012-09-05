<?php
/********************************************************************************************************
 * 	reporthistory.php - View file -  Controller - history/reporthistory.php
 *  CREATED BY	: AKSHAY SARDAR
 *  FOR 		: GLAM INDIA
 *  VERSION		: 1.0
 *  CREATED ON 	: 04 Sept 2012
 *  DESCRIPTION : THIS PHP FILE CONTENT THE VIEW FOR THE REPORT HISTORY PAGE
********************************************************************************************************/
error_reporting(E_ALL ^ E_NOTICE);
//if (!isset($reportTypeArr['Product Report'])){
	$reportTypeArr['Product Report'] = "''";
//}
?>
	<script src="<?php echo $this->config->base_url(); ?>public/js/grid_locale-en.js"></script>
	<script src="<?php echo $this->config->base_url(); ?>public/js/jqgrid.min.js"></script>
	<link rel="stylesheet" href="<?php echo $this->config->base_url(); ?>public/css/ui.jqgrid.css" />
	
<script language="javascript" type="text/javascript"> 
		jQuery(function() {
		
			var mydata = <?php echo $grid_data?>;	
            var basePath = '<?php echo $base_path;?>';
            var ajaxCallIds = '';
			var reportStatus;
			var tempMyData = '';
			var commaSeperator= '';
			
            // convert mydata to the standard format 
            var myNewData = new Array();
	           for (var i=0;i<mydata.length; i++) {
		          
            	var dataArr = mydata[i];
            	var pushStr = '';
				
				
				
            	for (var j=0;j<dataArr.length; j++) {

					
					
            		var editArr = dataArr[5].split("||");
            		var dateruntime = dataArr[0].replace(" ", '_') ;
            		//alert(dateruntime);
            		//die();
					var emailLink = "<a href='javascript:void(0)' onClick=javascript:SaveEmail('','"+editArr[0]+"','"+editArr[1]+"','"+dateruntime+"','')>email</a>";
					var tempEditArrParaOne = editArr[0];
					var tempEditArrParaTwo = editArr[1];
					var editArr = dataArr[6].split("|");
					var editLink = "<a class='tablewidget_button' href='javascript:void(0)' onClick=javascript:fnSubFmEditReport('"+editArr[0]+"','"+ dataArr[19]+"')>Edit</a>   | <a class='tablewidget_button' href='javascript:void(0)' onClick=javascript:fnSubFmRunReport('"+dataArr[8]+"')>Run</a>";
					if(dataArr[18]==<?php echo $reportTypeArr['Product Report']?>)
					{
						var editLink = "<a class='tablewidget_button' href='javascript:void(0)' onClick=javascript:fnSubFmRunReport('"+dataArr[8]+"')>Run</a>";
					}						
					var downloadArr = dataArr[4].split("||");
					var path = downloadArr[0];
					var cvsLink = '';
					var xlsLink = '';
					var pdfLink = '';
					var viewOnScreenLink;
					var actualStatus = '';
					var cvsLink = "<a href='"+path+"downloadfile/ID/"+downloadArr[1]+"/rnm/"+downloadArr[2]+"/f/csv'>CSV</a>";
					var xlsLink = "<a href='"+path+"downloadfile/ID/"+downloadArr[1]+"/rnm/"+downloadArr[2]+"/f/xls'>XLS</a>";
					var xmlLink = "<a href='"+path+"downloadfile/ID/"+downloadArr[1]+"/rnm/"+downloadArr[2]+"/f/xml'>XML</a>";
					var downloadLink = cvsLink+'  |  '+xlsLink+'  |  '+xmlLink;
				/*					*/
					{
						var downloadLink = xlsLink;
						var emailLink = "<a href='javascript:void(0)' onClick=javascript:SaveEmail('"+downloadArr[1]+"','"+tempEditArrParaOne+"','"+tempEditArrParaTwo+"','"+dateruntime+"',1)>email</a>";
					}				


					// block will decide wt to display in viewon screen column						
					if (dataArr[3] == undefined)
					{		
						viewOnScreenLink = "";
					}else if (dataArr[3] == "Not Available"){
						viewOnScreenLink = dataArr[3]+"["+dataArr[17]+"]";
					}else if(dataArr[17] == 0){
						viewOnScreenLink = "This report returned no available data";
					}else
					{
						viewOnScreenLink = "<a href='"+basePath+"generatereport/filename/"+dataArr[16]+"'>View on screen["+dataArr[17]+"]</a>";
					}
					
					if(dataArr[19]=='1')
					{
						viewOnScreenLink = (dataArr[17] == "0")?"This report returned no available data":'-';
					}

				//
					{
						viewOnScreenLink = '-';
					}


					// end of block//
					
					actualStatus = dataArr[2];
					// block will decide wt to display in download column						
					if (dataArr[2] == "IN QUEUE" || dataArr[2] == "RUNNING" || dataArr[2] == "FAILED" || dataArr[17] == "0"){		
						downloadLink = "";
						emailLink	 = "";
						if (dataArr[2] != "FAILED" && dataArr[17] != "0"){
							reportStatus = dataArr[2];
						}else{
							reportStatus = dataArr[2];
						}
					}else{
						reportStatus = dataArr[2];
					}
					// end of block//
					
					var gridReportIdLink = "<a href='javascript:void(0)' onClick=javascript:fnReportDetail('"+dataArr[8]+"')>"+dataArr[8]+"</a>";
					
					var gridReportNameLink = "<div style='cursor:pointer;float:right;padding-left:4px;' onClick=javascript:fnReportDetail('"+dataArr[8]+"')>[+]</div>"+dataArr[1];
					
					
					
            		pushStr = '"dateRun":"'+dataArr[0]+'","reportID":"'+gridReportIdLink+'","reportName":"'+gridReportNameLink+'","status":"'+reportStatus+'","viewonscreen":"'+viewOnScreenLink+'","download":"'+downloadLink+'","email":"'+emailLink+'","editReport":"'+editLink           			+'","reportID":"'+dataArr[8]+'","sequenceNumber":"'+dataArr[9]+'","format":"'+dataArr[10]+'","recordCount":"'+dataArr[11]	+'","failedReason":"'+dataArr[12]+'","fileName":"'+dataArr[13]+'","generatedOn":"'+dataArr[14]+'","data":"'+dataArr[15]+'","actualStatus":"'+actualStatus+'"';                                                          	            								                                                                  	            				
                }
            	pushStr = "{" + pushStr + "}";
            	//alert(pushStr);
               //	pushStr = JSON.parse(pushStr);
               //myNewData.push(pushStr);

			   	tempMyData += commaSeperator + pushStr;
            	commaSeperator = ",";
            	
            }  

			tempMyData = "[" + tempMyData + "]";
	        tempMyData = eval(tempMyData);

            
		
		
			var grid = $("#list");
            grid.jqGrid({
                datatype: "local",
                //datatype: "json",
                data: tempMyData,
                colNames:["Date RUN", "Report ID", "Report Name", "Status", "View On Screen", "Download", "e-mail", "Action", "sequenceNumber", "format", "recordCount", "failedReason", "fileName", "generatedOn", "data", "actualStatus"],
                colModel:[
							{name:'dateRun',index:'dateRun', width:'35px', search:true}, 
							{name:'reportID',index:'reportID', width:'30px',align:"center"}, 
							{name:'reportName',index:'reportName', search:true}, 
							{name:'status',index:'status',align:"center", width:'50px', search:true}, 
							{name:'viewonscreen',index:'viewonscreen',align:"center", width:'50px', search:false},								
							{name:'download',index:'download', width:'50px', align:"center", search:false},
							{name:'email',index:'email', width:'30px',align:"center", search:false},
							{name:'editReport',index:'editReport', width:'40px',align:"center", search:false},
							{name:'sequenceNumber',index:'sequenceNumber', width:'80px',align:"right", hidden:true}, 
							{name:'format',index:'format', width:'80px',align:"right", hidden:true}, 
							{name:'recordCount',index:'recordCount', width:'80px',align:"right", hidden:true}, 
							{name:'failedReason',index:'failedReason', width:'80px',align:"right", hidden:true}, 
							{name:'fileName',index:'fileName', width:'80px',align:"right", hidden:true}, 
							{name:'generatedOn',index:'generatedOn', width:'80px',align:"right", hidden:true}, 
							{name:'data',index:'data', width:'80px',align:"right", hidden:true},
							{name:'actualStatus',index:'actualStatus', width:'80px',align:"right", hidden:true}
      	                ],
                rowNum:50,
                rowList:[50,100,150,200],
                pager: '#pager',
                viewrecords: true,
                sortorder: "desc",
                caption:"",
                height: "400",
               	autowidth: true,
            	forcefit:false
            });
            

            $("#list").jqGrid('navGrid','#pager',
                    {
                    	edit:false,add:false,del:false,search:true,refresh:true
                    },
                    {}, // edit options
                    {}, // add options
                    {}, //del options
                    {multipleSearch:true} // search options
                    );
            

         	$("#list").addClass("srchresult");
         	$("#pg_pager").addClass("srchresult");
         	$("#gview_list").addClass("srchresult");         	

         	$("div").css("font-size","12px");
         	$("table").css("font-size","12px");
		});	
	</script>	

	<div id="report-history-main">
		<h1 class="page-title">History</h1>
			<div id="middle-page-container">
				<table id="list"><tr><td></td></tr></table>
				<div id="pager"></div>
			</div>
	</div>
				