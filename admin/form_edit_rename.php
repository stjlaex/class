<?php 
/**									   		form_edit_rename.php
 */

$action='form_edit_rename.php';

if(isset($_POST['name'])){$oldname=$_POST['name'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
   	mysql_query("UPDATE form SET id='$newname', name='$newname'  WHERE 
		id='$oldname'");
   	mysql_query("UPDATE community SET name='$newname' WHERE 
		name='$oldname' AND type='form'");
   	mysql_query("UPDATE student SET form_id='$newname' WHERE 
		form_id='$oldname'");
	$result[]=$oldname.':'.$newname;
	$action=$cancel;
	include('scripts/redirect.php');
	exit;
	}
else{

	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
								action="<?php print $host; ?>">

	  <fieldset class="center">
		<legend><?php print_string('changegroupname',$book);?></legend>

		<div class="center">
		  <label for="Currentname"><?php print_string('currentgroupname',$book);?></label>
			<input type="text" id="Currentname"  tabindex="1"  maxlength="10"
						class="required" name="oldname" value="<?php print $oldname;?>" />
		</div>

		<div class="center">
		  <label for="Newname"><?php print_string('newgroupname',$book);?></label>
			<input type="text" id="Newname" name="newname"
						tabindex="2" maxlength="10"
							class="required" value="" />
		</div>

	  </fieldset>

	<input type="hidden" name="name" value="<?php print $oldname;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>

<?php
	}
?>
