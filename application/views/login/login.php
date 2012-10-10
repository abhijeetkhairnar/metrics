<?php
/******************************************************
*	FileName 	 : login.php
*	Created By 	 : Amin S.
*	Updated By 	 : Akshay S.
*	Created Date : 03 Oct 2012
*	Updated Date : 04 Oct 2012
*	Description	 : login view file.               
******************************************************/ 
?>

<div id="form-div">  
<!---------------- FORM - START ---------------->	
  <form method="POST" action="<?php echo base_url();?>index.php/login/login">
	<div id="login-form" class="form-group-body">
		<?php if (isset($message) && !empty($message)){?>
		<div class="row">
				<?php echo $message;?>
		</div>
		<?php } ?>
		<div class="row">
		  <label for="report-name">Email</label>
		  <div class="input-div">
			<input type="text" name="email" value="" id="email" tabindex="1" size="40"/>
		  </div>
		</div>
		
		<div class="row">
		  <label for="description">Password</label>
		  <div class="input-div">
			<input type="password" name="password" value="" id="password" tabindex="2" size="40"/>
		  </div>
		</div>		
		
		<div class="row">
			<input name="rememberme" id="rememberme" value="forever" tabindex="3" type="checkbox"> Remember Me
			<div class="log-button">
			<input type="submit" value="Log In" name="frm_submit" tabindex="4">
			</div>
		</div>					
	</div>
</form>
</div>
