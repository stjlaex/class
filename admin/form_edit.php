<?php 
/**											   form_edit.php
 *
 * Manage the list of students belonging to a form.
 *
 */

$action='form_edit_action.php';
$cancel='formgroup_matrix.php';
$choice='formgroup_matrix.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}

	$community=get_community($comid);
	$fid=$community['name'];
	/*Check user has permission to edit*/
	$perm=getFormPerm($fid);
	$neededperm='r';
	include('scripts/perm_action.php');

	$yid=get_form_yeargroup($fid);
	$year=get_yeargroupname($yid);
	$tutor_users=(array)list_community_users($community,array('r'=>1,'w'=>1,'x'=>1));

	$extrabuttons['renamegroup']=array('name'=>'current','value'=>'community_group_rename.php');
	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

		<div style="width:33%;float:left;" id="viewcontent">
		<table class="listmenu" id="sidtable">
		<caption>
		<?php print $year;?>
		<?php print_string('formgroup');?>
		</caption>
		  <tr>
			<th colspan="3">
			  <h2>
		<?php print $fid.' &nbsp;&nbsp;';?>
<?php 
		  foreach($tutor_users as $tutor_user){
			  print $tutor_user['forename'][0].' '. $tutor_user['surname'];
			  emaillink_display($tutor_user['email']);
			  }
?>
			  </h2>
			</th>
			<th>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		<?php print_string('checkall'); ?>
			</th>
		  </tr>
<?php
	$students=(array)listin_community($community);
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];
		if($_SESSION['role']=='admin' or $_SESSION['role']=='office'){
			$Enrolment=fetchEnrolment($sid);
			$extra=$Enrolment['EnrolNumber']['value'];
			}
		else{$extra='&nbsp;';}
		
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.$rown++.'</td>';
		print '<td>'.$student['surname']. ', '.$student['forename'].' '.$student['preferredforename'].'</td><td>'.$extra.'</td>';
		print '<td><input type="checkbox" name="sids[]" value="'.$sid.'" /></td>';
		print '</tr>';
		}
?>
		</table>
	  </div>

	  <div style="width:67%;float:right;">
		<fieldset class="center">
		<legend><?php print_string('reflectthesechangesinsubjectclasses',$book);?></legend>
		  <div class="left">
			<label><?php print_string('yes');?></label>
			<input type="radio" name="classestoo"  value="yes" checked />
		  </div>

		  <div class="right">
		  <label><?php print_string('no');?></label>
			<input type="radio" name="classestoo"  value="no" />
		  </div>
		</fieldset>

		<fieldset class="center">
		<legend><?php print_string('choosestudentstoadd',$book);?></legend>
		<div class="left">
		  <label><?php print_string('studentsnotinaform',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
   	$d_student=mysql_query("SELECT id, surname, forename, preferredforename, form_id FROM
			student WHERE yeargroup_id LIKE '$yid' AND (form_id='' OR
				form_id IS NULL) ORDER BY surname");
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print  ' value="'.$student['id'].'">'.$student['surname'].', 
				'.$student['forename'].' '.$student['preferredforename'].' ('.$student['form_id'].')</option>';
			}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyinaform',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">	
<?php
  	$d_student=mysql_query("SELECT id, forename,
					surname, preferredforename, form_id FROM student WHERE
					yeargroup_id LIKE '$yid' AND form_id!='' ORDER BY surname"); 
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
			print '<option ';
			print	' value="'.$student['id'].'">'.$student['surname']. 
					', '.$student['forename'].' '.$student['preferredforename'].' ('.$student['form_id'].')</option>';
			}
?>		
		  </select>
		</div>
		</fieldset>
	  </div>

	<input type="hidden" name="newcomtype" value="form" />
	<input type="hidden" name="comid" value="<?php print $comid;?>" />
	<input type="hidden" name="name" value="<?php print $fid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
