<?php
/**							new_assessment_scores.php
 */

$action='new_assessment_scores_action.php';
$choice='new_assessment.php';

if(isset($_POST['eid'])){$eid=$_POST['eid'];}

include('scripts/sub_action.php');

	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');

$AssDef=fetchAssessmentDefinition($eid);

three_buttonmenu();
?>
<div id="heading">
<?php print get_string('importscores',$book).': '. $AssDef['Description']['value'];?>
</div>

<div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>"

	  <fieldset class="left">
		<legend><?php print_string('firstcolumnidentifier',$book);?></legend>

		<label for="enrolno"><?php print_string('enrolmentnumber','infobook');?></label>
		<input type="radio" name="firstcol" 
		  eitheror="sid"  class="requiredor" checked="checked" 
		  title="" id="enrolno" value="enrolno" />
		  
		<label for="sid"><?php print_string('studentdbid',$book);?></label>
		<input type="radio" name="firstcol" 
			eitheror="enrolno"  class="requiredor" 
			id="sid" title="" value="sid" />
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('selectfiletoimportfrom');?></legend>
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" 
		  class="required" name="importfile" />
		<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
	  </fieldset>

 	<input type="hidden" name="eid" value="<?php print $eid; ?>">
 	<input type="hidden" name="cancel" value="<?php print $choice; ?>">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>

