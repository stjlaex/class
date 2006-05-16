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
		<label for="importfile"><?php print_string('updatepasswordsdetail',$book);?></label>
		<p><?php print_string('',$book);?></p>
		<input type="radio" name="newpasswords" value="regenerate" />
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('emailreminders',$book);?></legend>
		<label for="importfile"><?php print_string('emailuserpasswordsdetail',$book);?></label>
		<p><?php print_string('',$book);?></p>
		<input type="radio" name="emailstaff"  value="reminders" />
	  </fieldset>
	
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
</form>  
</div>
