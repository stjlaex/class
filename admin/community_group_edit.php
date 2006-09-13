<?php 
/**											  community_group_edit.php
 */

$action='community_group_edit_action.php';
$cancel='community_group.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_GET['newcomtype'])){$newcomtype=$_GET['newcomtype'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}

	$d_com=mysql_query("SELECT name FROM community WHERE id='$comid'");
	$comname=mysql_result($d_com,0);
	$currentcommunity=array('type'=>$newcomtype,'id'=>$comid);

	if($newcomid!=''){$newcommunity=array('id'=>$newcomid);}
	else{$newcommunity=array('type'=>'year','name'=>'');}

	$oldstudents=listinCommunity($currentcommunity);
	$newstudents=listin_unionCommunities($currentcommunity,$newcommunity);

	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:33%;float:left;">
		<table class="listmenu">
		  <caption>
			<?php
			$description=displayEnum($newcomtype,'community_type');
			print_string($description,$book);
			?>
		  </caption>
		  <tr>
		  <th><?php print $comname;?></th>
			<th><?php print_string('remove');?></th>
		  </tr>
<?php
	while(list($sid,$student)=each($oldstudents)){
		print '<tr><td>'.$student['surname']. 
				', '.$student['forename']. ' ('.$student['form_id'].')</td>';
		print '<td><input type="checkbox" name="oldsids[]" value="'.$student['id'].'" /></td>';
		print '</tr>';
		}
?>
		</table>
	  </div>

	  <div style="width:67%;float:right;">
		<fieldset class="center">
		<legend><?php print_string('changegroup',$book);?></legend>
		  <div class="center">
<?php
			$onchange='yes';$required='no';$type='year';$multi='1';
			$selcomids=array($newcomid);
			include('scripts/list_community.php');
?>
		  </div>
		</fieldset>

		<fieldset class="center">
		<legend><?php print_string('choosestudentstoadd',$book);?></legend>
		<div class="left">
		  <label><?php print_string('studentsnotin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	while(list($index,$student)=each($newstudents['scab'])){
		print '<option ';
		print	'value="'.$student['student_id'].'">'. 
		$student['surname'].', '.$student['forename'].' '. 
		$student['middlenames'].' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	while(list($index,$student)=each($newstudents['union'])){
		print '<option ';
		print	'value="'.$student['student_id'].'">'. 
		$student['surname'].', '.$student['forename'].' '. 
		$student['middlenames'].' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>
		</fieldset>
	  </div>
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="newcomid" value="<?php print $newcomid;?>" /> 
	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>