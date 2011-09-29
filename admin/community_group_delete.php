<?php
/**								   community_group_delete.php
 *
 */

$choice='community_group.php';
$action='community_group_delete.php';
$action_post_vars=array('newcomtype','comids');

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}else{$comids=array();}

include('scripts/sub_action.php');

if(sizeof($comids)==0){
		$result[]=get_string('youneedtoselectsomething');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

if($sub=='Submit'){

	include('scripts/answer_action.php');

	foreach($comids as $comid){
		$com=(array)get_community($comid);
		delete_community($com);
		}

	$action=$choice;
	include('scripts/results.php');
	include('scripts/redirect.php');
	}
else{

	three_buttonmenu();

?>
<div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	<fieldset class="center"> 
		<legend><?php print_string('becareful',$book);?></legend>
	  <p class="center warn">
	<?php print_string('deletecommunitygroups',$book); ?>
	  </p>
	</fieldset>

	<fieldset class="center divgroup"> 
		<legend><?php print_string('confirm',$book);?></legend>
		<p><?php print_string('confidentwhatyouaredoing',$book);?></p>
		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		</div>
	</fieldset>

<?php
foreach($comids as $comid){
?>
	<input type="hidden" name="comids[]" value="<?php print $comid; ?>" />
<?php
	}
?>
	<input type="hidden" name="newcomtype" value="<?php print $newcomtype; ?>" />
	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
  </form> 
</div>
<?php

	}

?>
