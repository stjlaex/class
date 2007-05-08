<?php 
/**				   	   				   community_group_rename.php
 */

$action='community_group_rename.php';
$action_post_vars=array('newcomtype','newcomid');

if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$community=array('id'=>'','type'=>$newcomtype,'name'=>$newname);
	if($newcomid!=''){
		$communityfresh=$community;
		$community=array('id'=>$newcomid);
		}
	else{$communityfresh=array();}
	$newcomid=update_community($community,$communityfresh);
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
		  <label for="Newname"><?php print_string('newgroupname',$book);?></label>
			<input type="text" id="Newname" name="newname"
						tabindex="<?php print $tab++;?>" maxlength="10"
							class="required" value="" />
		</div>

	  </fieldset>

	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>

<?php
	}
?>
