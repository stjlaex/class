<?php 
/*									report_reports.php
*/

$action='report_reports_list.php';
$choice='report_reports.php';

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
<?php
 	$required='yes';
	include('scripts/list_pastoralgroup.php');
?>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
<?php
	 include('scripts/list_report.php');
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('limitbysubject',$book);?></legend>
<?php
	  $multi='1'; $bid='%'; $required='no';
	include('scripts/list_subjects.php');
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('coversheets',$book);?></legend>
		<label><?php print_string('generatecoversheet',$book);?></label>
		<input style="width:2em;"  id="Coversheet" 
		  type="checkbox" name="coversheet" value="yes" />
	  </fieldset>

	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>

