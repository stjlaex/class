<?php 
/**													class_edit.php
 */
$action='class_edit_action.php';

if(isset($_GET{'newcid'})){$newcid=$_GET{'newcid'};}
if(isset($_GET{'newtid'})){$newtid=$_GET{'newtid'};}else{$newtid='';}
if(isset($_POST{'newcid'})){$newcid=$_POST{'newcid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}

$d_class = mysql_query("SELECT * FROM class WHERE id='$newcid'");
$class=mysql_fetch_array($d_class, MYSQL_ASSOC);
$yid=$class{'yeargroup_id'};
$bid=$class{'subject_id'};
if(isset($_POST{'newyid'})){$newyid=$_POST{'newyid'};}else{$newyid=$yid;}
$selyid=$newyid;

$extrabuttons['unassignclass']=array('name'=>'sub','value'=>'Unassign');
$extrabuttons['changeyeargroup']=array('name'=>'current','value'=>'class_edit.php');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:35%;float:left;">
	  <table class="listmenu">
		<caption><?php print_string('currentclassfor',$book);?><?php print $bid;?></caption>
		<tr>
		  <th><?php print $newcid.'/'.$newtid; ?></th>
		  <th><?php print_string('remove');?></th>
		</tr>
<?php
	$c=0;
	$d_student=mysql_query("SELECT a.student_id, b.surname, b.middlenames,
				b.forename, b.yeargroup_id, b.form_id FROM cidsid a, student b 
				WHERE a.class_id='$newcid' AND b.id=a.student_id ORDER BY b.surname");
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student{'student_id'};
		    print '<tr><td>'.$student['forename'].'
		    '.$student['surname'].' ('.$student['form_id'].')</td>';
		    print '<td><input type="checkbox" name="'.$sid.'" /></td>';
		    print '</tr>';
		    $c++;
			}
?>
	  </table>
	  </div>
<?php
	$d_class = mysql_query("SELECT * FROM class WHERE
		subject_id='$bid' AND yeargroup_id='$yid'");
	$i=0;
	while($class = mysql_fetch_array($d_class,MYSQL_ASSOC)){
		$cid = $class{'id'};

/*	Fetch students for these classes.  */
		if($i==0){mysql_query("CREATE TEMPORARY TABLE students
			(SELECT a.student_id, b.surname, b.forename,
		b.middlenames, b.form_id, a.class_id FROM
			cidsid a, student b WHERE a.class_id='$cid' AND
			b.id=a.student_id ORDER BY b.surname)");}
		else{mysql_query("INSERT INTO students SELECT
			a.student_id, b.surname, b.forename, b.middlenames, 
				b.form_id, a.class_id FROM cidsid a,
			student b WHERE a.class_id='$cid' AND b.id=a.student_id ORDER
			BY b.surname");}
		$i++;
		}

/*	Only select those not assigned already in this subject and yeargroup*/
  	$d_student=mysql_query("SELECT student.id, student.forename, student.middlenames,
					student.surname, student.form_id FROM student LEFT JOIN students ON
					student.id=students.student_id WHERE
					students.student_id IS NULL AND
					yeargroup_id='$newyid' ORDER BY student.form_id, student.surname"); 
?>
	  <div style="width:63%;float:right;">
		<fieldset class="left">
		  <legend><?php print_string('studentsnotinsubject',$book);?></legend>
		  <select name="newsid[]" size="20" multiple="multiple">	
<?php
	while($student = mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print	'value="'.$student['id'].'">'.$student['surname'].',
  	'.$student['forename'].' '.$student{'middlenames'}.' ('.$student['form_id'].')</option>';
			}
?>
		</select>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('studentsalreadyinsubject',$book);?></legend>
		<select name="newsid[]" size="20" multiple="multiple">	
<?php
/*	Select all those assigned already in this subject and yeargroup*/
  	$d_student = mysql_query("SELECT student_id, forename, middlenames,
					surname, form_id FROM students ORDER BY surname"); 
	while($student = mysql_fetch_array($d_student,MYSQL_ASSOC)) {
			print '<option ';
			print	'value="'.$student['student_id'].'">'. 
				$student['surname'].', '.$student['forename'].' '. 
					$student['middlenames'].' ('.$student['form_id'].')</option>';
			}
?>		
		</select>
	  </fieldset>
	  <fieldset class="right">
		<legend><?php print_string('yeargroup',$book);?></legend>
		<p><?php print_string('studentsfromotheryeargroup',$book);?>
		  <?php include('scripts/list_year.php'); ?>
		</p>
	  </fieldset>
</div>

	<input type="hidden" name="selyid" value="<?php print $selyid;?>" /> 
	<input type="hidden" name="newcid" value="<?php print $newcid;?>" /> 
	<input type="hidden" name="newtid" value="<?php print $newtid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
