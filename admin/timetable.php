<?php
/**								   timetable.php
 *
 */

$choice='timetable.php';
$action='timetable_export.php';

include('scripts/sub_action.php');

three_buttonmenu();
?>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center"> 
		<legend><?php print_string('timetable',$book);?></legend> 
		<?php print_string('timetable',$book);?>
	  </fieldset>
	  
	  <fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>

		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		</div>
	  </fieldset> 

		<input type="hidden" name="cancel" value="" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
</div>
