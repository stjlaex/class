<?php 
/**									   		form_edit_action.php
 */

$action='form_edit.php';

if(isset($_POST{'fid'})){$fid=$_POST{'fid'};}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}
if(isset($_POST{'newsids'})){$newsids=(array)$_POST{'newsids'};}
else{$newsids=array();}
if(isset($_POST{'oldsids'})){$oldsids=(array)$_POST{'oldsids'};}
else{$oldsids=array();}
if(isset($_POST{'classestoo'})){$classestoo=$_POST{'classestoo'};}

include('scripts/sub_action.php');

if($sub=='Unassign'){
   	if(mysql_query("UPDATE form SET teacher_id='' WHERE 
		teacher_id='$newtid' AND id='$fid'")){
   			}
   	else{$error[]=mysql_error();}
    $action=$cancel;
	}

elseif($sub=='Submit'){
	$todate=date("Y-m-d");
    $changecids=array();
	$changecids=formsClasses($fid);
	$comid=updateCommunity(array('type'=>'form','name'=>$fid));

	/*sids to remove*/
   	while(list($index,$sid)=each($oldsids)){
		mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");
		mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$comid' AND student_id='$sid'");
		if($classestoo='yes'){
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				$result[]='Added '.$sid.' to ' .$cid;
				}
			}
		}

	/*sids to add*/
   	while(list($index,$sid)=each($newsids)){
	   	$d_student=mysql_query("SELECT form_id FROM student WHERE id='$sid'");
		$student=mysql_fetch_array($d_student, MYSQL_ASSOC);
   		if($student['form_id']!='' and $fid!=$student['form_id']){
			/*first remove sid from their old form*/
			$oldcomid=updateCommunity(array('type'=>'form','name'=>$student['form_id']));
			mysql_query("UPDATE student SET form_id='' WHERE id='$sid'");
			mysql_query("UPDATE comidsid SET leavingdate='$todate' WHERE
							community_id='$oldcomid' AND student_id='$sid'");
			if($classestoo='yes'){
				$otherchangecids=array();
				$otherchangecids=formsClasses($student['form_id']);
				for($c=0;$c<sizeof($otherchangecids);$c++){
					$cid=$otherchangecids[$c];
					mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
					$result[]='Removed '.$sid.' to ' .$cid;
					}
				}
			}

   		if($fid!=$student['form_id']){
			/*now add to this form if not already in it*/
			mysql_query("UPDATE student SET
							form_id='$fid' WHERE id='$sid'");

			$d_comidsid=mysql_query("SELECT * FROM comidsid WHERE
				community_id='$comid' AND student_id='$sid'");	
			if(mysql_num_rows($d_comidsid)==0){
				mysql_query("INSERT INTO comidsid SET joiningdate='$todate',
							community_id='$comid', student_id='$sid'");
				}
			else{
				mysql_query("UPDATE comidsid SET leavingdate='' WHERE
							community_id='$oldcomid' AND student_id='$sid'");
				}
			if($classestoo='yes'){
		   		for($c=0;$c<sizeof($changecids);$c++){
		   			$cid=$changecids[$c];
		   			mysql_query("INSERT INTO cidsid
		   				(student_id, class_id) VALUES ('$sid', '$cid')");
					$result[]='Added '.$sid.' to ' .$cid;
		   			}
	   			}
			}
   		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
