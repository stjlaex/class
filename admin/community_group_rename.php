<?php 
/**				   	   				   community_group_rename.php
 */

$action='community_group_rename.php';
$action_post_vars=array('newcomtype','newcomid','comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newname'])){$newname=$_POST['newname'];}


include('scripts/sub_action.php');

if($sub=='Submit'){

	$community=array('id'=>'','type'=>$newcomtype,'name'=>$newname);
	$communityfresh=array();

	if($comid!=''){
		/* Editing an existing group */
		$communityfresh=$community;
		$community=(array)get_community($comid);
		if(isset($_POST['charge'])){$communityfresh['charge']=$_POST['charge'];}
		if(isset($_POST['sessions'])){
			$sessions=$_POST['sessions'];
			foreach($sessions as $sess){
				if(isset($communityfresh['sessions'])){$sep=':';}
				else{$sep='';}
				$communityfresh['sessions'].=$sep . 'A'.$sess;
				}
			}
		else{
			$communityfresh['sessions']='';
			}
		}

	$comid=update_community($community,$communityfresh);
	$action=$cancel;
	include('scripts/redirect.php');
	exit;

	}
else{
	if(isset($comid)){$com=get_community($comid);}
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
?>
	  <fieldset class="center">
		<div class="center">
		  <label for="Charge"><?php print_string('fee',$book);?></label>
			<input  name="charge" value="<?php print $com['charge'];?>" >
		</div>
	  </fieldset>

	  <fieldset class="center">
		<legend for="days"><?php print_string('sessions',$book);?></legend>
		<div class="center">
<?php
					  foreach($days as $day => $dayname){
						  $pos=strpos($com['sessions'],"A$day");
?>
						  <div>
						  <?php print_string($dayname);?>

						  <input type="checkbox" name="sessions[]" value="<?php print $day;?>" 
<?php if($pos!==false){print 'checked="checked"';}?>/>

						  </div>
<?php
						  }
?>
		</div>
	  </fieldset>
<?php
					  }

?>



	<input type="hidden" name="comid" value="<?php print $comid;?>" />
	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="newcomid" value="<?php print $newcomid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>

<?php
	}
?>
