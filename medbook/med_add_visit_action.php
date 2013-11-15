<?php
/**                                  med_add_visit_action.php
 */
if(isset($_POST['studentid']) and $_POST['studentid']!=''){$sid=$_POST['studentid'];}else{$sid='';}
if(isset($_POST['date0']) and $_POST['date0']!=''){$date=$_POST['date0'];}else{$date='';}
if(isset($_POST['time']) and $_POST['time']!=''){$time=$_POST['time'];}else{$time='';}
if(isset($_POST['category']) and $_POST['category']!=''){$category=$_POST['category'];}else{$category='';}
if(isset($_POST['detail']) and $_POST['detail']!=''){$details=$_POST['detail'];}else{$details='';}

if(isset($_POST['visitaction']) and $_POST['visitaction']!=''){$visitaction=$_POST['visitaction'];}else{$visitaction='';}
if(isset($_POST['visitid']) and $_POST['visitid']!=''){$entid=$_POST['visitid'];}else{$entid='';}


if($details!='' and $date!='' and $visitaction!='update'){
	if(mysql_query("INSERT INTO medical_log
				(student_id,details,category,date,time,user_id) 
				VALUES ('$sid','$details','$category','$date','$time','$tid');")){
		$result[]='Added!';
		}
	else{$result[]='Not added!';}
	}
if($details!='' and $date!='' and $visitaction=='update' and $entid!=''){
	if(mysql_query("UPDATE medical_log SET details='$details',category='$category',date='$date',time='$time',user_id='$tid' WHERE id='$entid';")){
		$result[]='Entry updated!';
		}
	else{$result[]='Not updated!';}
	}

$action='med_view_visits.php';

include('scripts/results.php');
include('scripts/redirect.php');

?>
