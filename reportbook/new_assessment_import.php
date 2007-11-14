<?php
/*												new_assessment_import.php
*/

$action='new_assessment_action.php';
$rcrid=$respons[$r]['course_id'];

include('scripts/sub_action.php');

/*Check user has permission to configure*/
$perm=getCoursePerm($rcrid,$respons);
$neededperm='x';
include('scripts/perm_action.php');

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('selectfiletoimportfrom');?></legend>
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" name="importfile" />
		  <input type="hidden" name="MAX_FILE_SIZE" value="800000">	
	  </fieldset>

	  <input type="hidden" name="current" value="<?php print $action; ?>">
		<input type="hidden" name="choice" value="<?php print $choice; ?>">
		  <input type="hidden" name="cancel" value="<?php print $choice; ?>">
	</form>
  </div>

