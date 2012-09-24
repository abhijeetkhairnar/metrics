<?php
/******************************************************
*	FileName 	 : standard.php
*	Created By 	 : Akshay Sardar.
*	Created Date : 27 AUG 2012
*	Description	 : standard view file.               
******************************************************/ 
?>
	<script type="text/javascript"  src="<?php echo base_url();?>public/js/dd-jquery.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>public/css/dd-style.css" />

	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$( "#start_date_range" ).datepicker({
				showOn: "button",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true
			});	
			$( "#end_date_range" ).datepicker({
				showOn: "button",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true
			});	
			
			$( "#dimensions" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getDimensions",
				pagination: false
			});			
			$( "#metrics" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getMetrics",
				pagination: false
			});
			$( "#filters" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getFilters",
				pagination: false
			})		
			
			
			
			$('#filters_left_ul li span input[type=checkbox]').live("click", function() {
				var self = this;
				if($(self).is(':checked')){
					$.ajax({url: '<?php echo base_url();?>index.php/report/adopsreport/getFilterInput',type: 'POST',data: { id : $(self).val() , text : $(self).parent().text() },dataType: 'html', 
					success: function(data){ 
						if(String($.trim(data)) == 'ListBox'){
							$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).parent().text()+'</div><div id="'+$(self).val()+'" class="row"></div></div>')
							$( "#"+$(self).val()).dd({
								source: "<?php echo base_url();?>index.php/report/adopsreport/getFilterDataJson?id="+$(self).val(),
								limit: 20
							});
						}else{
							$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row">'+data+'</div>')
						}
					}
				 });
				}else{
					$('#filters_advance #'+$(self).parent().parent().attr('class')+'_div').remove();
				}	
			});			
			$('#filters_right_ul li span.ui-icon-close').live("click", function() {
				$('#filters_advance #'+$(this).parent().attr('class')+'_div').remove();
			});
			
			
			$('.timeRangeCls').focus(function() {								   
			//  alert($('.' + $(this).parent().parent().attr('class') + ' input([name=date_range]:radio)').attr('id'));
			//	alert($(this).prev().prev('input[type=radio]').attr('name'));

			});
													
			
		});
	</script>

<div id="form-div">
	<h2 class="form-title"><?php echo $title ?></h2>		
		<div class="validation-div">
			<?php echo validation_errors(); ?>
		</div>			
		<?php echo form_open('report/adopsreport/standard') ?>
		<div class="form-data">
			<div class="row action-button">
				<input type="submit" name="save" value="Save" />
				<input type="submit" name="submit" value="Run" />
			</div>
			
			<div class="form-group">
				<div class="row form-inner-head">
					<span class="inner-head">Report Details</span>	<a href="javascript:;" class="form-close-div" onclick="toggleFormGroupDiv('form-group-1');">Close</a>
				</div>
				<div id="form-group-1">
					<div class="row">
						<label for="report-name">Report name</label> 
						<div class="input-div"><input type="input" name="report_name" value="" id="report_name" /></div>
					</div>
					<div class="row">			
						<label for="description">Description</label>
						<div class="input-div"><input type="input" name="report_desc" value="" id="report_desc" /></div>
					</div>
					<div class="row">			
						<label for="sharing">Sharing</label>
						<div class="input-div">
							<input type="radio" name="is_shared" value="1" id="is_shared_private" /> Private
							<input type="radio" name="is_shared" value="0" id="is_shared_public" /> Public
							<br /><br />
							<input type="checkbox" name="is_inc_header" value="1" id="is_inc_header" />Include Report Header in Download
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="form-group">
				<div class="row form-inner-head">
					<span class="inner-head">Time Range</span>	<a href="javascript:;" class="form-close-div">Close</a>
				</div>
				<div class="row">				
					<div class="form-note-center">
					Data Available: From 01-JAN-10 To 26-JUN-12 EST<br />
					[ hourly data Available From 21-Jun-2012 00:00 To 27-Jun-2012 13:00 ]
					</div>
				</div>
				<div id="form-group-2">
					<div class="row">						
						<label for="predefined">
							<input type="radio" name="date_range" id="predefined_radio" value="predefined_radio_checked" checked>
								Predefined
							</label> 
						<div class="input-div">
							<select id="predefined" name="predefined" class="timeRangeCls">
								<option value="Yesterday" selected="selected">Yesterday</option>
								<option value="Week to date">Week to date</option>
								<option value="Past 7 days">Past 7 days</option>
								<option value="Last week">Last week</option>
								<option value="Month to date">Month to date</option>
								<option value="Past 30 days">Past 30 days</option>
								<option value="Last month">Last month</option>
								<option value="Year to date">Year to date</option>
							</select>					
						</div>
					</div>
					<div class="row">						
						<label for="last">
							<input type="radio" name="date_range" value="last_radio_check" id="last_radio" />
							Last
						</label> 
						<div class="input-div">
							<input type="input" name="frequency_val" value="" id="frequency_val" class="timeRangeCls" />
							<select id="frequency_type" name="frequency_type">
								<option value="Days">Days</option>
								<option value="Weeks">Weeks</option>
							</select>
						</div>
					</div>
					<div class="row">						
						<label for="custom">
							<input type="radio" name="date_range" id="custom_radio" value="custom_radio_check" />
							Custom
						</label> 
						<div class="input-div">
							Start Date <input type="text" name="start_date_range" value="" id="start_date_range" class="timeRangeCls" /> &nbsp;&nbsp;
							End Date <input type="text" name="end_date_range" value="" id="end_date_range" class="timeRangeCls" />
						</div>
					</div>		
					<div class="row">
						<label for="report-name">Report Schedule</label> 
						<div class="input-div"><a href="#">Not Scheduled</a></div>
					</div>						
				</div>
				
			<div class="form-group">
				<div class="row form-inner-head">
					<span class="inner-head">Dimensions</span>	<a href="javascript:;" class="form-close-div">Close</a>
				</div>
				<div id="form-group-3">
					<div class="row">
						<div class="input-div">
							<div id="dimensions"></div>
						</div>
					</div>
				</div>
			</div>				
			<div class="form-group">
				<div class="row form-inner-head">
					<span class="inner-head">Metrics</span>	<a href="javascript:;" class="form-close-div">Close</a>
				</div>
				<div id="form-group-4">
					<div class="row">
						<div class="input-div">
							<div id="metrics"></div>
						</div>
					</div>
				</div>
			</div>	
			<div class="form-group">
				<div class="row form-inner-head">
					<span class="inner-head">Filters</span>	<a href="javascript:;" class="form-close-div">Close</a>
				</div>
				<div id="form-group-5">
					<div class="row">
						<div class="input-div">
							<div id="filters"></div>
							<div class="clear"></div>
							<div id="filters_advance" class="advance-div"></div>	
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>							
				<div class="row action-button">
					<input type="submit" name="save" value="Save" />
					<input type="submit" name="submit" value="Run" />
				</div>
			</div>
		</div>
		<input type="hidden" name="RUN_TYPE" value="<?php echo $report_type;?>">		
	</form>
</div>
	