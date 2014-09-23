<?php 
/**								new_teacher.php
 */

$choice='new_teacher.php';
$action='new_teacher_action.php';

three_buttonmenu($extrabuttons);
?>
<div class="content">
	<form name="formtoprocess" id="formtoprocess" enctype="multipart/form-data" method="post" action="<?php print $host; ?>">

	  <fieldset id="viewcontent" class="divgroup">
      <h5><?php print_string('selectfiletoupload');?></h5>
		  <label for="importfile"><?php print_string('filename');?></label>
		  <p><?php print_string('formatoffilenewteacher',$book);?></p>
		  <input type="file" id="importfile" name="importfile" />
	  </fieldset>	
    <input type="hidden" name="MAX_FILE_SIZE" value="800000">	
		<input type="hidden" name="current" value="<?php print $action; ?>">
		<input type="hidden" name="choice" value="<?php print $choice; ?>">
		<input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
</div>
