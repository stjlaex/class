<?php 
/**										   		class_edit_action.php
 */

$action='class_edit.php';

if(isset($_POST{'newcid'})){$newcid=$_POST{'newcid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}
if(isset($_POST{'newsid'})){$newsid=$_POST{'newsid'};}
if(isset($_POST{'selyid'})){$newyid=$_POST{'selyid'};}

include('scripts/sub_action.php');

if($sub=='Unassign'){
   	if(mysql_query("DELETE FROM tidcid WHERE 
		teacher_id='$newtid' AND class_id='$newcid'")){
   			$result[]='Unasigned the class from '.$newtid;	
   			}
   	else{$error[]=mysql_error();}
    $action=$cancel;	
	}
elseif($sub=='Submit'){
   	$d_student = mysql_query("SELECT a.student_id, b.surname,
		b.forename FROM cidsid a, student b WHERE a.class_id='$newcid' 
		AND b.id=a.student_id ORDER BY b.surname");
   	while ($student = mysql_fetch_array($d_student, MYSQL_ASSOC)) {
			$sid = $student{'student_id'};
			if(isset($_POST{"$sid"})){
				if(mysql_query("DELETE FROM cidsid WHERE
					student_id='$sid' AND class_id='$newcid' LIMIT 1")){
					$result[]='Removed '.$student{'surname'};
					}
				else{$error[]=mysql_error();}
				}
			}
   	$c=0;
   	while (isset($newsid[$c])) {
   		$sid = $newsid[$c];
	   	$d_student = mysql_query("SELECT surname FROM student WHERE id='$sid'");
		$student = mysql_fetch_array($d_student, MYSQL_ASSOC);
   		if(mysql_query("INSERT INTO cidsid SET student_id='$sid',
			class_id='$newcid'")){
   			$result[]='Added '.$student{'surname'};
			}
   		else{$error[]='Failed. Student already in class!';}
   		$c++;
   		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
