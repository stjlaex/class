<?php 
/** 		   						register_list_action.php
 */

$action='register_list.php';
$action_post_vars=array('startday','checkeveid');

if(isset($_POST['checkeveid'])){$checkeveid=$_POST['checkeveid'];}
else{$checkeveid=0;}

include('scripts/sub_action.php');

if($sub=='Previous'){
	if($community['type']=='class'){
		$startday=$startday-1;
		}
	else{
		$startday=$startday-7;
		}
	}
elseif($sub=='Next'){
	if($community['type']=='class'){
		$startday=$startday+1;
		}
	else{
		$startday=$startday+7;
		}

	if($startday>=0){$startday='';}
	}
elseif($sub=='Submit'){


	$date=$_POST['date'];
	$session=$_POST['session'];
	if($checkeveid<0){
		/* This is for an event which is a teaching period. */
		$period=abs($checkeveid);
		$checkeveid=0;
		}
	else{
		$period=0;
		}

	//trigger_error($checkeveid.':: '.$date.' : '.$session.' : '.$period,E_USER_WARNING);

	if($checkeveid==0){
		/* This event was not in the db when first displayed. */
		$d_event=mysql_query("SELECT id FROM event WHERE date='$date' AND session='$session' 
										AND period='$period';");
		if(mysql_num_rows($d_event)==0){
			mysql_query("INSERT INTO event (date,session,period) VALUES ('$date','$session','$period');");
			$eveid=mysql_insert_id();
			}
		else{
			$eveid=mysql_result($d_event,0);
			}
		$checkeveid=$eveid;
		}
	else{$eveid=$checkeveid;}


	if($community['type']=='class'){
		$students=(array)listin_class($community['name'],true);
		}
	else{
		$students=(array)listin_community($community);
		}
	foreach($students as $student){
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

				$d_attendance=mysql_query("SELECT status, code, late, comment FROM attendance
												WHERE student_id='$sid' AND event_id='$eveid';");
				if(mysql_num_rows($d_attendance)==0){
					mysql_query("INSERT INTO attendance (event_id,
								student_id, status, code, late, comment, teacher_id, class_id) 
								VALUES ('$eveid','$sid','$instatus','$incode','$inlate','$incomm','$tid','$newcid');");
					}
				else{
					$att=mysql_fetch_array($d_attendance,MYSQL_ASSOC);
					if($att['status']!=$instatus or $att['code']!=$incode or 
					   $att['late']!=$inlate or $att['comment']!=$incomm){
						mysql_query("UPDATE attendance SET status='$instatus', code='$incode', 
									late='$inlate', comment='$incomm', teacher_id='$tid', class_id='$newcid' 
									WHERE event_id='$eveid' AND student_id='$sid';");
						}
					}
				}
			}
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>