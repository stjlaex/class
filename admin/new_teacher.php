<?php 
/**								new_teacher.php
 */

$choice='new_teacher.php';
$action='new_teacher_action.php';
$extrabuttons['importfrofile']=array('name'=>'sub','value'=>'Load');
three_buttonmenu($extrabuttons);
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess"
	  enctype="multipart/form-data" method="post" action="<?php print $host; ?>">

	<fieldset class="center"><legend><?php print_string('selectfiletoupload');?></legend>
	<label for="importfile"><?php print_string('filename');?></label>
	<p><?php print_string('formatoffilenewteacher',$book);?></p>
  	<input type="file" id="importfile" name="importfile" />
   </fieldset>	

	  <fieldset class="center">
		<legend><?php print_string('enterdetails');?></legend>

		<div class="left">
		<label for="ID"><?php print_string('teacherid');?></label>
		  <input class="required"  pattern="alphanumeric" 
				type="text" id="ID" name="newtid" maxlength="14" />

			<label for="Surname"><?php print_string('surname');?></label>
			<input class="required" pattern="alphanumeric"
			  type="text" id="Surname" name="surname" maxlength="30" />  

			  <label for="Forename"><?php print_string('forename');?></label>
			  <input class="required" pattern="alphanumeric"
				type="text" id="Forename" name="forename" maxlength="30" />
		</div>
		<div class="right">

		  <label for="Number"><?php print_string('staffno');?></label>
		  <input  class="required" pattern="integer"
			type="text" id="Number" name="no" maxlength="3" />

			<?php include('scripts/list_roles.php');?>

			  <label for="Email"><?php print_string('email');?></label>
			  <input pattern="email"
				  type="text" id="Email" name="email" maxlength="190" />
		</div>
	  </fieldset>
	
 	<input type="hidden" name="MAX_FILE_SIZE" value="800000">	
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $choice; ?>">
 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
</form>  
</div>
