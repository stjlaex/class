<?php
/**			       		report_incidents.php
 */

$action='report_incidents_list.php';
$choice='report_incidents.php';

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="left">
		<legend><?php print_string('reportonstudentsfrom');?></legend>
<?php
		  $required='yes';
		  include('scripts/list_pastoralgroup.php');
?>
	  </fieldset>

	  <fieldset class="right" >
		<legend><?php print_string('limittoonesubject');?></legend>
<?php
		  $required='no';
		  include('scripts/list_subjects.php');
?>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('collatecommentssince');?></legend>
		<?php include('scripts/jsdate-form.php'); ?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('publicationdateforprinting');?></legend>
		<?php $required='no'; include('scripts/jsdate-form.php'); ?>
	  </fieldset>


	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>  
  </div>
