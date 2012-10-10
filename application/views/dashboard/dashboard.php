<?php 
/******************************************************
*	FileName 	 : dashboard.php
*	Created By 	 : Aksahy Sardar.
*	Created Date : 25 Aug 2012.
*	Description	 : dashboard view file.       
******************************************************/
?>
<script type="text/javascript">
$(document).ready(function(){						   
	$(".report-favorites-row .close").click(function(){
		$(this).closest('div.report-favorites-row').animate({ opacity: 'hide' }, "slow");
	});
	
	$("#schedule_popup_close").live("click", function() {
		$("#popupbox").hide();
	});
	
	$('#report_list_popup_box').live("click", function() {
		$("#popupbox").hide();
		$.ajax({
			url: '<?php echo base_url();?>index.php/dashboard/dashboard/getReportList',
			type: 'POST',
			data: { 
					user_id : 104 
				},
			dataType: 'html', 
			success: function(data){ 
				var row = '';
				$("#popupbox").show();
				$('#popupbox').css('height',$(document).height());
				var obj = jQuery.parseJSON(data);
				for(var i =0;  i  < obj.length; i++){
					row += '<tr><td>' + obj[i].report_type + '</a></td><td> <a href="<?php echo base_url();?>index.php/report/adopsreport/standard?edit=true&id='+obj[i].report_id+'"> ' + obj[i].report_name + '</a></td></tr>';
				}
				var table = '<table class="table"> <tr> <th> Report Type</th> <th> Report Name </th> </tr>' + row + '</table>';
				$("#my_report_list").html(table);
			}
		 });
	});
});
</script>
<div id="dashboard-main">
	<h1 class="page-title">Dashboard</h1>
	
	<div id="dashbord-left">
		<h2 class="inner-title">
			<a href="javascript:void(0)" id="report_list_popup_box">
				My Report Summary
			</a>	
		</h2>
		<div id="summary-count-div">
			<div id="summary-count-warrper">
				<div class="grey-box">
					<div class="count-val">0</div>
					<div class="text-val">Running Now</div>
				</div>
				<div class="grey-box">
					<div class="count-val"><?php echo $resultSet; ?></div>
					<div class="text-val">Last 7 days</div>
				</div>
			</div>	
			
			<div id="summary-content-wapper">
				<div class="summary-row">
					<h3>Next scheduled reports:</h3>
					<div class="content-row">
						<p><a href="#">Crayola_monthly_2012</a> at 07/20/2012 20:00</p>
						<p><a href="#">daily_pushdowns</a> at 06/21/2012 20:00</p>
					</div>
				</div>
				
				<div class="summary-row">
					<h3>System status:</h3>
					<div class="content-row">
						<p>DataSetName refresh delay - Reason:</p>
						<p>Data available through 20-JUN-2012</p>
					</div>
					<div class="content-row">
						<p>DataSetName refresh delay - Reason:</p>
						<p>Data available through 20-JUN-2012</p>
					</div>
					<div class="content-row">
						<p>DataSetName refresh delay - Reason:</p>
						<p>Data available through 20-JUN-2012</p>
					</div>	
				</div>				
			
				<div class="summary-row">
					<h3>System news:</h3>
					<div class="content-row">
						<p>02-JUL-2012: New social <a href="#">activity report</a> available.</p>
						<p>02-JUL-2012: System Maintenance scheduled for 07/04/2012 (EST). more...</p>
					</div>
				</div>			
			
				<div class="summary-row">
					<h3>My settings:</h3>
					<div class="content-row">
						<p><label for="role">Role: </label>
							<select id="role" name="role">
								<option value="">select a role...</option>
								<option>role1</option>
								<option>role2</option>
								<option>role3</option>
							</select>
						</p>
					</div>
				</div>				
			
			</div>
					
		</div>
	</div>
	
	<div id="dashbord-right">
		<h2 class="inner-title">My Report Favorites</h2>	
		<div id="report-favorites-div">


			<div class="report-favorites-row">
				<!-- report-favorites-row - start -->
				<div class="content-row">
					<div class="left-content">
						<h2>Crayola monthly 2012</h2>
						<p>Splash Engagement (Campaign)</p>
						<p>Time range: Last 7 days</p>
						<p>Last run: 12/20/2012 02:23:33</p>
					</div>
					<div class="right-content">
						<div class="run-div">
							<a href="#">Run</a>
						</div>
						<div class="action-icon-div">
							<a href="javascript:void(0)" class="close">Close</a>
							<a href="#">Clone</a>	
							<a href="#">Details</a>
						</div>
					</div>					
				</div>		
			<!-- report-favorites-row - end -->
			</div>
		

			<div class="report-favorites-row">
				<!-- report-favorites-row - start -->
				<div class="content-row">
					<div class="left-content">
						<h2>Homepage pushdown</h2>
						<p>Pushdown Report (Instant Inventory)</p>
						<p>Time range: 7 days</p>
						<p>Last run: 12/20/2012 02:23:33</p>
					</div>
					<div class="right-content">
						<div class="run-div">
							<a href="#">Run</a>
						</div>
						<div class="action-icon-div">
							<a href="javascript:void(0)" class="close">Close</a>
							<a href="#">Clone</a>	
							<a href="#">Details</a>
						</div>
					</div>					
				</div>		
			<!-- report-favorites-row - end -->
			</div>


			<div class="report-favorites-row">
				<!-- report-favorites-row - start -->
				<div class="content-row">
					<div class="left-content">
						<h2>URL report1432</h2>
						<p>URL Report (Campaign)</p>
						<p>Time range: 7 days</p>
						<p>Last run: 12/20/2012 02:23:33</p>
					</div>
					<div class="right-content">
						<div class="run-div">
							<a href="#">Run</a>
						</div>
						<div class="action-icon-div">
							<a href="javascript:void(0)" class="close">Close</a>
							<a href="#">Clone</a>	
							<a href="#">Details</a>
						</div>
					</div>					
				</div>		
			<!-- report-favorites-row - end -->
			</div>

			<div class="report-favorites-row">
				<!-- report-favorites-row - start -->
				<div class="content-row">
					<div class="left-content">
						<h2>Geo oneday largeads</h2>
						<p>Custom Template (Inventory)</p>
						<p>Time range: 7 days</p>
						<p>Last run: 12/20/2012 02:23:33</p>
					</div>
					<div class="right-content">
						<div class="run-div">
							<a href="#">Run</a>
						</div>
						<div class="action-icon-div">
							<a href="javascript:void(0)" class="close">Close</a>
							<a href="#">Clone</a>	
							<a href="#">Details</a>
						</div>
					</div>					
				</div>		
			<!-- report-favorites-row - end -->
			</div>
			<div class="report-favorites-row">
				<!-- report-favorites-row - start -->
				<div class="content-row">
					<div class="left-content">
						<h2>US 1daybreakdown 2012</h2>
						<p>Vertical Fill (Inventory)</p>
						<p>Time range: 1 day</p>
						<p>Last run: 12/20/2012 02:23:33</p>
					</div>
					<div class="right-content">
						<div class="run-div">
							<a href="#">Run</a>
						</div>
						<div class="action-icon-div">
							<a href="javascript:void(0)" class="close">Close</a>
							<a href="#">Clone</a>	
							<a href="#">Details</a>
						</div>
					</div>					
				</div>		
			<!-- report-favorites-row - end -->
			</div>

			<div class="report-favorites-row">
				<div class="add-favorites-report"><a href="#">+ Add Favorite Report</a></div>
			</div>
		</div>
		
		<!------------------------ My Report POPUP START --------------------------->				
			<div id="popupbox" >
				<div class="popupbox-600"> 
					<h1 class="popbox-head">My Reports</h1>
					<div id="my_report_list"></div>
					<div class="row">
						<input type="button" name="schedule_popup_close" value="Close" id="schedule_popup_close" />
					</div>
				</div>
			</div>
		<!------------------------ My Report POPUP END --------------------------->	
			
	</div>	
</div>