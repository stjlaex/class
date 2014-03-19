<?php 
/**											  community_group_edit.php
 */

$action='community_group_edit_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_GET['yid'])){$yid=$_GET['yid'];}else{$yid='%';}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}
if(isset($_GET['newcomtype'])){$newcomtype=$_GET['newcomtype'];}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}

	$d_com=mysql_query("SELECT name FROM community WHERE id='$comid';");
	$comname=mysql_result($d_com,0);
	$currentcommunity=array('yeargroup_id'=>$yid,'type'=>$newcomtype,'id'=>$comid);


	/* This sets the group to select students from for adding. */
	if($newcomid!=''){$newcommunity=array('id'=>$newcomid);}
	elseif($yid!='%'){$newcommunity=array('type'=>'year','name'=>$yid);}
	else{$newcommunity=array('type'=>'year','name'=>'');}

	$oldstudents=listin_community($currentcommunity);
	$newstudents=listin_both_communities($currentcommunity,$newcommunity);
	$description=displayEnum($newcomtype,'community_type');

$extrabuttons['edit']=array('name'=>'current','value'=>'community_group_rename.php');
three_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<h4><?php print_string($description,$book);?></label><?php print $comname;?></h4>
  </div>
 <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <div id="viewcontent">
	      <fieldset class="divgroup">
		<h5><?php print_string($description,$book);?></h5>
		<table class="listmenu">
		  <tr>
			<th colspan="3"><h6><?php print $comname;?></h6></th>
			<th>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			  <?php print_string('checkall'); ?>
			</th>
		  </tr>
<?php
	$no=1;
	foreach($oldstudents as $student){
		$sid=$student['id'];
		$Student=(array)fetchStudent_short($sid);
		print '<tr><td>'.$no++.'</td>';
		print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_transport.php&sid='.$sid.'">'. $Student['DisplayFullSurname']['value'].'</a></td>';
		print '<td>'.$student['form_id'].'</td>';
		print '<td><input type="checkbox" name="oldsids[]" value="'.$sid.'" /></td>';
		print '</tr>';
		}
?>
		</table>
		</fieldset>
	  </div>

	  <div >
		<fieldset class="divgroup">
		<h5><?php print_string('changegroup',$book);?></h5>
		  <div class="center">
<?php
			$onchange='yes';$required='no';$multi='1';
			$type=$newcomtype;$selcomids=array($newcomid);
			include('scripts/list_community.php');
?>
		  </div>
		</fieldset>

		<fieldset class="divgroup">
		<h5><?php print_string('choosestudentstoadd',$book);?></h5>
		<div class="left">
		  <label><?php print_string('studentsnotin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	foreach($newstudents['complement'] as $student){
		print '<option value="'.$student['student_id'].'">'. 
			$student['surname'].', '.$student['forename'].' '. 
			$student['middlenames'].' '.$student['preferredforename']. 
			' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	foreach($newstudents['intersection'] as $student){
		print '<option value="'.$student['student_id'].'">'. 
			$student['surname'].', '.$student['forename'].' '. 
			$student['middlenames'].' '.$student['preferredforename']. 
			' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>
		</fieldset>
	  </div>

	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="yid" value="<?php print $yid;?>" /> 
	<input type="hidden" name="newcomid" value="<?php print $newcomid;?>" /> 
	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
