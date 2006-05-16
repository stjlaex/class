<?php
/*												new_stats_import.php
*/

$action='new_stats_action.php';

include('scripts/sub_action.php');

include('scripts/course_respon.php');

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">
	  <fieldset class="center"> 
		<label for="Description"><?php print_string('description');?></label>
		<input class="required" type="text" id="Description"
				name="description"  style="width:20em;" length="20" maxlength="59" />
	  </fieldset>

	  <fieldset class="center"> 
		<label for="File name"><?php print_string('filename');?></label>
		<input style="width:20em;" type="file" id="File name" name="importfile" />
		  <input type="hidden" name="MAX_FILE_SIZE" value="800000" /> 	
	  </fieldset>

	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
		<input type="hidden" name="current" value="<?php print $action; ?>"/>
		  <input type="hidden" name="choice" value="<?php print $choice; ?>"/>
	</form>

  </div>
