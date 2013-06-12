<?php 
/**
 *									new_import.php	
 */
$choice='new_import.php';
$action='new_import_action.php';

three_buttonmenu();

$maxsize=return_bytes(ini_get('upload_max_filesize'));

?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" 
	  method="post" enctype="multipart/form-data" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('requirements');?></legend>
		<?php print_string('importstudentfileinstructions',$book);?>
	  </fieldset>
	
	  <fieldset class="center">
		<legend><?php print_string('selectfile',$book);?></legend>
		<label for="Filename"><?php print_string('filename',$book);?></label>
		<input class="required" type="file" id="Filename" name="importfile" />
	  </fieldset>
	

	
 	<input type="hidden" name="MAX_FILE_SIZE" value="<?php print $maxsize;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
 	<input type="hidden" name="cancel" value="<?php print '';?>">
	</form>
  </div>















