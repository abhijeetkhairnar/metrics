<style>
	.row{
		padding-top: 2px;
	}
	.row label{
		padding-left:10px;
	}
</style>

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
							<label for="report-name">Report name</label> 
							<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
							
							<label for="description">Description</label>
							<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
						</div>
						
						<div class="row">
							<label for="report-name">Report name</label> 
							<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
							
							<label for="description">Description</label>
							<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
						</div>
						
						<div class="row">
							<label for="report-name">Report name</label> 
							<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
							
							<label for="description">Description</label>
							<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
						</div>
						
						<div class="row">
							<label for="report-name">Report name</label> 
							<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
							
							<label for="description">Description</label>
							<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
						</div>
						
						<div class="row">
							<label for="report-name">Report name</label> 
							<div class="input-div"><input type="input" name="report-name" value="" id="report-name" /></div>
							
							<label for="description">Description</label>
							<div class="input-div"><input type="input" name="description" value="" id="description" /></div>
						</div>
					</div>
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
	