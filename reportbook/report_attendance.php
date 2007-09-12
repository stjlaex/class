<?php
/**			       		report_attendance.php
 */

$action='report_attendance_list.php';
$choice='report_attendance.php';

$tomonth=date('n')-1;
$today=date('j');
$toyear=date('Y');
$todate=$toyear.'-'.$tomonth.'-'.$today;

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php $required='yes'; include('scripts/'.$listgroup);?>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('collatesince',$book);?></legend>
		<?php include('scripts/jsdate-form.php'); ?>
	  </fieldset>


	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </div

