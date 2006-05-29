<?php 
/** 									year_end.php
 */

$action='year_end_action.php';
$choice='year_end.php';

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center"> 
		<legend>End of Year</legend> 

	<p>This option will promote students up to the next
	yeargroup. It will also archive all marks from the MarkBook which
	have been linked to an Assessment. It is important that any marks
	needed for future reference have been disignated as an Assessment
	in this way, as clicking YES empties the MarkBook, ready for the next
	academic year.<p/>

	</fieldset>

	<fieldset class="center"> 
	<legend>Confirm</legend>
	<p>Are you ready to continue?</p>
<?php
	check_yesno();
?>

	</fieldset> 


		<input type="hidden" name="cancel" value="<?php print ''; ?>">
		  <input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form> 
  </div>


