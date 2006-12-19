<?php 
/** 		   						register_list_action.php
 */

$action='register_list.php';

$checkeveid=$_POST['checkeveid'];

include('scripts/sub_action.php');

if($sub=='Previous'){
	$startday=$startday-7;
	}
elseif($sub=='Next'){
	$startday=$startday+7;
	if($startday>=0){$startday='';}
	}
elseif($sub=='Submit'){

	if($checkeveid==0){
		$date=$_POST['date'];
		$period=$_POST['period'];
		$d_event=mysql_query("SELECT id FROM event
				WHERE date='$date' AND period='$period'");
		if(mysql_num_rows($d_event)==0){
			mysql_query("INSERT INTO event (date,period) 
					VALUES ('$date','$period');");
			$eveid=mysql_insert_id();
			}
		else{
			$eveid=mysql_result($d_event,0);
			}
		$checkeveid=$eveid;
		}
	else{$eveid=$checkeveid;}

	$students=(array)listinCommunity($community);
	while(list($index,$student)=each($students)){
		$instatus='';
		$sid=$student['id'];
		if(isset($_POST['status-' .$sid])){
			$instatus=$_POST['status-' .$sid];
			if($instatus!='n'){
				if($instatus=='a'){
					$incode=$_POST['code-'.$sid];
					$incomm=clean_text($_POST['comm-'.$sid]);
					$inlate='';
					}
				else{
					$inlate=$_POST['late-'.$sid];
					$incode='';
					$incomm='';
					}

				$d_attendance=mysql_query("SELECT status FROM attendance
				WHERE student_id='$sid' AND event_id='$eveid'");
				if(mysql_num_rows($d_attendance)==0){
					mysql_query("INSERT INTO attendance (event_id,
					student_id, status, code, late, comment, teacher_id) 
					VALUES ('$eveid','$sid','$instatus','$incode','$inlate','$incomm','$tid');");
					}
				else{
					mysql_query("UPDATE attendance SET status='$instatus',
							code='$incode', late='$inlate',
							comment='$incomm', teacher_id='tid' WHERE
							event_id='$eveid' AND student_id='$sid'");
					}
				}
			}
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>