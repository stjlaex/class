<?php
/*												support.php
 */

$action='post.php';
$choice='support.php';

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('contactsupport',$book);?></legend>

		<div class="left" >
		  <label for="Summary"><?php print_string('summary',$book);?></label>
		  <input name="summary" id="Summary" class="required"
			maxlength="100" size="60" />
		</div>

		<div class="right" >
		  <?php include('scripts/jsdate-form.php'); ?>
		</div>
		<div class="left" >
		  <label for="Detail"><?php print_string('details',$book);?></label>
		  <textarea name="detail" id="Detail" maxlength="1200" class="required"  
			rows="8" cols="60" ></textarea>
		</div>
<?php
	$subject="Support Request from ".$tid."(".$schoolname.")";
?>
 	<input type="hidden" name="subject" value="<?php print $subject; ?>">
 	<input type="hidden" name="queue" value="support">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print 'about.php'; ?>">
	</form>

  </fieldset>

	<fieldset class="center">
	  <legend><?php print_string('supportprocedure',$book);?></legend> 
	  <p><?php print_string('supportexplanation',$book);?></p>
	</fieldset>

  </div>
