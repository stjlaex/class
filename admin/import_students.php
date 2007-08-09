<?php 
/*												import_students.php	
*/
$choice='import_students.php';
$action='import_students_action0.php';

three_buttonmenu();
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
	
	  <fieldset class="center">
		<legend><?php print_string('records',$book);?></legend>
		<label for="multiline"><?php print_string('multiplelines',$book);?></label>
		<select class="required" id="multiline" name="multiline">
		  <option value="1">1</option>
		  <option value="2">2</option>
		  <option value="3">3</option>
		  <option value="4">4</option>
		  <option value="5">5</option>
		</select>
	  </fieldset>
	
 	<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
 	<input type="hidden" name="cancel" value="<?php print '';?>">
	</form>
  </div>















