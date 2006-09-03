<?php 
/**											   year_edit.php
 */
$action='yeargroup_edit_action.php';
$cancel='yeargroup_matrix.php';

if(isset($_GET{'newyid'})){$yid=$_GET{'newyid'};}
if(isset($_GET{'newtid'})){$newtid=$_GET{'newtid'};}else{$newtid='';}
if(isset($_POST{'yid'})){$yid=$_POST{'yid'};}
if(isset($_POST{'newcomid'})){$newcomid=$_POST{'newcomid'};}else{$newcomid='';}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}

	/*Check user has permission to edit*/
	$perm=getYearPerm($yid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	$d_year=mysql_query("SELECT * FROM yeargroup WHERE id='$yid'");
	$yeargroup=mysql_fetch_array($d_year, MYSQL_ASSOC);
	$yearcommunity=array('type'=>'year','name'=>$yid);

	if($newcomid!=''){$newcommunity=array('id'=>$newcomid);}
	else{$newcommunity=array('type'=>'year','name'=>'');}

	$oldstudents=listinCommunity($yearcommunity);
	$newstudents=listin_unionCommunities($yearcommunity,$newcommunity);

	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:33%;float:left;">
		<table class="listmenu">
		  <caption>
			<?php print_string('current');?>
			<?php print_string('yeargroup');?>
		  </caption>
		  <tr>
		  <th><?php print $yeargroup['name'];?></th>
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
	<input type="hidden" name="yid" value="<?php print $yid;?>" /> 
	<input type="hidden" name="name" value="<?php print $yid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="newyid" value="<?php print $newyid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
