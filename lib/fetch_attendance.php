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
		$event=get_event();
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

/* Given an event_id it returns the xml_array for the event.*/
/* An event does not neccessarily have to be an attendance event and */
/* this is therefore not really exclusive to attendnace but... */
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

/* Returns all events which exist in the db inclusive of the period */
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

/* Returns an event record for the matching date, if no date set then */
/* the default is to return the current session event.*/
function get_event($date='',$session=''){
	global $CFG;
	if($date==''){
		$date=date('Y-m-d');
		}
	if($session==''){
		if($CFG->registration=='double'){$session=date('A');}
		else{$session='AM';}
		}
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

function list_absentStudents($eveid='',$lates=0){
	if($eveid==''){
		$event=get_event();
		$eveid=$event['id'];
		}
	$Students['Student']=array();
	if($eveid!=''){
		$d_attendance=mysql_query("SELECT student.id AS sid, attendance.status, 
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(attendance.logtime) AS logtime
			FROM attendance JOIN student ON student.id=attendance.student_id WHERE
			attendance.student_id=student.id AND
			attendance.event_id='$eveid' AND attendance.status='a'
			ORDER BY student.yeargroup_id, student.form_id, student.surname");
		$Attendance=array();
		$Student=array();
		while($attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC)){
			/* Logical lates defaults to 0 and flags to filter out those
				students who are merely late (codes U and L), as these students
				will be on site and in classes. They will though still be
				counted in statistics as absent.*/
			if($attendance['code']!='U' and $attendance['code']!='L' and
			   $lates==0){
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
		}
	return nullCorrect($Students);
	}


/* */
function fetchAttendanceSummary($sid,$startdate,$endate){
	$Attendance['Summary']=array();

	$no_present=count_attendance($sid,$startdate,$endate);
	$no_late_authorised=count_attendance($sid,$startdate,$endate,'L');
	$no_late_unauthorised=count_attendance($sid,$startdate,$endate,'U');
	$no_late=$no_late_authorised+$no_late_unauthorised;
	$no_attended=$no_present+$no_late;

	$Attendance['Summary'][]=array('label' => 'attended',
								 'value' => ''.$no_attended);
	$Attendance['Summary'][]=array('label' => 'late',
								 'value' => ''.$no_late);

	$no_absent=count_attendance($sid,$startdate,$endate,'%') - $no_late;
	$no_ill=count_attendance($sid,$startdate,$endate,'I');
	$no_medical=count_attendance($sid,$startdate,$endate,'M');
	$no_notagreed=count_attendance($sid,$startdate,$endate,'G');
	$no_notexplained=count_attendance($sid,$startdate,$endate,'O');
	$no_noreason=count_attendance($sid,$startdate,$endate,'N');

	$no_unauthorised_absent=$no_ill+$no_medical+$no_notagreed+$no_noreason+$no_notexplained;
	$no_authorised_absent=$no_absent-$no_unauthorised_absent;

	$Attendance['Summary'][]=array('label' => 'authorisedabsent',
								 'value' => ''.$no_authorised_absent);
	$Attendance['Summary'][]=array('label' => 'unauthorisedabsent',
								 'value' => ''.$no_unauthorised_absent);

	return $Attendance;
	}

/* */
function count_attendance($sid,$startdate,$enddate,$code=''){

	if($code==''){
		$status='p';
		$code='%';
		}
	else{
		$status='a';
		}
	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND
			attendance.status='$status' AND attendance.code LIKE '$code' 
			AND event.date > '$startdate' AND event.date < '$enddate';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}
?>
