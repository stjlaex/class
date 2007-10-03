<?php
/*							new_assessment_scores.php
 */

$action='import_assessment_scores.php';

$eid=$_POST['id'];
include('scripts/sub_action.php');

	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');

$AssDef=fetchAssessmentDefinition($eid);

three_buttonmenu();
?>
<div id="heading">
Importing scores for <?php print $AssDef['Description']['value'];?>
</div>

<div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>"

	  <fieldset class="center">
		<legend><?php print_string('scoresareforthissubject');?></legend>
		<?php include('scripts/list_subjects.php');?>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('firstcolumn:studentidentifier');?></legend>

		<label for="enrolno"><?php print_string('enrolemntnumber');?></label>
		<input type="radio" name="firstcol" title="" id="enrolno" value="enrolno" />
		  <label for="sid"><?php print_string('studentdatabaseid');?></label>
		  <input type="radio" name="firstcol" id="sid" title="" value="sid" />
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('selectfiletoimportfrom');?></legend>
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" name="importfile" />
		<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
	  </fieldset>

 	<input type="hidden" name="eid" value="<?php print $eid; ?>">
 	<input type="hidden" name="cancel" value="<?php print $choice; ?>">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>


