<?php 
/**													class_edit.php
 */

$action='class_edit_action.php';
$cancel='teacher_matrix.php';

if(isset($_GET['newcid'])){$newcid=$_GET['newcid'];}
if(isset($_GET['newtid'])){$newtid=$_GET['newtid'];}else{$newtid='';}
if(isset($_POST['newcid'])){$newcid=$_POST['newcid'];}
if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}

$class=get_this_class($newcid);
$crid=$class['crid'];
$bid=$class['bid'];
$stage=$class['stage'];
$detail=$class['detail'];
$d_c=mysql_query("SELECT description FROM classes 
						WHERE course_id='$crid' AND subject_id='$bid' AND stage='$stage';");
$description=mysql_result($d_c,0);
/*keeping things simple by fixing season to a single value*/
/*to sophisticate in the future*/
$currentseason='S';
$currentyear=get_curriculumyear($crid);

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
			b.middlenames, b.preferredforename, b.form_id FROM
			comidsid a, student b WHERE a.community_id='$comid' AND
			b.id=a.student_id  AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL))");}
		else{mysql_query("INSERT INTO cohortstudents SELECT
				a.student_id, b.surname, b.forename, b.middlenames, b.preferredforename, 
				b.form_id FROM comidsid a,
				student b WHERE a.community_id='$comid' AND b.id=a.student_id
				AND (a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)");}
		$firstit++;
		}

	/*Fetch students already in classes for this subject.*/
	$classes=list_course_classes($crid,$bid,$stage,$currentyear);
	$firstit=0;
	foreach($classes as $otherclass){
		$cid=$otherclass['id'];
		if($firstit==0){mysql_query("CREATE TEMPORARY TABLE subjectstudents
			(SELECT a.student_id, b.surname, b.forename, 
			b.middlenames, b.preferredforename, b.form_id, a.class_id FROM
			cidsid a, student b WHERE a.class_id='$cid' AND
			b.id=a.student_id ORDER BY b.surname)");}
		else{mysql_query("INSERT INTO subjectstudents SELECT
			a.student_id, b.surname, b.forename, b.middlenames, b.preferredforename, 
				b.form_id, a.class_id FROM cidsid a,
			student b WHERE a.class_id='$cid' AND b.id=a.student_id ORDER
			BY b.surname");}
		$firstit++;
		}
?>

	  <div style="float:left;width:33%;"  id="viewcontent">
		<table class="listmenu">
		<caption><?php print_string('currentclassfor',$book);?>: <?php print $bid;?></caption>
		<tr>
		  <th colspan="3"><?php print $class['name'].'/'.$newtid; ?></th>
			<td>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
				<?php print_string('checkall'); ?>
			</td>
		</tr>
<?php
	/*students already in this class*/
	$d_student=mysql_query("SELECT a.student_id, b.surname,
				b.middlenames, b.preferredforename,
				b.forename, b.yeargroup_id, b.form_id FROM cidsid a, student b 
				WHERE a.class_id='$newcid' AND b.id=a.student_id ORDER BY b.surname");
	$rown=1;
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		$sid=$student['student_id'];
		$Student=(array)fetchStudent_short($sid);
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.$rown++.'</td>';
		print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'&sids[]='.$sid.'">'. $Student['DisplayFullSurname']['value'].'</a></td><td>'.$student['form_id'].'</td>';
		print '<td><input type="checkbox" name="'.$sid.'" /></td>';
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
					a.surname, a.preferredforename, a.form_id FROM
					cohortstudents AS a LEFT JOIN subjectstudents AS b ON
					a.student_id=b.student_id WHERE
					b.student_id IS NULL 
					ORDER BY a.surname");
?>
		<div class="left">
		  <label><?php print_string('studentsnotinsubject',$book);?></label>
			<select name="newsid[]" size="20" 	
			  tabindex="<?php print $tab++;?>"
			  multiple="multiple" style="width:98%;">	
<?php
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print 'value="'.$student['student_id'].'">'.$student['surname']. 
			', '.$student['forename'].' '.$student['middlenames']. 
			' '.$student['preferredforename'].' ('.$student['form_id'].')</option>';
			}
?>
		  </select>
		</div>

		<div class="right">
		  <label><?php print_string('studentsalreadyinsubject',$book);?></label>
			<select name="newsid[]" size="20" 
			  tabindex="<?php print $tab++;?>"
			  multiple="multiple" style="width:98%;">	
<?php
		/*all those assigned already in this subject and yeargroup*/
		$d_student=mysql_query("SELECT student_id, forename, middlenames,
					surname, preferredforename, form_id FROM subjectstudents ORDER BY surname"); 
		while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print	'value="'.$student['student_id'].'">'. 
				$student['surname'].', '.$student['forename'].' '. 
					$student['middlenames'].' '.$student['preferredforename'].' ('.$student['form_id'].')</option>';
			}
?>

		  </select>
		</div>

	  </fieldset>
	  </div>
	  <div style="float:right;width:66%;">
		<fieldset>
		  <legend>
			<?php print get_string('notesonclass',$book);?>
		  </legend>
		  <input name="detail" maxlength="240" tabindex="<?php print $tab++;?>" value="<?php print $detail;?>"/>
		</fieldset>
	  </div>
	  <div style="float:right;width:66%;">
		<fieldset>
		  <legend>
			<?php print get_string('coursecurriculum',$book);?>
		  </legend>
		  <textarea  tabindex="<?php print $tab++;?>" name="description" class="htmleditorarea" 
					 id="Description" rows="3" cols="35"><?php print $description;?></textarea>
		</fieldset>
	  </div>


	<input type="hidden" name="newcid" value="<?php print $newcid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
<script src="lib/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="lib/tiny_mce/loadeditor.js" type="text/javascript"></script>
<script type="text/javascript">loadEditor();</script>
