<?php
/**                                  student_reports.php    
 */

$action="student_reports_print.php";

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('subjectreports'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>
  <div class="content">
	<fieldset class="center">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<?php	  include('scripts/list_student_report.php');?>
		<input type="hidden" name="current" value="<?php print $action;?>"/>
		<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
		<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	  </form>
	</fieldset>
  </div>
