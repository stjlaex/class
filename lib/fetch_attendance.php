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


/**
 *
 * Returns all attendance records for the $nodays before the day
 * specified by $startday (and $startday=0 is today)
 *
 */
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


/**
 *
 */
function fetchcurrentAttendance($sid,$eveid=''){
	if($eveid==''){
		$secid=get_student_section($sid);
		$event=get_event('','',$secid);
		$eveid=$event['id'];
		}
	$Attendance=array();
	if($eveid!=''){
		$d_a=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(logtime) AS logtime, event.id,
			event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.id='$eveid';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);

		$Attendance['id_db']=$a['id'];
		$Attendance['Period']=array('label' => 'period',
								'value' => ''.$a['period']);
	   	$Attendance['Date']=array('label' => 'date', 
									'value' => ''.$a['date']);
	   	$Attendance['Status']=array('label' => 'attendance',
								  'value' => ''.$a['status']);
	   	$Attendance['Code']=array('label' => 'code',
								  'value' => ''.$a['code']);
	   	$Attendance['Late']=array('label' => 'late',
								  'value' => ''.$a['late']);
	   	$Attendance['Comment']=array('label' => 'comment',
								  'value' => ''.$a['comment']);
	   	$Attendance['Logtime']=array('label' => 'time',
								  'value' => ''.$a['logtime']);
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
function fetchAttendanceEvents($startday=0,$nodays=7,$period='%'){
	$AttendanceEvents=array();
	$evetable=array();
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday+1,date('Y')));
	$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-$nodays,date('Y')));
	$d_event=mysql_query("SELECT id, period, date FROM event WHERE date < '$startdate' 
			AND date > '$enddate' AND period LIKE '$period' ORDER BY date, period");
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

/**
 *
 * Returns an event record for the matching date, if no date set then 
 * the default is to return the current session event.
 *
 */
function get_event($date='',$session='',$secid=1){
	global $CFG;
	if($date==''){
		$date=date('Y-m-d');
		}
	if($session==''){
		$session='AM';

		/* secid=1 is wholeschool, use this if no others configured */
		if(!is_array($CFG->registration)){$reg='single';}
		elseif(!isset($CFG->registration[$secid])){$reg=$CFG->registration[1];}
		else{$reg=$CFG->registration[$secid];}

		/* if double registration is configured then the reg array
		   should be set to the turnover time for the registers to PM
		   (and set in 24 hour clock)
		*/
		if($reg!='single' and $reg!=''){
			$time=split(':',$reg);
			if(date('H')>$time[0] or (date('H')==$time[0] and date('i')>=$time[1])){$session='PM';}
			}
		}

	$d_event=mysql_query("SELECT id FROM event WHERE date='$date' 
						AND period='$session';");
	if(mysql_num_rows($d_event)==0){
		$eveid='0';
		}
	else{
		$eveid=mysql_result($d_event,0);
		}
	$event=array('id'=>$eveid,'date'=>$date,'period'=>$session);
	return $event;
	}

/**
 *
 * Returns the number of sids in a community and if eveid is for a
 * vliad event then the number present and number absent.
 *
 */
function check_communityAttendance($community,$eveid=-1){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	$nosids=countin_community($community);

	/* If no register yet taken for current session then $eveid=0 so set sensible defaults*/
	$nop=0;$noa=0;

	if($eveid>0){
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
		}

	$results=array($nosids,$nop,$noa);
	return $results;
	}


/**
 *
 */
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
				students who are merely late (codes U, L, UA, UB), as these students
				will be on site and in classes. They will though still be
				counted in statistics as absent.*/
			if($attendance['code']!='U' and $attendance['code']!='L' and $attendance['code']!='UB' 
			   and $attendance['code']!='UA' and $lates==0){
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


/**
 * Produces an xml-array called Summary with label,value pairs containg 
 * number of lates, attended, authorised absences and unauthorised absences.
 * Need to add count for approved educational activity codes.
 */
function fetchAttendanceSummary($sid,$startdate,$enddate){
	$Attendance['Summary']=array();

	$no_present=count_attendance($sid,$startdate,$enddate);

	/**
	 * These are lates after the register closed only
	 * NB. UK Government says a late does not count as a present but here it does
	 *
	 * NB. Non-UK local additions here are UA and UB for authorised lates
	 */
	$no_late_authorised=count_attendance($sid,$startdate,$enddate,'L');
	$no_late_authorised+=count_attendance($sid,$startdate,$enddate,'UA');
	$no_late_authorised+=count_attendance($sid,$startdate,$enddate,'UB');
	$no_late_unauthorised=count_attendance($sid,$startdate,$enddate,'U');
	$no_late=$no_late_authorised+$no_late_unauthorised;
	$no_attended=$no_present+$no_late;
	$Attendance['Summary']['Attended']=array('label' => 'attended',
											 'value' => ''.$no_attended);
	$Attendance['Summary']['Late']=array('label' => 'late',
										 'value' => ''.$no_late);
	/** 
	 * For the purpose of official statistics an attendnace code can
	 * be either an unauthorised absence, authorised absence or in
	 * attendance and the following formula resepcts this for
	 * compiling the summary are  
	 */
	$no_absent=count_attendance($sid,$startdate,$enddate,'%') - $no_late;
	$no_notagreed=count_attendance($sid,$startdate,$enddate,'G');
	$no_notexplained=count_attendance($sid,$startdate,$enddate,'O');
	$no_noreason=count_attendance($sid,$startdate,$enddate,'N');
	//$no_ill=count_attendance($sid,$startdate,$endate,'I');
	//$no_medical=count_attendance($sid,$startdate,$endate,'M');

	$no_unauthorised_absent=$no_notagreed+$no_noreason+$no_notexplained;
	$no_authorised_absent=$no_absent-$no_unauthorised_absent;

	$Attendance['Summary']['Absentauthorised']=array('label' => 'authorisedabsent',
													 'value' => ''.$no_authorised_absent);
	$Attendance['Summary']['Absentunauthorised']=array('label' => 'unauthorisedabsent',
													   'value' => ''.$no_unauthorised_absent);
	$Attendance['Summary']['Lateunauthorised']=array('label' => 'lateunauthrosied',
													 'value' => ''.$no_late_unauthorised);
	$Attendance['Summary']['Lateauthorised']=array('label' => 'lateauthorised',
												   'value' => ''.$no_late_authorised);
	$Attendance['Summary']['Notexplained']=array('label' => 'unexplained',
												 'value' => ''.$no_notexplained);
	$Attendance['Summary']['Enddate']=array('label' => 'enddate',
											'value' => ''.$enddate);
	$Attendance['Summary']['Startdate']=array('label' => 'startdate',
											  'value' => ''.$startdate);
	return $Attendance;
	}

/* This will count all present marks unless a code is specified when */
/* it will count absence marks with that code. Set code=% will cunt */
/* all absence marks excluding those which can be counted for a */
/* student (ie. school closed #, not on roll Z, enforced closure Y and */
/* non-compulsory age X) */
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
			AND attendance.code!='X' AND attendance.code!='Y' AND attendance.code!='Z'  
			AND attendance.code!='#' 
			AND event.date > '$startdate' AND event.date < '$enddate';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}
?>