<?php
/********************************************************************************************************
 * 	campaign.php - View file -  Controller - adopsreport/adopsreport.php
 *  CREATED BY	: AMin S
 *  FOR 		: GLAM INDIA
 *  VERSION		: 1.0
 *  CREATED ON 	: 04 Sept 2012
 ********************************************************************************************************/
error_reporting(E_ALL ^ E_NOTICE);
//if (!isset($reportTypeArr['Product Report'])){
	$reportTypeArr['Product Report'] = "''";
//}
?>

	<script src="<?php echo $this->config->base_url(); ?>public/js/grid_locale-en.js"></script>
	<script src="<?php echo $this->config->base_url(); ?>public/js/jqgrid.min.js"></script>
	<script src="<?php echo $this->config->base_url(); ?>public/js/report/campaign.js"></script>
	<link rel="stylesheet" href="<?php echo $this->config->base_url(); ?>public/css/ui.jqgrid.css" />
	
	<script>
		$(document).ready(function() {
			$("#searchBtn").click(function() {
				$.ajax({
				url: "<?php echo $this->config->base_url(); ?>index.php/report/adopsreport/getAdIDs",
				type: "post",
				data: {
					},
				// callback handler that will be called on success
				success: function(response, textStatus, jqXHR){
					jQuery("#listAdIds").jqGrid({ 
							datatype: "local",
							data: [],
							colNames:['Select','Advertisder Name', 'Ad Name', 'Ad ID','Ad Size','Start Date','End Date'], 
							colModel:[ {name:'select',index:'select', width:30, sorttype:"int"}, {name:'advertiser_name',index:'advertiser_name', width:150, sorttype:"date"}, {name:'ad_name',index:'ad_name', width:150}, {name:'ad_id',index:'ad_id', width:80, align:"right",sorttype:"float"}, {name:'ad_size',index:'ad_size', width:80, align:"right",sorttype:"float"}, {name:'start_date',index:'start_date', width:80,align:"right",sorttype:"float"}, {name:'end_date',index:'end_date', width:150, sortable:false} ], 
							rowNum:20, 
							rowList:[10,20,30], 
							pager: '#pagination', 
							sortname: 'id', 
							height: "100",
							viewrecords: true, 
							sortorder: "desc", 
							loadonce: true, 
							caption: "List of Ad Ids" 
						});
					}
				});
			});
		});
	</script>

	<div id="form-div">
	<h2 class="form-title"><?php echo $title ?></h2>		
		<div class="validation-div">
			<?php echo validation_errors(); ?>
		</div>			
		<?php echo form_open('report/adopsreport/campaign') ?>
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
						<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
					</div>
					<div class="row">			
						<label for="description">Description</label>
						<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
					</div>
					<div class="row">			
						<label for="sharing">Sharing</label>
						<div class="input-div">
							<input type="radio" name="sharing" value="1" id="sharing-private" /> Private
							<input type="radio" name="sharing" value="0" id="sharing-private" /> Public
							<br /><br />
							<input type="checkbox" name="report-header-downlaod" value="yes" id="report-header-downlaod" />Include Report Header in Download
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="form-group">				
				<div class="row form-inner-head">
					<span class="inner-head">Splash Ad</span>	<a href="javascript:;" class="form-close-div">Close</a>
				</div>
				<fieldset>
					<legend>Find Splash Ad:</legend>
					<div>
						<div class="row" class="row">
							<label for="advertiser-name">Advertiser name</label> 
							<div class="input-div"><?php echo $advertiser_name_autocomplete;?></div>
							
							<label for="advertiser-id">Advertiser Id</label>
							<div class="input-div">
							
							</div>
						</div>
						
						<div class="row">
							<label for="order-name">Order name</label> 
							<div class="input-div"><?php echo $order_name_autocomplete;?></div>
							
							<label for="order-id">Order Id</label>
							<div class="input-div">
							
							</div>
						</div>
					
						<div class="row">
							<label for="ad-name">Ad name</label> 
							<div class="input-div"><?php echo $ad_name_autocomplete;?></div>
							
							<label for="ad_id">Ad Id</label>
							<div class="input-div">
							
							</div>
						</div>
						
						<div class="row">
							<label for="creative-name">Creative Name</label> 
							<div class="input-div"><?php echo $creative_name_autocomplete;?></div>
							
							<label for="creative-id">Creative Id</label>
							<div class="input-div">
							
							</div>
						</div>
						
						<div class="row">
							<label for="creative-format-type">Creative Format Type</label> 
							<div class="input-div"><?php echo $creative_format_autocomplete;?></div>
							
							<label for="ad-size">Ad Size</label>
							<div class="input-div"><?php echo $ad_size_autocomplete;?></div>
						</div>
						
						<div class="row action-button">
							<input type="button" id="searchBtn" value="Search" name="search">
							<input type="button" id="restBtn" value="Reset" name="submit">
						</div>
					</div>
					<table id="listAdIds"></table> 
					<div id="pagination"></div>
				</fieldset>					
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
				<div id="form-group-3">
					<div class="row">
						<label for="predefined">Predefined</label> 
						<div class="input-div">
							<select id="predefined" name="predefined">
								<option value="Yesterday">Yesterday</option>
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
						<label for="last">Last</label> 
						<div class="input-div">
							<input type="input" name="last_num" value="" id="last_num" />
							<select id="last" name="last">
								<option value="Days">Days</option>
								<option value="Weeks">Weeks</option>
							</select>
						</div>
					</div>
					<div class="row">
						<label for="custom">Custom</label> 
						<div class="input-div">
							Start Date <input type="input" name="start-date" value="" id="start-date" />
							End Date <input type="input" name="end-date" value="" id="end-date" />
						</div>
					</div>		
					<div class="row">
						<label for="report-name">Report Schedule</label> 
						<div class="input-div"><a href="#">Not Scheduled</a></div>
					</div>						
				</div>
				
				<div class="row action-button">
					<input type="submit" name="save" value="Save" />
					<input type="submit" name="submit" value="Run" />
				</div>
			</div>
		</div>
	</form>
	</div>	