<?php 
/**									   		form_edit_action.php
 */

$action='form_edit.php';

if(isset($_POST{'newfid'})){$newfid=$_POST{'newfid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}
if(isset($_POST{'newsid'})){$newsid=$_POST{'newsid'};}
if(isset($_POST{'classestoo'})){$classestoo=$_POST{'classestoo'};}

include('scripts/sub_action.php');

if($sub=='Unassign'){
   	if(mysql_query("UPDATE form  SET teacher_id='' WHERE 
		teacher_id='$newtid' AND id='$newfid'")){
   			$result[]='Unasigned the form from '.$newtid;	
   			}
   	else{$error[]=mysql_error();}
    $action=$cancel;	
	}

elseif($sub=='Submit'){
    $changecids=array();
	$changecids=formsClasses($newfid);
   	$d_student = mysql_query("SELECT id, surname,
		forename FROM student WHERE form_id='$newfid' ORDER BY surname");
   	while ($student = mysql_fetch_array($d_student, MYSQL_ASSOC)) {
			$sid = $student{'id'};
			if(isset($_POST{"$sid"})){
				if(mysql_query("UPDATE student SET form_id='' WHERE id='$sid'")){
					$result[]='Removed '.$student{'surname'};
					if($classestoo='yes'){
						for($c=0;$c<sizeof($changecids);$c++){
							$cid=$changecids[$c];
							mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
							}
			   			$result[]='Removed from this forms subject classes too.';
						}
					}
				else{$error[]=mysql_error();}
				}
			}
   	$c=0;
   	while(isset($newsid[$c])){
   		$sid = $newsid[$c];
	   	$d_student = mysql_query("SELECT surname, form_id FROM student WHERE id='$sid'");
		$student = mysql_fetch_array($d_student, MYSQL_ASSOC);
   		if($newfid!=$student['form_id']){
			mysql_query("UPDATE student SET
				form_id='$newfid' WHERE id='$sid'");
   			$result[]='Added '.$student{'surname'};
			if($classestoo='yes'){
		   		for($c=0;$c<sizeof($changecids);$c++){
		   			$cid=$changecids[$c];
		   			mysql_query("INSERT INTO cidsid
		   				(student_id, class_id) VALUES ('$sid', '$cid')");
		   			}
		   			$result[]='Added to this forms subject classes too.';
	   			}
			}
   		else{$error[]='Failed. Student already in this form!';}
   		$c++;
   		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
