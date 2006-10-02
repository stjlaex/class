<?php 
/**													class_edit.php
 */

$action='class_edit_action.php';
$cancel='teacher_matrix.php';

if(isset($_GET['newcid'])){$newcid=$_GET['newcid'];}
if(isset($_GET['newtid'])){$newtid=$_GET['newtid'];}else{$newtid='';}
if(isset($_POST['newcid'])){$newcid=$_POST['newcid'];}
if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}

$d_class=mysql_query("SELECT * FROM class WHERE id='$newcid'");
$class=mysql_fetch_array($d_class, MYSQL_ASSOC);
$crid=$class['course_id'];
$bid=$class['subject_id'];
$stage=$class['stage'];
/*keeping things simple by fixing season to a single value*/
/*to sophisticate in the future*/
$currentseason='S';
$currentyear=getCurriculumYear($crid);

$extrabuttons['unassignclass']=array('name'=>'sub','value'=>'Unassign');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

<?php

	/*Fetch students in this cohort.*/
	$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid JOIN
		cohort ON cohidcomid.cohort_id=cohort.id WHERE 
		cohort.course_id='$crid' AND cohort.year='$currentyear' AND
		cohort.season='$currentseason' AND cohort.stage='$stage'");
	$firstit=0;
	while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
		$comid=$cohidcomid['community_id'];
		if($firstit==0){mysql_query("CREATE TEMPORARY TABLE cohortstudents
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid' AND
			b.id=a.student_id  AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");}
		else{mysql_query("INSERT INTO cohortstudents SELECT
				a.student_id, b.surname, b.forename, b.middlenames, 
				b.form_id FROM comidsid a,
				student b WHERE a.community_id='$comid' AND b.id=a.student_id
				AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)");}
		$firstit++;
		}
	/*Fetch students already in classes for this subject.*/
	$d_class=mysql_query("SELECT id FROM class WHERE
		course_id='$crid' AND subject_id='$bid' AND stage='$stage'");
	$firstit=0;
	while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
		$cid=$class['id'];
		if($firstit==0){mysql_query("CREATE TEMPORARY TABLE subjectstudents
			(SELECT a.student_id, b.surname, b.forename,
			b.middlenames, b.form_id, a.class_id FROM
			cidsid a, student b WHERE a.class_id='$cid' AND
			b.id=a.student_id ORDER BY b.surname)");}
		else{mysql_query("INSERT INTO subjectstudents SELECT
			a.student_id, b.surname, b.forename, b.middlenames, 
				b.form_id, a.class_id FROM cidsid a,
			student b WHERE a.class_id='$cid' AND b.id=a.student_id ORDER
			BY b.surname");}
		$firstit++;
		}
?>

	  <div style="float:left;width:33%;">
		<table class="listmenu">
		<caption><?php print_string('currentclassfor',$book);?>: <?php print $bid;?></caption>
		<tr>
		  <th><?php print $newcid.'/'.$newtid; ?></th>
			<td>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
				<?php print_string('checkall'); ?>
			</td>
		</tr>
<?php
	/*students already in this class*/
	$d_student=mysql_query("SELECT a.student_id, b.surname, b.middlenames,
				b.forename, b.yeargroup_id, b.form_id FROM cidsid a, student b 
				WHERE a.class_id='$newcid' AND b.id=a.student_id ORDER BY b.surname");
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		print '<tr><td>'.$student['surname']. 
				', '.$student['forename'].' ('.$student['form_id'].')</td>';
		print '<td><input type="checkbox" name="'.$student['student_id'].'" /></td>';
		print '</tr>';
		}
?>
		</table>
	  </div>

		<div  style="float:right;width:66%;">
		<fieldset>
		  <legend><?php print_string('choosestudentstoadd',$book);?></legend>

<?php
	/*list those not assigned already in this subject*/
  	$d_student=mysql_query("SELECT a.student_id, a.forename, a.middlenames,
					a.surname, a.form_id FROM
					cohortstudents AS a LEFT JOIN subjectstudents AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL 
					ORDER BY a.surname");
?>
		<div class="left">
		  <label><?php print_string('studentsnotinsubject',$book);?></label>
		  <select name="newsid[]" size="20" multiple="multiple" style="width:98%;">	
<?php
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print	'value="'.$student['student_id'].'">'.$student['surname'].',
  	'.$student['forename'].' '.$student['middlenames'].' ('.$student['form_id'].')</option>';
			}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyinsubject',$book);?></label>
		  <select name="newsid[]" size="20" multiple="multiple" style="width:98%;">	
<?php
		/*all those assigned already in this subject and yeargroup*/
		$d_student=mysql_query("SELECT student_id, forename, middlenames,
					surname, form_id FROM subjectstudents ORDER BY surname"); 
		while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
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

	<input type="hidden" name="newcid" value="<?php print $newcid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
