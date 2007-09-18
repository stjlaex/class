<?php	
/**	   							 fetch_attendance.php
 *
 */	

/* Returns a blank attendance record (even for sid!=-1!)*/
function fetchAttendance($sid='-1'){
	$Attendance=array();
	$Attendance['id_db']=$attendance['id'];
	$Attendance['Date']=array('label' => 'date', 
							  'inputtype'=> 'required',
							  'table_db' => 'event', 
							  'field_db' => 'date',
							  'type_db' => 'date', 
							  'value' => ''.$attendance['date']);
	$Attendance['Period']=array('label' => 'period',
							  'inputtype' => 'required',
							  'table_db' => 'event', 
							  'field_db' => 'period',
							  'type_db' => 'enum', 
								'value' => ''.$attendance['period']);
	$Attendance['Status']=array('label' => 'attendance',
							  'inputtype'=> 'required',
							  'table_db' => 'attendance', 
							  'field_db' => 'status',
							  'type_db' => 'enum', 
							  'value' => ''.$attendance['status']);
	$Attendance['Code']=array('label' => 'code',
							  'inputtype'=> 'required',
							  'table_db' => 'attendance', 
							  'field_db' => 'code',
							  'type_db' => 'enum', 
							  'value' => ''.$attendance['code']);
	$Attendance['Late']=array('label' => 'late',
							  'table_db' => 'attendance', 
							  'field_db' => 'code',
							  'type_db' => 'enum', 
							  'value' => ''.$attendance['late']);
	$Attendance['Comment']=array('label' => 'comment',
							  'table_db' => 'attendance', 
							  'field_db' => 'comment',
							  'type_db' => 'text', 
							  'value' => ''.$attendance['comment']);
	$Attendance['Logtime']=array('label' => 'time',
							  'table_db' => 'attendance', 
							  'field_db' => 'logtime',
							  'type_db' => 'text', 
							  'value' => ''.$attendance['logtime']);
	$Attendance['Teacher']=array('label' => 'teacher',
							  'table_db' => 'attendance', 
							  'field_db' => 'teacher_id',
							  'type_db' => 'varchar(14)', 
							  'value' => ''.$attendance['teacer_id']);
	return $Attendance;
	}


/* Returns all attendance records for the $nodays before the day */
/* specified by $startday (and $startday=0 is today)*/
function fetchAttendances($sid,$startday=0,$nodays=7){
	$Attendances=array();
	$evetable=array();
	/*defaults to choose this past week*/
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday+1,date('Y')));
	$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-$nodays,date('Y')));

	$d_attendance=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(attendance.logtime) AS logtime, event.id,
			event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.date < '$startdate' 
			AND event.date > '$enddate' 
			ORDER BY event.date, event.period");
	$index=0;
	$Attendances['Attendance']=array();
	while($attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC)){
		$Attendance=array();
		$Attendance['id_db']=$attendance['id'];
	   	$Attendance['Period']=array('label' => 'period',
								  'value' => ''.$attendance['period']);
	   	$Attendance['Date']=array('label' => 'date', 
									'type_db'=>'date', 
									'value' => ''.$attendance['date']);
	   	$Attendance['Status']=array('label' => 'attendance',
								  'value' => ''.$attendance['status']);
	   	$Attendance['Code']=array('label' => 'code',
								  'value' => ''.$attendance['code']);
	   	$Attendance['Late']=array('label' => 'late',
								  'value' => ''.$attendance['late']);
	   	$Attendance['Comment']=array('label' => 'comment',
								  'value' => ''.$attendance['comment']);
	   	$Attendance['Logtime']=array('label' => 'time',
								  'value' => ''.$attendance['logtime']);
		$Attendances['Attendance'][]=$Attendance;
		$evetable[$attendance['id']]=$index++;
		}

	$Attendances['evetable']=$evetable;
	return nullCorrect($Attendances);
	}

function fetchcurrentAttendance($sid,$eveid=''){
	if($eveid==''){
		$event=currentEvent();
		$eveid=$event['id'];
		}
	$Attendance=array();
	if($eveid!=''){
		$d_attendance=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(logtime) AS logtime, event.id,
			event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.id='$eveid'");

		$attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC);
		$Attendance['id_db']=$attendance['id'];
		$Attendance['Period']=array('label' => 'period',
								'value' => ''.$attendance['period']);
	   	$Attendance['Date']=array('label' => 'date', 
									'value' => ''.$attendance['date']);
	   	$Attendance['Status']=array('label' => 'attendance',
								  'value' => ''.$attendance['status']);
	   	$Attendance['Code']=array('label' => 'code',
								  'value' => ''.$attendance['code']);
	   	$Attendance['Late']=array('label' => 'late',
								  'value' => ''.$attendance['late']);
	   	$Attendance['Comment']=array('label' => 'comment',
								  'value' => ''.$attendance['comment']);
	   	$Attendance['Logtime']=array('label' => 'time',
								  'value' => ''.$attendance['logtime']);
		}
	return nullCorrect($Attendance);
	}

function fetchAttendanceEvent($eveid='-1'){
	$Event=array();
	$d_event=mysql_query("SELECT period, date FROM event WHERE id='$eveid'");
	$event=mysql_fetch_array($d_event,MYSQL_ASSOC);

	$Event=array();
	$Event['id_db']=$eveid;
	$Event['Period']=array('label' => 'period',
						   'value' => ''.$event['period']);
	$Event['Date']=array('label' => 'date', 
						 'value' => ''.$event['date']);
	return nullCorrect($Event);
	}

/* returns all events which exist in the db inclusive of the period */
/* from startday to the previous nodays */
function fetchAttendanceEvents($startday=0,$nodays=7){
	$AttendanceEvents=array();
	$evetable=array();
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday+1,date('Y')));
	$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-$nodays,date('Y')));
	$d_event=mysql_query("SELECT id, period, date FROM event WHERE date < '$startdate' 
			AND date > '$enddate'  ORDER BY date, period");
	$AttendanceEvents['Event']=array();
	$index=0;
	while($event=mysql_fetch_array($d_event,MYSQL_ASSOC)){
		$Event=array();
		$Event['id_db']=$event['id'];
	   	$Event['Period']=array('label' => 'period',
							   'value' => ''.$event['period']);
	   	$Event['Date']=array('label' => 'date', 
							 'type_db'=>'date', 
							 'value' => ''.$event['date']);
		$AttendanceEvents['Event'][]=$Event;
		$evetable[$event['id']]=$index++;
		}
	$AttendanceEvents['evetable']=$evetable;

	return nullCorrect($AttendanceEvents);
	}

function currentEvent(){
	global $CFG;
	if($CFG->registration=='double'){$session=date('A');}
	else{$session='AM';}
	$date=date('Y-m-d');
	$d_event=mysql_query("SELECT id FROM event WHERE date='$date' AND period='$session'");
	if(mysql_num_rows($d_event)==0){
		$eveid='0';
		}
	else{
		$eveid=mysql_result($d_event,0);
		}
	$event=array('id'=>$eveid,'date'=>$date,'period'=>$session);
	return $event;
	}

function fetchEvent($eveid){
	$d_event=mysql_query("SELECT id,date,period FROM event WHERE id='$eveid'");
	if(mysql_num_rows($d_event)==0){
		$eveid='0';
		$event=array('id'=>$eveid,'date'=>'','period'=>'');
		}
	else{
		$event=mysql_fetch_array($d_event,MYSQL_ASSOC);
		}
	return $event;
	}

function check_communityAttendance($community,$eveid=''){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	$nosids=countin_community($community);
	$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid'  
							 AND (comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND attendance.event_id='$eveid' AND attendance.status='a'");
	$noa=mysql_result($d_att,0);
	$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid' 
							 AND (comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND attendance.event_id='$eveid' AND attendance.status='p'");
	$nop=mysql_result($d_att,0);

	$results=array($nosids,$nop,$noa);
	return $results;
	}

function list_absentStudents($eveid=''){
	if($eveid==''){
		$event=currentEvent();
		$eveid=$event['id'];
		}
	$Students['Student']=array();
	if($eveid!=''){
		$d_attendance=mysql_query("SELECT student.id AS sid, attendance.status, 
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(attendance.logtime) AS logtime
			FROM attendance JOIN student ON student.id=attendance.student_id WHERE
			attendance.student_id=student.id AND
			attendance.event_id='$eveid' AND attendance.status='a'");

		$Attendance=array();
		$Student=array();
		while($attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC)){
			$Attendance['id_db']=$eveid;
			$Attendance['Status']=array('label' => 'attendance',
								  'value' => ''.$attendance['status']);
			$Attendance['Code']=array('label' => 'code',
								  'value' => ''.$attendance['code']);
			$Attendance['Late']=array('label' => 'late',
								  'value' => ''.$attendance['late']);
			$Attendance['Comment']=array('label' => 'comment',
								  'value' => ''.$attendance['comment']);
			$Attendance['Logtime']=array('label' => 'time',
								  'value' => ''.$attendance['logtime']);
			$Student['id_db']=$attendance['sid'];
			$Student['Attendance']=$Attendance;
			$Students['Student'][]=$Student;
			}
		}
	return nullCorrect($Students);
	}
?>
