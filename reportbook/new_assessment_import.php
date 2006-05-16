<?php
/*												new_assessment_import.php
*/

$action='new_assessment_action.php';

include('scripts/sub_action.php');

include('scripts/course_respon.php');

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

