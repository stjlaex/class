<?php 
/**				   	   				   community_group_action.php
 */

$action='community_group.php';
$action_post_vars=array('newcomtype');

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$result[]=$oldname.':'.$newname;
	$action=$cancel;
	include('scripts/redirect.php');
	exit;
	}
elseif($sub=='Create'){

	three_buttonmenu();
?>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
								action="<?php print $host; ?>">

	  <fieldset class="divgroup">
		<h5><?php print_string('changegroupname',$book);?></h5>


		<div class="center">
		  <label for="Newname"><?php print_string('newgroupname',$book);?></label>
			<input type="text" id="Newname" name="newname" tabindex="<?php print $tab++;?>" maxlength="10" class="required" value="" />
		</div>

	  </fieldset>

	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>

<?php
	exit;
	}

include('scripts/redirect.php');
?>
