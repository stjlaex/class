<?php
/**												report_bug.php
 *
 */

$choice='report_bug.php';
$action='post.php';

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('reportbug',$book);?></legend>

		<div class="left" >
		  <label for="Summary"><?php print_string('summary',$book);?></label>
		  <input name="summary" id="Summary" class="required"
		  tabindex="<?php print $tab++;?>" maxlength="100" size="40" />
		</div>


		<div class="left" >
		  <label for="Detail"><?php print_string('details',$book);?></label>
		  <textarea name="detail" id="Detail" maxlength="1200" tabindex="<?php print $tab++;?>" rows="8" cols="70"></textarea>
		</div>

		<div class="right">
		  <?php include('scripts/list_books.php'); ?>
		</div>

<?php
	$subject="Bug Report for ".$shortname."(".$CFG->schoolname.")";
?>

 	<input type="hidden" name="subject" value="<?php print $subject; ?>">
 	<input type="hidden" name="queue" value="class-bug">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print 'about.php'; ?>">
	</form>

  </fieldset>

	<fieldset class="center">
	<legend><?php print_string('thanks',$book);?></legend>
	<p><?php print_string('submittingbughelps',$book);?></p>
	<p><?php print_string('timetakenappreciated',$book);?></p>
	</fieldset>

  </div>
