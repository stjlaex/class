<?php 
/*												new_extra_info_field.php
*/
$cancel='staff_list.php';
$action='new_extra_info_field_action.php';

$action_post_vars=array('subtype');

if(isset($_POST['subtype']) and $_POST['subtype']!=""){$subtype=$_POST['subtype'];}else{$subtype='';}

include('scripts/sub_action.php');

three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('newfield'); ?></legend>
		<label for="fieldname"><?php print_string('name'); ?></label>
		<input type="text" name="fieldname">
	  </fieldset>

	<?php include('scripts/set_action_post_vars.php'); ?>
	<input type="hidden" name="current" value="<?php print $action;?>">
	<input type="hidden" name="choice" value="<?php print $choice;?>">
	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
	</form>
  </div>
