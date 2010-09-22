<?php 
/**				   	   				   community_group_rename.php
 */

$action='community_group_rename.php';
$action_post_vars=array('newcomtype','newcomid');

if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}


include('scripts/sub_action.php');

if($sub=='Submit'){
	trigger_error($newcomtype.':'.$newname,E_USER_WARNING);

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
	if(isset($comids)){$com=get_community($comids[0]);}
	three_buttonmenu();
?>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
								action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('changegroupname',$book);?></legend>
		<div class="center">
		  <label for="Newname"><?php print_string('newgroupname',$book);?></label>
			<input type="text" id="Newname" name="newname"
				  tabindex="<?php print $tab++;?>" maxlength="30" class="required" value="<?php print $com['name']; ?>">
		</div>
	  </fieldset>

<?php

				  if($newcomtype=='TUTOR'){
					  $days=getEnumArray('dayofweek');
					  //$sessions=explode($com['detail'],':');

?>
	  <fieldset class="center">
		<div class="center">
		  <label for="Cost"><?php print_string('cost',$book);?></label>
			<input  name="cost" value="<?php print $com['cost'];?>" >
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend for="days"><?php print_string('sessions',$book);?></legend>
		<div class="center">
<?php
					  foreach($days as $day => $dayname){
?>
						  <div>
						  <?php print $dayname;?>

						  <input type="checkbox" name="sessions[]" value="<?php print $day;?>" 
<?php if(strpos($com['detail'],"A$day")){print 'checked="checked"';}?>/>

						  </div>
<?php
						  }
?>
		</div>
	  </fieldset>
<?php
					  }

?>



	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>

<?php
	}
?>
