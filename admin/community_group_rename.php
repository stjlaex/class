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

	if(isset($comid) and $comid!=''){
		/* Existing group is being edited. */
		$community=(array)get_community($comid);
		$communityfresh=(array)$community;
		$communityfresh['name']=$newname;
		$oldname=$community['name'];

		/* Changing the name of a form has implications for any
		 * classes which are named after it.
		 */
		if($community['type']=='form'){
			$oldcids=(array)list_forms_classes($oldname);
			foreach($oldcids as $oldcid){
				$d_c=mysql_query("SELECT subject_id FROM class WHERE id='$oldcid';");
				if(mysql_num_rows($d_c)>0){
					$bid=mysql_result($d_c,0);
					$newcid=$bid.''.$newname;
					mysql_query("UPDATE class SET id='$newcid' WHERE id='$oldcid';");
					mysql_query("UPDATE cidsid SET class_id='$newcid' WHERE class_id='$oldcid';");
					mysql_query("UPDATE tidcid SET class_id='$newcid' WHERE class_id='$oldcid';");
					mysql_query("UPDATE midcid SET class_id='$newcid' WHERE class_id='$oldcid';");
					}
				}
			}

		}
	else{
		/* New group is being created. */
		$community=array('id'=>'','type'=>$newcomtype,'name'=>$newname);
		if($newcomtype=='form'){$community['yeargroup_id']=$_POST['newyid'];}
		$comid=update_community($community);
		$community=(array)get_community($comid);
		$communityfresh=(array)$community;

		/* TODO: create subject classes in a similar many to the renaming above. */
		}


	/* Only two fields, charge and sessions, can be edited apart from the name. */
	if(isset($_POST['charge'])){$communityfresh['charge']=$_POST['charge'];}
	if(isset($_POST['sessions'])){
		$sessions=$_POST['sessions'];
		unset($communityfresh['sessions']);
		foreach($sessions as $sess){
			if(isset($communityfresh['sessions'])){$sep=':';}
			else{$sep='';$communityfresh['sessions']='';}
			$communityfresh['sessions'].=$sep . 'A'.$sess;
			}
		}
	else{
		$communityfresh['sessions']='';
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
				   tabindex="<?php print $tab++;?>" maxlength="30" class="required" value="<?php if(isset($com['name'])){print $com['name'];} ?>">
		</div>
	  </fieldset>


<?php

	if($newcomtype=='form'){
		$yeargroups=list_yeargroups();
		$selnewyid=get_form_yeargroup($com['name']);
?>
	  <fieldset class="center">
		<legend><?php print_string('yeargroups',$book);?></legend>
		<div class="center">
		<?php $required='yes'; include('scripts/list_year.php');?>
		</div>
<?php
		 }
	elseif($newcomtype=='TUTOR'){
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


<?php
	if(isset($comid)){
?>
	<input type="hidden" name="comid" value="<?php print $comid;?>" />
<?php
		}
?>
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
