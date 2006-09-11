<?php 
/**								passwords.php
 */

$choice='passwords.php';
$action='passwords_action.php';
three_buttonmenu();
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess"
	  enctype="multipart/form-data" method="post" action="<?php print $host; ?>">

	  <fieldset class="center">
		  <legend><?php print_string('regeneratepasswords',$book);?></legend>

		<div class="center">
		  <p><?php print_string('updatepasswordsdetail',$book);?></p>
		  <div class="right">
<?php 
	$checkname='passwords';
	$checkchoice='no';
	$checkcaption=get_string('regeneratepasswords',$book); 
	include('scripts/check_yesno.php')
?>
		  </div>
		</div>

		<div class="center">
		  <p><?php print_string('emailuserpasswordsdetail',$book);?></p>
		  <div class="right">
<?php 
	$checkname='emailstaff';
	$checkchoice='yes';
	$checkcaption=get_string('emailreminders',$book); 
	include('scripts/check_yesno.php')
?>
		  </div>
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('emailuserlist',$book);?></legend>

		<div class="center">
		  <p><?php print_string('emailadminwithuserlist',$book);?></p>
		  <div class="right">
<?php 
	$checkname='emailadmin';
	$checkcaption=get_string('emailuserlist',$book); 
	$checkchoice='no';
	include('scripts/check_yesno.php')
?>
		  </div>
		</div>
	  </fieldset>
	
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
</form>  
</div>
