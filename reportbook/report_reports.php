<?php 
/**									report_reports.php
 */

$action='report_reports_list.php';
$choice='report_reports.php';

include('scripts/sub_action.php');


three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php $required='yes'; include('scripts/'.$listgroup);?>
	  </fieldset>

<?php
	  if($reportpubs=='yes'){
?>
	  <fieldset class="center">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
<?php
		include('scripts/list_report_wrapper.php');
?>
	  </fieldset>

<?php
		  }
	  else{
?>

	  <fieldset class="center">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
<?php
		include('scripts/list_report.php');
?>
	  </fieldset>
<?php
	  }
?>

	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>

