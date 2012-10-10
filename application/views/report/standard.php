<?php
//echo "<pre>"; print_r($this->session);echo "</pre>"; exit;
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
				dateFormat : "d-M-yy",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true,
				onSelect: function( selectedDate ) {
					$( "#end_date_range" ).datepicker( "option", "minDate", selectedDate );
				}				
			});	
			$( "#end_date_range" ).datepicker({
				showOn: "button",
				dateFormat : "d-M-yy",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true,
				onSelect: function( selectedDate ) {
					$( "#start_date_range" ).datepicker( "option", "maxDate", selectedDate );
				}
			});	
			$( "#schedule_popup_start_date" ).datepicker({
				showOn: "button",
				dateFormat : "d-M-yy",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true,
				onSelect: function( selectedDate ) {
					$( "#schedule_popup_end_date" ).datepicker( "option", "minDate", selectedDate );
				}	
			});				
			$( "#schedule_popup_end_date" ).datepicker({
				showOn: "button",
				dateFormat : "d-M-yy",
				buttonImage: "<?php echo base_url();?>public/images/calendar.png",
				buttonImageOnly: true,
				onSelect: function( selectedDate ) {
					$( "#schedule_popup_start_date" ).datepicker( "option", "maxDate", selectedDate );
				}
			});				
			
			
			$( "#dimensions" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getDimensions",
				pagination: false,
				minSearchCharLength: <?php echo $this->config->item('min_search_char_lenght'); ?>,
				<?php if($dimensions){ ?>
				rightSideData: <?php echo $dimensions; ?>
				<?php } ?>				
			});			
			$( "#metrics" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getMetrics",
				pagination: false,
				minSearchCharLength: <?php echo $this->config->item('min_search_char_lenght'); ?>,
				<?php if($metrics){ ?>
				rightSideData: <?php echo $metrics; ?>
				<?php } ?>				
			});
			$( "#filters" ).dd({
				source: "<?php echo base_url();?>index.php/report/adopsreport/getFilters",
				pagination: false,
				minSearchCharLength: <?php echo $this->config->item('min_search_char_lenght'); ?>,
				<?php if($filtersDataKey){ ?>
				rightSideData: <?php echo $filtersDataKey; ?>
				<?php } ?>				
			})		
				
			<?php
			/***************************************************************/
			/*	Dynamic creation of filter on loading the edit and post page	
			/***************************************************************/
			if($_REQUEST){	
			?>
			var reportFiltersBody = new Array();
				<?php					
					foreach($reportFiltersBody as $key => $val){ ?>
					reportFiltersBody[<?php echo $key; ?>] = <?php echo $val; ?>;
				<? } ?>			
			
				$('#filters_right_ul li').each(function(){
					var self = this;
				//alert(reportFiltersBody[$('.'+$(self).attr('class')+' input[type=hidden]').val()]);
				//alert($('.'+$(self).attr('class')+' input[type=hidden]').val());

					$.ajax({url: '<?php echo base_url();?>index.php/report/adopsreport/getFilterInput',type: 'POST',data: { id : $('.'+$(self).attr('class')+' input[type=hidden]').val() , text :$(self).text() },dataType: 'html', 
						success: function(data){ 
							if(String($.trim(data)) == 'ListBox'){
							
								/************************ ListBox **************************/							
								$('#filters_advance').append('<div id="'+$(self).attr('class')+'_div" class="row"><div class="label-div">'+$(self).text()+'</div><div id="'+$('.'+$(self).attr('class')+' input[type=hidden]').val()+'" class="row"></div></div>')
								$( "#"+$('.'+$(self).attr('class')+' input[type=hidden]').val()).dd({
									source: "<?php echo base_url();?>index.php/report/adopsreport/getFilterDataJson?inputtype=ListBox&id="+$('.'+$(self).attr('class')+' input[type=hidden]').val(),
									limit: 20,
									minSearchCharLength: <?php echo $this->config->item('min_search_char_lenght'); ?>,
									rightSideData: reportFiltersBody[$('.'+$(self).attr('class')+' input[type=hidden]').val()]
								});
								/************************ ListBox **************************/
							
							}else if(String($.trim(data)) == 'Search'){	
							
								/************************ TextBoxAutoComplete Search **************************/						
								$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).text()+'</div><input type="text" name="'+$('.'+$(self).attr('class')+' input[type=hidden]').val()+'" value="" size="106" id="'+$('.'+$(self).attr('class')+' input[type=hidden]').val()+'"></div>')			
													
								$('#'+$('.'+$(self).attr('class')+' input[type=hidden]').val()).val(reportFiltersBody[$('.'+$(self).attr('class')+' input[type=hidden]').val()]);
								autoCompleteFun($('.'+$(self).attr('class')+' input[type=hidden]').val());								
								/************************ TextBoxAutoComplete Search **************************/						
							
							}else{	
							
								/************************ Default **************************/
								$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).text()+'</div>'+data+'</div>')
								$('#'+$('.'+$(self).attr('class')+' input[type=hidden]').val()).val(reportFiltersBody[$('.'+$(self).attr('class')+' input[type=hidden]').val()]);
								/************************ Default **************************/
							
							}
						}
					});
						
					
				});	
						
			<? } 
			/***************************************************************/
			?>
			$('#filters_left_ul li span input[type=checkbox]').live("click", function() {
				var self = this;
				if($(self).is(':checked')){
					$.ajax({url: '<?php echo base_url();?>index.php/report/adopsreport/getFilterInput',type: 'POST',data: { id : $(self).val() , text : $(self).parent().text() },dataType: 'html', 
					success: function(data){ 
						if(String($.trim(data)) == 'ListBox'){
						
						/************************ ListBox **************************/						
							$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).parent().text()+'</div><div id="'+$(self).val()+'" class="row"></div></div>')
							$( "#"+$(self).val()).dd({
								source: "<?php echo base_url();?>index.php/report/adopsreport/getFilterDataJson?inputtype=ListBox&id="+$(self).val(),
								limit: 20,
								minSearchCharLength: <?php echo $this->config->item('min_search_char_lenght'); ?>
							});
						/************************ ListBox **************************/
						
						}else if(String($.trim(data)) == 'Search'){
												
							/************************ TextBoxAutoComplete Search **************************/						
							$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).parent().text()+'</div><input type="text" name="'+$(self).val()+'" id="'+$(self).val()+'" value="" size="106"></div>')												
							
							autoCompleteFun($(self).val());
							/************************ TextBoxAutoComplete Search **************************/	
						}else{
						
							/************************ Default **************************/
							$('#filters_advance').append('<div id="'+$(self).parent().parent().attr('class')+'_div" class="row"><div class="label-div">'+$(self).parent().text()+'</div>'+data+'</div>')
							/************************ Default **************************/						
						}
						
					}
				 });
				}else{
					$('#filters_advance #'+$(self).parent().parent().attr('class')+'_div').remove();
				}	
			});
				
			function split( val ) {
				return val.split( /,\s*/ );
			}
			function extractLast( term ) {
				return split( term ).pop();
			}			
			function autoCompleteFun(autoCompleteInputId){
				/************************ TextBoxAutoComplete Search Funtion - START **************************/
				$( "#"+autoCompleteInputId)
						// don't navigate away from the field on tab when selecting an item
						.bind( "keydown", function( event ) {
							if ( event.keyCode === $.ui.keyCode.TAB &&
									$( this ).data( "autocomplete" ).menu.active ) {
								event.preventDefault();
							}
						})
						.autocomplete({
								source: function( request, response ) {
									$.getJSON( "<?php echo base_url();?>index.php/report/adopsreport/getFilterDataJson?inputtype=Search&id="+autoCompleteInputId, {
										searchval: extractLast( request.term )
									}, response );
								},
								search: function() {
									// custom minLength
									var term = extractLast( this.value );
									if ( term.length < <?php echo $this->config->item('min_search_char_lenght'); ?> ) {
										return false;
									}
								},
								focus: function() {
									// prevent value inserted on focus
									return false;
								},
								select: function( event, ui ) {
									var terms = split( this.value );
									// remove the current input
									terms.pop();
									// add the selected item
									terms.push( ui.item.value );
									// add placeholder to get the comma-and-space at the end
									terms.push( "" );
									this.value = terms.join( ", " );
									return false;
								}
							});
			/************************ TextBoxAutoComplete Search Funtion - END **************************/
			}
			
					
			$('#filters_right_ul li span.ui-icon-close').live("click", function() {
				$('#filters_advance #'+$(this).parent().attr('class')+'_div').remove();
			});
			
			$('#popupbox').css('height',$(document).height());
			$('#schedule_popup_box').live("click", function() {
				$('#popupbox').toggle();
			});	
			$('#schedule_popup_cancel').live("click", function() {
				$('#popupbox').toggle();
			});	
			$('#schedule_popup_done').live("click", function() {
				$('#popupbox').toggle();
				$('#schedule_popup_status').val('1');
				$('#schedule_popup_box').html('Scheduled');
                                $('#is_scheduled').val('1');
                                
			});	
			
			$('.inputChild').live("focus", function() {
				$(this).parent().parent().children().children('.radioParent').attr('checked',true);
			});	
			
		});
	</script>

<div id="form-div">
  <h2 class="form-title"><?php echo $title ?></h2>
<!---------------- ERROR - START ---------------->	
  <?php if (is_array($error_msg)){?>
  <div class="validation-div msg_error">
    <ul>
      <?php foreach($error_msg as $key => $val) {
				echo '<li>'.$val.'</li>';
			}?>
    </ul>
  </div>
  <?php } ?>
<!---------------- ERROR - END ---------------->	

<!---------------- FORM - START ---------------->	
  <?php 
	if (isset($_GET[id]) && !empty($_GET['id'])){
			echo form_open("report/adopsreport/standard?edit=true&id=".$_GET['id']);
	}else{
			echo form_open("report/adopsreport/standard");
	}?>
  <div class="form-data">
	
	<!---------------- ACTION BUTTON - START ---------------->	  
	<div class="row action-button">      
	  <?php if (isset($reportHeader['report_id']) && !empty($reportHeader['report_id'])){?>
		<input type="submit" name="update" value="Update" />
	  <? }else { ?>
		<input type="submit" name="save" value="Save" />
	  <?php } ?>
	  <input type="submit" name="run" value="Run" />
    </div>
	<!---------------- ACTION BUTTON - START ---------------->		
    
	<!---------------- GROUP - START ---------------->
	<div class="form-group">
      <div class="form-inner-head"> <span class="inner-head">Report Details</span> <a href="javascript:;" class="form-close-div" onclick="toggleFormGroupDiv('form-group-1');">Close</a> </div>
      <div id="form-group-1" class="form-group-body">
        <div class="row">
          <label for="report-name">Report name</label>
          <div class="input-div">
            <input type="text" name="report_name" value="<?php echo $reportHeader['report_name']; ?>" id="report_name"  size="60"/>
          </div>
        </div>
        <div class="row">
          <label for="description">Description</label>
          <div class="input-div">
            <textarea name="report_desc" id="report_desc" cols="46" rows="3"><?php echo $reportHeader['report_desc']; ?></textarea>
          </div>
        </div>
        <div class="row">
          <label for="sharing">Sharing</label>
          <div class="input-div">
            <input type="radio" name="is_shared" value="1" id="is_shared_private" <?php if($reportHeader['is_shared'] == 1){ ?> checked="checked" <?php } ?> />
            Private
            <input type="radio" name="is_shared" value="0" id="is_shared_public" <?php if($reportHeader['is_shared'] == 0){ ?> checked="checked" <?php } ?> />
            Public <br />
            <br />
            <input type="checkbox" name="is_inc_header" value="100" id="is_inc_header"  <?php if($reportHeader['is_inc_header'] == 1){ ?> checked="checked" <?php } ?> />
            Include Report Header in Download </div>
        </div>
      </div>
    </div>
	<!---------------- GROUP - END ---------------->
	
	<!---------------- GROUP - START ---------------->
    <div class="form-group">
      <div class="form-inner-head"> <span class="inner-head">Time Range</span> <a href="javascript:;" class="form-close-div">Close</a> </div>
      <div id="form-group-2" class="form-group-body">
        <div class="row">
          <div class="form-note-center"> Data Available: From 01-JAN-10 To 26-JUN-12 EST<br />
            [ hourly data Available From 21-Jun-2012 00:00 To 27-Jun-2012 13:00 ] </div>
        </div>
        <div class="row">
          <label for="predefined">
          <input type="radio" name="date_range" id="predefined_radio" value="predefined_radio_checked"  <?php if($reportHeader['date_range'] == "predefined_radio"|| $reportHeader['date_range'] == "" ){ ?> checked="checked" <?php } ?> class="radioParent" />
          Predefined </label>
          <div class="input-div">
            <select id="predefined" name="predefined" class="inputChild" >
              <?php
								foreach($this->config->item('predefined_date_range') as $val){
									if($val == $reportHeader['date_range_desc']){ $selected = 'selected="selected"'; }else{	$selected = ''; }
									echo '<option value="'.$val.'" '.$selected.'> '.$val.'</option>';
								}
							?>
            </select>
          </div>
        </div>
        <div class="row">
          <label for="last">
          <input type="radio" name="date_range" value="last_radio_check" id="last_radio" <?php if($reportHeader['date_range'] == "last_radio"){ ?> checked="checked" <?php } ?> class="radioParent" />
          Last </label>
          <div class="input-div">
            <input type="text" name="date_range_num" value="<?php echo $reportHeader['date_range_num']; ?>" id="date_range_num"  class="inputChild" />
            <select id="date_range_dsc" name="date_range_dsc" class="inputChild">
              <?php
					foreach($this->config->item('last_date_range') as $val){
						if($val == $reportHeader['date_range_dsc']){ $selected = 'selected="selected"'; }else{	$selected = ''; }
						echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
					}
				?>
            </select>
          </div>
        </div>
        <div class="row">
          <label for="custom">
          <input type="radio" name="date_range" id="custom_radio" value="custom_radio_check" <?php if($reportHeader['date_range'] == "custom_radio"){ ?> checked="checked" <?php } ?> class="radioParent" />
          Custom </label>
          <div class="input-div"> Start Date
            <input type="text" name="start_date_range" id="start_date_range"  <?php if($reportHeader['date_range'] == "custom_radio"){ ?> value="<?php echo $reportHeader['start_date_range']?>"  <?php }else{?> value=""<?php } ?> class="inputChild" />
            &nbsp;&nbsp;
            End Date
            <input type="text" name="end_date_range"  id="end_date_range" <?php if($reportHeader['date_range'] == "custom_radio"){ ?> value="<?php echo $reportHeader['end_date_range']?>"  <?php }else{?> value=""<?php } ?> class="inputChild" />
          </div>
        </div>
        <div class="row">
          <label for="report-name">Report Schedule</label>
          <div class="input-div"><a href="javascript:;" id="schedule_popup_box" class="boldCls">Not Scheduled</a></div>
        </div>
      </div>
    </div>
	<!---------------- GROUP - END ---------------->
	
	<!---------------- GROUP - START ---------------->
    <div class="form-group">
      <div class="form-inner-head"> <span class="inner-head">Dimensions</span> <a href="javascript:;" class="form-close-div">Close</a> </div>
      <div id="form-group-3" class="form-group-body">
        <div class="row">
          <div class="input-div">
            <div id="dimensions"></div>
          </div>
        </div>
      </div>
    </div>
	<!---------------- GROUP - END ---------------->
	
	<!---------------- GROUP - START ---------------->
    <div class="form-group">
      <div class="form-inner-head"> <span class="inner-head">Metrics</span> <a href="javascript:;" class="form-close-div">Close</a> </div>
      <div id="form-group-4" class="form-group-body">
        <div class="row">
          <div class="input-div">
            <div id="metrics"></div>
          </div>
        </div>
      </div>
    </div>
	<!---------------- GROUP - END ---------------->
	
	<!---------------- GROUP - START ---------------->
    <div class="form-group">
      <div class="form-inner-head"> <span class="inner-head">Filters</span> <a href="javascript:;" class="form-close-div">Close</a> </div>
      <div id="form-group-5" class="form-group-body">
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
	<!---------------- GROUP - END ---------------->
	
    <!------------------------ SCHEDULE POPUP START --------------------------->
    <div id="popupbox" >
      <div class="popupbox-800">
        <div class="form-group form-group-body"> 
          <div class="row"> <span class="inner-head">Report Details </span> </div>
          <div class="row">
            <label for="schedule_popup_type">
            <input type="radio" value="1" name="schedule_popup_criteria" id="schedule_popup_criteria_criteria_predefine"   <?php if($reportHeader['scheduler_date_range'] == "predefined_radio"|| $reportHeader['scheduler_date_range'] == "" ){ ?> checked="checked" <?php } ?>  class="radioParent" />
            Predefined </label>
            <div class="input-div">
              <select id="schedule_popup_type" name="schedule_popup_type" class="inputChild">
                <?php
										foreach($this->config->item('scheduler_predefied_date_range') as $val){
											if($val == $reportHeader['frequency_type']){ $selected = 'selected="selected"'; }else{	$selected = ''; }
											echo '<option value="'.$val.'" '.$selected.'> '.$val.'</option>';
										}
									?>
              </select>
            </div>
          </div>
          <div class="row">
            <label for="last">
            <input type="radio" value="2" name="schedule_popup_criteria" id="schedule_popup_criteria_last" <?php if($reportHeader['scheduler_date_range'] == "last_radio"){ ?> checked="checked" <?php } ?> class="radioParent">
            Last </label>
            <div class="input-div">
              <input type="text" id="schedule_popup_date_range_num" value="<?php echo $reportHeader['frequency_num']; ?>" name="schedule_popup_date_range_num" class="inputChild">
              <select name="schedule_popup_date_range_dsc" id="schedule_popup_date_range_dsc" class="inputChild">
                <?php
										foreach($this->config->item('last_date_range') as $val){
											if($val == $reportHeader['frequency_type']){ $selected = 'selected="selected"'; }else{	$selected = ''; }
											echo '<option value="'.$val.'" '.$selected.'>'.$val.'</option>';
										}
									?>
              </select>
            </div>
          </div>
          <div class="row">
            <label for="schedule_popup_every_day">
            <input type="radio" value="3" name="schedule_popup_criteria" id="schedule_popup_criteria_custom"  <?php if($reportHeader['scheduler_date_range'] == "custom_radio"){ ?> checked="checked" <?php } ?> class="radioParent">
            Custom </label>
            <div class="input-div"> Start Date
              <input type="text" name="schedule_popup_start_date" id="schedule_popup_start_date" <?php if($reportHeader['scheduler_date_range'] == "custom_radio"){ ?> value="<?php echo $reportHeader['start_date']?>"  <?php }else{?> value=""<?php } ?> class="inputChild" />
              &nbsp;&nbsp;
              End Date
              <input type="text" name="schedule_popup_end_date" id="schedule_popup_end_date" <?php if($reportHeader['scheduler_date_range'] == "custom_radio"){ ?> value="<?php echo $reportHeader['end_date']?>"  <?php }else{?> value=""<?php } ?> class="inputChild" />
            </div>
          </div>
          <div class="row">
            <label for="schedule_popup_emails">Email</label>
            <div class="input-div">
              <input type="text" value="<?php echo $reportHeader['email']?>" id="schedule_popup_emails" name="schedule_popup_emails" size="70">
            </div>
          </div>
          <div class="row action-button">
            <input type="hidden" name="schedule_popup_status" value="0" id="schedule_popup_status" />
            <input type="button" name="schedule_popup_cancel" value="Cancel" id="schedule_popup_cancel" />
            <input type="button" name="schedule_popup_done" value="Done" id="schedule_popup_done" />
          </div>
        </div>
      </div>
    </div>
    <!------------------------ SCHEDULE POPUP END --------------------------->
	
	<!---------------- ACTION BUTTON - START ---------------->
    <div class="row action-button">      
	   <?php if (isset($reportHeader['report_id']) && !empty($reportHeader['report_id'])){?>
		<input type="submit" name="update" value="Update" />
	  <? }else { ?>
		<input type="submit" name="save" value="Save" />
	  <?php } ?>
	  <input type="submit" name="run" value="Run" />
    </div>
	<!---------------- ACTION BUTTON - START ---------------->
	
</div>
<input type="hidden" name="run_type" value="1">
<input type="hidden" id="is_scheduled" name="is_scheduled" value="0">
<input type="hidden" name="report_id" value="<?php echo $reportHeader['report_id'];?>">
</form>
</div>
