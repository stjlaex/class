<?php
/**												report_assessments.php
 */

$action='report_assessments_view.php';
$choice='report_assessments.php';

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" 
		name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="center">
		<legend><?php print_string('collateforstudentsfrom',$book);?></legend>
		  <?php $required='yes'; include('scripts/list_pastoralgroup.php');?>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('choosetoinclude',$book);?></legend>
		<div class="left" >
		<?php $multi='12'; include('scripts/list_subjects.php');?>
		</div>
		<div class="right" >
		<?php include('scripts/list_assessment.php');?>
		</div>
	  </fieldset>
	  <input type="hidden" name="selcrid" value="<?php print $selcrid;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
