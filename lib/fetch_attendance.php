<?php	
/**	   							 fetch_attendance.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since		
 */	

/**
 *
 * Returns a blank attendance record (even for sid!=-1!)
 * 
 * @param integer $sid
 * @return array
 */
function fetchAttendance($sid='-1'){
	$Attendance=array();
	$Attendance['id_db']=$attendance['id'];
	$Attendance['Date']=array('label'=>'date', 
							  'inputtype'=> 'required',
							  'table_db'=>'event', 
							  'field_db'=>'date',
							  'type_db'=>'date', 
							  'value'=>''.$attendance['date']);
	$Attendance['Session']=array('label'=>'session',
								 'inputtype'=>'required',
								 'table_db'=>'event', 
								 'field_db'=>'session',
								 'type_db'=>'enum', 
								 'value'=>''.$attendance['session']);
	$Attendance['Period']=array('label'=>'period',
								//'inputtype'=>'required',
								'table_db'=>'event', 
								'field_db'=>'period',
								'type_db'=>'enum', 
								'value'=>''.$attendance['period']);
	$Attendance['Status']=array('label'=>'attendance',
								'inputtype'=> 'required',
								'table_db'=>'attendance', 
								'field_db'=>'status',
								'type_db'=>'enum', 
								'value'=>''.$attendance['status']);
	$Attendance['Code']=array('label'=>'code',
							  'inputtype'=> 'required',
							  'table_db'=>'attendance', 
							  'field_db'=>'code',
							  'type_db'=>'enum', 
							  'value'=>''.$attendance['code']);
	$Attendance['Late']=array('label'=>'late',
							  'table_db'=>'attendance', 
							  'field_db'=>'code',
							  'type_db'=>'enum', 
							  'value'=>''.$attendance['late']);
	$Attendance['Comment']=array('label'=>'comment',
								 'table_db'=>'attendance', 
								 'field_db'=>'comment',
								 'type_db'=>'text', 
								 'value'=>''.$attendance['comment']);
	$Attendance['Logtime']=array('label'=>'time',
								 'table_db'=>'attendance', 
								 'field_db'=>'logtime',
								 'type_db'=>'text', 
								 'value'=>''.$attendance['logtime']);
	$Attendance['Teacher']=array('label'=>'teacher',
								 'table_db'=>'attendance', 
								 'field_db'=>'teacher_id',
								 'type_db'=>'varchar(14)', 
								 'value'=>''.$attendance['teacher_id']);
	$Attendance['Class']=array('label'=>'class',
							   'table_db'=>'attendance', 
							   'field_db'=>'class_id',
							   'type_db'=>'varchar(10)', 
							   'value'=>''.$attendance['class_id']);
	return $Attendance;
	}


/**
 *
 * Returns all attendance records for the $nodays before the day
 * specified by $startday (and $startday=0 is today)
 *
 * The returned Attendances array includes $Attendances['eveindex']
 * where the eveindex is an array to provide a lookup index to the
 * Attendances based on the eveid
 *
 * @param integer sid
 * @param integer $startday
 * @param integer $nodays
 * @return array
 */
function fetchAttendances($sid,$startday=0,$nodays=7){
	$Attendances=array();
	$eveindex=array();
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday,date('Y')));

	/* If nodays is 1 then interested in all events for this day
	 * otherwise we are looking for events limited to AM/PM sessions (ie. period=0)
	 * for the time window
	 */
	if($nodays==1){
		$d_a=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, attendance.teacher_id, attendance.class_id,
			UNIX_TIMESTAMP(attendance.logtime) AS logtime, event.id,
			event.session, event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.date='$startdate' 
			ORDER BY event.date, event.session, event.period;");
		}
	else{
		$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-$nodays,date('Y')));
		$d_a=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(attendance.logtime) AS logtime, event.id,
			event.session, event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.date <= '$startdate' 
			AND event.date >= '$enddate' AND event.period='0'
			ORDER BY event.date, event.session;");
		}

	$index=0;
	$Attendances['Attendance']=array();
	while($a=mysql_fetch_array($d_a,MYSQL_ASSOC)){
		$Attendance=array();
		$Attendance['id_db']=$a['id'];
	   	$Attendance['Date']=array('label'=>'date', 
								  'type_db'=>'date', 
								  'value'=>''.$a['date']);
	   	$Attendance['Session']=array('label'=>'session',
									 'value'=>''.$a['session']);
	   	$Attendance['Period']=array('label'=>'period',
									 'value'=>''.$a['period']);
	   	$Attendance['Status']=array('label'=>'attendance',
									'value'=>''.$a['status']);
	   	$Attendance['Code']=array('label'=>'code',
								  'value'=>''.$a['code']);
	   	$Attendance['Late']=array('label'=>'late',
								  'value'=>''.$a['late']);
	   	$Attendance['Comment']=array('label'=>'comment',
									 'value'=>''.$a['comment']);
	   	$Attendance['Logtime']=array('label'=>'time',
									 'value'=>''.$a['logtime']);
		if($nodays==1){
			$Attendance['Teacher']=array('label'=>'teacher',
										 'value'=>''.$a['teacher_id']);
			$Attendance['Class']=array('label'=>'class',
									   'value'=>''.$a['class_id']);
			}
		$Attendances['Attendance'][]=$Attendance;
		$eveindex[$a['id']]=$index++;
		}


	$Attendances['eveindex']=$eveindex;
	return $Attendances;
	}



/**
 *
 * Returns attendance records for the previous number of lessons
 * for the class $cid before the day specified by $startday (and
 * $startday=0 is today)
 *
 * The returned Attendances array includes $Attendances['eveindex']
 * where the eveindex is an array to provide a lookup index to the
 * Attendances based on the eveid
 *
 * @param integer cid
 * @param integer $startday
 * @param integer $lessonno
 * @param integer sid
 *
 * @return array
 */
function fetch_classAttendances($cid,$sid,$startday=0,$lessonno=4,$dayno=-1){
	$Attendances=array();
	$eveindex=array();
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday,date('Y')));

	if($dayno==-1){
		$datelimit='';
		}
	else{
		/* limit to lessons within a number of days */
		$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-($dayno-1),date('Y')));
		$datelimit="AND event.date >= '$enddate' ";
		}

	$d_a=mysql_query("SELECT attendance.status, attendance.code, attendance.late, attendance.comment, 
			event.id, event.session, event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE attendance.class_id='$cid'
			AND attendance.student_id='$sid' AND event.date <= '$startdate' $datelimit 
			ORDER BY event.date DESC, event.session, event.period LIMIT $lessonno;");

	$index=0;
	$Attendances['Attendance']=array();
	while($a=mysql_fetch_array($d_a,MYSQL_ASSOC)){
		$Attendance=array();
		$Attendance['id_db']=$a['id'];
	   	$Attendance['Date']=array('label'=>'date', 
								  'type_db'=>'date', 
								  'value'=>''.$a['date']);
	   	$Attendance['Session']=array('label'=>'session',
									 'value'=>''.$a['session']);
	   	$Attendance['Period']=array('label'=>'period',
									 'value'=>''.$a['period']);
	   	$Attendance['Status']=array('label'=>'attendance',
									'value'=>''.$a['status']);
	   	$Attendance['Code']=array('label'=>'code',
								  'value'=>''.$a['code']);
	   	$Attendance['Late']=array('label'=>'late',
								  'value'=>''.$a['late']);
	   	$Attendance['Comment']=array('label'=>'comment',
									 'value'=>''.$a['comment']);
		$Attendances['Attendance'][]=$Attendance;
		$eveindex[$a['id']]=$index++;
		}

	//trigger_error(sizeof($eveindex),E_USER_WARNING);
	$Attendances['eveindex']=$eveindex;
	return $Attendances;
	}


/**
 *
 * Returns a single attendance record for a single sid for the event
 * identified by eveid or the current event if none specified.
 *
 * @param integer sid
 * @param integer $eveid
 * @return array
 *
 */
function fetchcurrentAttendance($sid,$eveid=''){
	if($eveid==''){
		$secid=get_student_section($sid);
		$event=get_currentevent($secid);
		$eveid=$event['id'];
		}
	$Attendance=array();
	if($eveid!=''){
		$d_a=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(logtime) AS logtime, event.id,
			event.session, event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.id='$eveid';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);

		$Attendance['id_db']=$a['id'];
	   	$Attendance['Date']=array('label'=>'date', 
									'value'=>''.$a['date']);
		$Attendance['Session']=array('label'=>'session',
								'value'=>''.$a['session']);
	   	$Attendance['Period']=array('label'=>'period',
									 'value'=>''.$a['period']);
	   	$Attendance['Status']=array('label'=>'attendance',
								  'value'=>''.$a['status']);
	   	$Attendance['Code']=array('label'=>'code',
								  'value'=>''.$a['code']);
	   	$Attendance['Late']=array('label'=>'late',
								  'value'=>''.$a['late']);
	   	$Attendance['Comment']=array('label'=>'comment',
								  'value'=>''.$a['comment']);
	   	$Attendance['Logtime']=array('label'=>'time',
								  'value'=>''.$a['logtime']);
		}
	return $Attendance;
	}





/**
 *
 * Given an event_id it returns the xml_array for the event.
 * An event does not neccessarily have to be an attendance event and
 * this is therefore not really exclusive to attendnace but... 
 *
 * @param integer $eveid
 * @return array
 */
function fetchAttendanceEvent($eveid='-1'){
	$Event=array();
	$d_event=mysql_query("SELECT session, period, date FROM event WHERE id='$eveid'");
	$event=mysql_fetch_array($d_event,MYSQL_ASSOC);

	$Event=array();
	$Event['id_db']=$eveid;
	$Event['Date']=array('label'=>'date', 
						 'value'=>''.$event['date']);
	$Event['Session']=array('label'=>'session',
						   'value'=>''.$event['session']);
	$Event['Period']=array('label'=>'period',
						   'value'=>''.$event['period']);
	return $Event;
	}



/**
 *
 * Returns all events which exist in the db inclusive of the session 
 * from startday to the previous nodays 
 *
 * @param integer $startday
 * @param integer $nodays
 * @param string $nodays
 * @return array
 */
function fetchAttendanceEvents($startday=0,$nodays=7,$session='%'){
	$AttendanceEvents=array();
	$startdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday,date('Y')));

	if($nodays==1){
		$d_event=mysql_query("SELECT id, session, period, date FROM event WHERE date='$startdate' 
			AND session LIKE '$session'	ORDER BY date, session, period;");
		}
	else{
		$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')+$startday-$nodays,date('Y')));
		$d_event=mysql_query("SELECT id, session, period, date FROM event WHERE date <= '$startdate' 
			AND date > '$enddate' AND session LIKE '$session' AND event.period='0' ORDER BY date, session;");
		}

	$AttendanceEvents['Event']=array();
	$index=0;
	$eveindex=array();
	$perindex=array();
	while($event=mysql_fetch_array($d_event,MYSQL_ASSOC)){
		$Event=array();
		$Event['id_db']=$event['id'];
	   	$Event['Date']=array('label'=>'date', 
							 'type_db'=>'date', 
							 'value'=>''.$event['date']);
	   	$Event['Session']=array('label'=>'session',
							   'value'=>''.$event['session']);
	   	$Event['Period']=array('label'=>'period',
							   'value'=>''.$event['period']);
		$AttendanceEvents['Event'][]=$Event;
		$eveindex[$event['id']]=$index;
		$perindex[]=$event['period'];//only needed when nodays=1
		$index++;
		}
	$AttendanceEvents['eveindex']=$eveindex;
	$AttendanceEvents['perindex']=$perindex;

	return $AttendanceEvents;
	}



/**
 *
 *
 * @param integer $secid
 * @return array
 */
function get_currentevent($secid=1){
	global $CFG;

	$date=date('Y-m-d');
	$session='AM';

	/* secid=1 is wholeschool, use this if no others configured */
	if(!is_array($CFG->registration)){$reg='single';}
	elseif(!isset($CFG->registration[$secid])){$reg=$CFG->registration[1];}
	else{$reg=$CFG->registration[$secid];}

	/* If double registration is configured then the reg array
	   should be set to the turnover time for the registers to PM
	   (and set in 24 hour clock)
	*/
	if($reg!='single' and $reg!=''){
		$time=explode(':',$reg);
		if(date('H')>$time[0] or (date('H')==$time[0] and date('i')>=$time[1])){$session='PM';}
		}

	$event=get_event($date,$session);

	return $event;
	}


/**
 *
 *
 * @param array $currentevent
 * @return array
 */
function get_class_periods($currentevent,$secid=1){
	global $CFG;

	if(!isset($CFG->regperiods)){
		$periods[1]['AM']=array();
		$periods[1]['PM']=array();
		}
	else{
		$periods=$CFG->regperiods;
		}

	if(isset($periods[$secid])){
		$classperiods=(array)$periods[$secid][$currentevent['session']];
		}
	else{
		$classperiods=(array)$periods[1][$currentevent['session']];
		}


	/* secid=1 is wholeschool, use this if no others configured
	 * if(!is_array($CFG->registration)){$reg='single';}
	 * elseif(!isset($CFG->registration[$secid])){$reg=$CFG->registration[1];}
	 * else{$reg=$CFG->registration[$secid];}
	 */

	/* If double registration is configured then the reg array
	 * should be set to the turnover time for the registers to PM
	 * (and set in 24 hour clock)


	$date=date('Y-m-d');
	$time=explode(':',$reg);
	if(date('H')>$time[0] or (date('H')==$time[0] and date('i')>=$time[1])){$session='PM';}

	 */

	return $classperiods;
	}

/**
 *
 * Returns an event record for the matching date, if no date set then 
 * the default is to return the current session event.
 *
 * @param date $date
 * @param string $session
 * @param string $period
 * @return array
 */
function get_event($date='',$session='',$period='0'){

	if($date==''){
		$date=date('Y-m-d');
		}
	if($session==''){
		$session='AM';
		}

	$d_event=mysql_query("SELECT id FROM event WHERE date='$date' 
						AND session='$session' AND period='$period';");
	if(mysql_num_rows($d_event)==0){
		$eveid='0';
		}
	else{
		$eveid=mysql_result($d_event,0);
		}

	$event=array('id'=>$eveid,'date'=>$date,'session'=>$session,'period'=>$period);
	return $event;
	}

/**
 *
 * Returns the number of sids in a community and if eveid is for a
 * valid event then the number of them present and absent and late.
 *
 * @param array $community
 * @param integer $session
 * @return array
 */
function check_community_attendance($community,$event){
	if($community['id']!=''){$comid=$community['id'];}
	else{$comid=updateCommunity($community);}
	if(isset($community['yeargroup_id']) and $community['yeargroup_id']!=''){$yid=$community['yeargroup_id'];}
	$eveid=$event['id'];
	$nosids=countin_community($community);

	/* If no register yet taken for current session then $eveid=0 so set sensible defaults*/
	$nop=0;$noa=0;$nol=0;$nopl=0;

	if($eveid>0){
		if(isset($event['date'])){
			$startdate=$event['date'];
			$enddate=$event['date'];
			}
		else{
			$Event=fetchAttendanceEvent($eveid);
			$startdate=$Event['Date']['value'];
			$enddate=$Event['Date']['value'];
			}

		if(isset($yid) and $yid>-9000){
			/* no absent */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance 
							 WHERE attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code NOT IN ('U','L','UB','UA','US') AND attendance.student_id=ANY(
				SELECT comidsid.student_id FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE community_id='$comid' AND student.yeargroup_id='$yid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL));");
			$noa=mysql_result($d_att,0);

			/* no late */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance 
							 WHERE attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code IN ('U','L','UB','UA') AND attendance.student_id=ANY(
				SELECT comidsid.student_id FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE community_id='$comid' AND student.yeargroup_id='$yid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL));");
			$nol=mysql_result($d_att,0);

			/* no present at start but signed out */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance 
							 WHERE attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code IN ('US') AND attendance.student_id=ANY(
				SELECT comidsid.student_id FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE community_id='$comid' AND student.yeargroup_id='$yid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL));");
			$noso=mysql_result($d_att,0);

			/* no present */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance 
							 WHERE attendance.event_id='$eveid' AND attendance.status='p' AND attendance.student_id=ANY(
				SELECT comidsid.student_id FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE comidsid.community_id='$comid' AND student.yeargroup_id LIKE '$yid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL));");
			$nop=mysql_result($d_att,0) + $noso;

			/* Number present but late to register*/
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance 
							 WHERE attendance.event_id='$eveid' AND attendance.status='p' AND attendance.late!='0' AND attendance.student_id=ANY(
				SELECT comidsid.student_id FROM comidsid JOIN student ON student.id=comidsid.student_id
				 WHERE comidsid.community_id='$comid' AND student.yeargroup_id LIKE '$yid' AND (comidsid.leavingdate>'$enddate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
				AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL));");
			$nopl=mysql_result($d_att,0);
			}
		else{
			/* Number absent */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid'  
							 AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)
							 AND attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code NOT IN ('U','L','UB','UA','US');");
			$noa=mysql_result($d_att,0);

			/* Number late after register closed */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid'  
							 AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)
							 AND attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code IN ('U','L','UB','UA');");
			$nol=mysql_result($d_att,0);

			/* Number present for start of sesssion but signed out */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid'  
							 AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)
							 AND attendance.event_id='$eveid' AND attendance.status='a' AND attendance.code IN ('US');");
			$noso=mysql_result($d_att,0);

			/* Number present */
			$d_att=mysql_query("SELECT COUNT(attendance.student_id) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid' 
							 AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)
							 AND attendance.event_id='$eveid' AND attendance.status='p'");
			$nop=mysql_result($d_att,0) + $noso;

			/* Number present but late to register*/
			$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN comidsid
							 ON comidsid.student_id=attendance.student_id 
							 WHERE comidsid.community_id='$comid' 
							 AND (comidsid.leavingdate>'$enddate' OR comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
							 AND (comidsid.joiningdate<='$startdate' OR comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL)
							 AND attendance.status='p'  AND attendance.late!='0' AND attendance.event_id='$eveid';");
			$nopl=mysql_result($d_att,0);
			}
		}

	$results=array($nosids,$nop,$noa,$nol,$nopl);
	return $results;
	}


/**
 *
 * Returns an xml-array Student array which is empty except for their
 * Attendance for a single event. The sids included will be only those
 * who are strictly not in school (that is absent but not late or present but signed out). 
 * Set lates=1 to include all absent students (including those lates).
 * 
 * @param integer $eveid
 * @param integer $lates
 * @return array
 */
function list_absentStudents($eveid='',$yid='%',$lates=0){
	if($eveid==''){
		$event=get_currentevent();
		$eveid=$event['id'];
		}
	$Students['Student']=array();
	if($eveid!=''){
		$d_attendance=mysql_query("SELECT student.id AS sid, attendance.status, 
			attendance.code, attendance.late, attendance.comment, 
			UNIX_TIMESTAMP(attendance.logtime) AS logtime
			FROM attendance JOIN student ON student.id=attendance.student_id WHERE
			attendance.student_id=student.id AND
			attendance.event_id='$eveid' AND attendance.status='a' AND student.yeargroup_id LIKE '$yid'
			ORDER BY student.yeargroup_id, student.form_id, student.surname");
		$Attendance=array();
		$Student=array();
		while($attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC)){
			/* Logical lates defaults to 0 and flags to filter out those
			 * students who are merely late (codes U, L, UA, UB), as these students
			 * will be on site and in classes. They will though still be
			 * counted in statistics as an absensce.
			 */
			if($attendance['code']!='U' and $attendance['code']!='L' and $attendance['code']!='UB' 
			   and $attendance['code']!='UA' and $lates==0){
				$Attendance['id_db']=$eveid;
				$Attendance['Status']=array('label'=>'attendance',
											'value'=>''.$attendance['status']);
				$Attendance['Code']=array('label'=>'code',
										  'value'=>''.$attendance['code']);
				$Attendance['Late']=array('label'=>'late',
										  'value'=>''.$attendance['late']);
				$Attendance['Comment']=array('label'=>'comment',
										 'value'=>''.$attendance['comment']);
				$Attendance['Logtime']=array('label'=>'time',
										 'value'=>''.$attendance['logtime']);
				$Student['id_db']=$attendance['sid'];
				$Student['Attendance']=$Attendance;
				$Students['Student'][]=$Student;
				}
			}
		}
	return $Students;
	}


/**
 *
 * Produces an xml-array called Summary with label,value pairs containg 
 * number of lates, attended, authorised absences and unauthorised absences.
 * Need to add count for approved educational activity codes.
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @return array
 */
function fetchAttendanceSummary($sid,$startdate,$enddate,$session='%'){
	$Attendance['Summary']=array();
	$Attendance['Summary']['Session']=$session;

	$no_present=count_attendance($sid,$startdate,$enddate,'',$session);

	/**
	 * These are lates after the register closed only
	 * NB. UK Government says a late does not count as a present but here it does
	 *
	 * NB. Non-UK local additions here are UA and UB for authorised lates and US for signed out
	 */
	$no_late_authorised=count_attendance($sid,$startdate,$enddate,'L',$session);
	$no_late_authorised+=count_attendance($sid,$startdate,$enddate,'UA',$session);
	$no_late_authorised+=count_attendance($sid,$startdate,$enddate,'UB',$session);
	$no_late_unauthorised=count_attendance($sid,$startdate,$enddate,'U',$session);
	$no_signed_out=count_attendance($sid,$startdate,$enddate,'US',$session);
	$no_visit=count_attendance($sid,$startdate,$enddate,'V',$session);
	$no_late=$no_late_authorised+$no_late_unauthorised;

	/* Attended: includes all partial sessions (late after register and out for educational visits/trip and signed out) */
	$no_attended=$no_present+$no_late+$no_visit+$no_signed_out;

	/** 
	 * For the purpose of official statistics an attendnace code can
	 * be either an unauthorised absence, authorised absence or in
	 * attendance and the following formula resepcts this for
	 * compiling the summary
	 */
	$no_absent=count_attendance($sid,$startdate,$enddate,'%',$session) - $no_late - $no_visit - $no_signed_out;
	$no_notagreed=count_attendance($sid,$startdate,$enddate,'G',$session);
	$no_notexplained=count_attendance($sid,$startdate,$enddate,'O',$session);
	$no_noreason=count_attendance($sid,$startdate,$enddate,'N',$session);
	$no_late_register=count_late($sid,$startdate,$enddate,$session);
	//$no_ill=count_attendance($sid,$startdate,$endate,'I');
	//$no_medical=count_attendance($sid,$startdate,$endate,'M');

	$no_possible=$no_attended+$no_absent;
	$no_unauthorised_absent=$no_notagreed+$no_noreason+$no_notexplained;
	$no_authorised_absent=$no_absent-$no_unauthorised_absent;

	$Attendance['Summary']['Attended']=array('label'=>'attended',
											 'value'=>''.$no_attended);
	$Attendance['Summary']['Possible']=array('label'=>'possible',
											 'value'=>''.$no_possible);
	$Attendance['Summary']['Late']=array('label'=>'late',
										 'value'=>''.$no_late);
	$Attendance['Summary']['Signedout']=array('label'=>'late',
											  'value'=>''.$no_signed_out);
	$Attendance['Summary']['Absentauthorised']=array('label'=>'authorisedabsent',
													 'value'=>''.$no_authorised_absent);
	$Attendance['Summary']['Absentunauthorised']=array('label'=>'unauthorisedabsent',
													   'value'=>''.$no_unauthorised_absent);
	$Attendance['Summary']['Lateunauthorised']=array('label'=>'lateunauthrosied',
													 'value'=>''.$no_late_unauthorised);
	$Attendance['Summary']['Lateauthorised']=array('label'=>'lateauthorised',
												   'value'=>''.$no_late_authorised);
	$Attendance['Summary']['Latetoregister']=array('label'=>'latetoregister',
												   'value'=>''.$no_late_register);
	$Attendance['Summary']['Notexplained']=array('label'=>'unexplained',
												 'value'=>''.$no_notexplained);
	$Attendance['Summary']['Enddate']=array('label'=>'enddate',
											'value'=>''.$enddate);
	$Attendance['Summary']['Startdate']=array('label'=>'startdate',
											  'value'=>''.$startdate);
	return $Attendance;
	}





/**
 *
 * Produces an xml-array called Summary with label,value pairs containing 
 * number of lates, attended, authorised absences and unauthorised absences.
 * Need to add count for approved educational activity codes.
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @return array
 */
function fetch_classAttendanceSummary($cid,$sid,$startdate,$enddate,$session='%'){
	$Attendance['Summary']=array();
	$Attendance['Summary']['Session']=$session;

	$no_present=count_class_attendance($sid,$cid,$startdate,$enddate,'',$session);

	/**
	 * These are lates after the register closed only
	 * NB. UK Government says a late does not count as a present but here it does
	 *
	 * NB. Non-UK local additions here are UA and UB for authorised lates and US for signed out
	 */
	$no_late_authorised=count_class_attendance($sid,$cid,$startdate,$enddate,'L',$session);
	$no_late_authorised+=count_class_attendance($sid,$cid,$startdate,$enddate,'UA',$session);
	$no_late_authorised+=count_class_attendance($sid,$cid,$startdate,$enddate,'UB',$session);
	$no_late_unauthorised=count_class_attendance($sid,$cid,$startdate,$enddate,'U',$session);
	$no_visit=count_class_attendance($sid,$cid,$startdate,$enddate,'V',$session);
	$no_late=$no_late_authorised+$no_late_unauthorised;

	/* Attended: includes all partial sessions (late after register and out for educational visits/trip */
	$no_attended=$no_present+$no_late+$no_visit;
	/* But signed out is counted as an absent for the sake of class lesson attendance unlike for the session. */


	/** 
	 * For the purpose of official statistics an attendnace code can
	 * be either an unauthorised absence, authorised absence or in
	 * attendance and the following formula resepcts this for
	 * compiling the summary
	 */
	$no_absent=count_class_attendance($sid,$cid,$startdate,$enddate,'%',$session) - $no_late - $no_visit;
	$no_notagreed=count_class_attendance($sid,$cid,$startdate,$enddate,'G',$session);
	$no_notexplained=count_class_attendance($sid,$cid,$startdate,$enddate,'O',$session);
	$no_noreason=count_class_attendance($sid,$cid,$startdate,$enddate,'N',$session);
	//$no_late_register=count_late($sid,$startdate,$enddate,$session);

	$no_unauthorised_absent=$no_notagreed+$no_noreason+$no_notexplained;
	$no_authorised_absent=$no_absent-$no_unauthorised_absent;

	$Attendance['Summary']['Attended']=array('label'=>'attended',
											 'value'=>''.$no_attended);
	$Attendance['Summary']['Late']=array('label'=>'late',
										 'value'=>''.$no_late);
	$Attendance['Summary']['Absentauthorised']=array('label'=>'authorisedabsent',
													 'value'=>''.$no_authorised_absent);
	$Attendance['Summary']['Absentunauthorised']=array('label'=>'unauthorisedabsent',
													   'value'=>''.$no_unauthorised_absent);
	$Attendance['Summary']['Lateunauthorised']=array('label'=>'lateunauthrosied',
													 'value'=>''.$no_late_unauthorised);
	$Attendance['Summary']['Lateauthorised']=array('label'=>'lateauthorised',
												   'value'=>''.$no_late_authorised);
	/*
	$Attendance['Summary']['Latetoregister']=array('label'=>'latetoregister',
												   'value'=>''.$no_late_register);
	*/
	$Attendance['Summary']['Notexplained']=array('label'=>'unexplained',
												 'value'=>''.$no_notexplained);
	$Attendance['Summary']['Enddate']=array('label'=>'enddate',
											'value'=>''.$enddate);
	$Attendance['Summary']['Startdate']=array('label'=>'startdate',
											  'value'=>''.$startdate);
	return $Attendance;
	}



/**
 * This will count all present marks unless a code is specified when 
 * it will count absence marks with that code. Set code=% will count
 * all absence marks excluding those which can not be counted for a 
 * student (ie. school closed #, not on roll Z, enforced closure Y and
 * non-compulsory age X).
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @param string $code
 * @return array
 */
function count_attendance($sid,$startdate,$enddate,$code='',$session='%'){

	if($code==''){
		$status='p';
		$code='%';
		}
	else{
		$status='a';
		}
	$excludecode="";
	/*Normaly X and Z  should always be discounted for statistics except they have explicitly been searched for. */
	if($code!='X'){
		$excludecode=" AND attendance.code!='X'";
		}
	if($code!='Z'){
		$excludecode.=" AND attendance.code!='Z'";
		}
	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND attendance.status='$status' AND attendance.code LIKE '$code' 
			$excludecode AND attendance.code!='Y' AND attendance.code!='#' 
			AND event.date >= '$startdate' AND event.date <= '$enddate' AND event.period='0' AND event.session LIKE '$session';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}


/**
 * This will count all present marks unless a code is specified when 
 * it will count absence marks with that code. Set code=% will count
 * all absence marks excluding those which can not be counted for a 
 * student (ie. school closed #, not on roll Z, enforced closure Y and
 * non-compulsory age X).
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @param string $code
 * @return array
 */
function count_class_attendance($sid,$cid,$startdate,$enddate,$code=''){

	if($code==''){
		$status='p';
		$code='%';
		}
	else{
		$status='a';
		}
	$excludecode="";
	/*Normaly X and Z  should always be discounted for statistics except they have explicitly been searched for. */
	if($code!='X'){
		$excludecode=" AND attendance.code!='X'";
		}
	if($code!='Z'){
		$excludecode.=" AND attendance.code!='Z'";
		}
	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid'  AND attendance.class_id='$cid' AND attendance.status='$status' 
			AND attendance.code LIKE '$code' $excludecode AND attendance.code!='Y' AND attendance.code!='#' 
			AND event.date >= '$startdate' AND event.date <= '$enddate';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}




/**
 * This will count all present marks which have flagged with a
 * late, that is lates before registration closed.
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @return integer
 */
function count_late($sid,$startdate,$enddate,$session='%'){

	$status='p';
	$code='';
	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND attendance.status='$status'  AND attendance.late!='0' 
			AND event.date >= '$startdate' AND event.date <= '$enddate' AND event.period='0' AND event.session LIKE '$session';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}


/**
 *
 * Count no of attendance entries recorded for the whole schol between two given dates.
 * If no absence code is given then will be counting status=present entries only.
 * If a code given then coutning status=absent entries.
 * Disgards all entries which are not part of the school record (X, Y, Z and #).
 *
 * @param date $startdate
 * @param date $enddate
 * @param string $code
 * @return integer
 *
 * TODO: Only counting AM at the moment!!!!
 *
 */
function count_overall_attendance($startdate,$enddate,$code=''){
	if($code==''){
		$status='p';
		$code='%';
		}
	else{
		$status='a';
		}

	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.status='$status' AND attendance.code LIKE '$code' 
			AND attendance.code!='X' AND attendance.code!='Y' AND attendance.code!='Z'  
			AND attendance.code!='#' 
			AND event.date >= '$startdate' AND event.date <= '$enddate' AND period='0' AND session='AM';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}


/**
 *
 * TODO: Only counting AM at the moment!!!!
 *
 */
function count_overall_late($startdate,$enddate){
	$d_attendance=mysql_query("SELECT COUNT(attendance.status) FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.status='a' AND  
			(attendance.code='U' OR attendance.code='UA' OR attendance.code='UB')
			AND event.date >= '$startdate' AND event.date <= '$enddate' AND period='0' AND session='AM';");
	$noatts=mysql_result($d_attendance,0);

	return $noatts;
	}


/**
 *
 *
 * @param date $startdate
 * @param date $enddate
 * @param string $session
 * @param string $period
 * @return array
 */
function list_events($startdate,$enddate,$session='',$period='0'){

	if($session==''){
		$session='AM';
		}

	//trigger_error('SESS!! '.$session,E_USER_WARNING);
	$events=array();
	$d_event=mysql_query("SELECT id,date,session,period FROM event WHERE 
						session LIKE '$session' AND period LIKE '$period' 
						AND date >= '$startdate' AND date <= '$enddate';");
	if(mysql_num_rows($d_event)>0){
		while($e=mysql_fetch_array($d_event,MYSQL_ASSOC)){
			$events[]=array('id'=>$e['id'],'date'=>$e['date'],'session'=>$e['session'],'period'=>$e['period']);
			}
		}

	return $events;
	}


/**
 *
 * Returns all attendance bookings for a sid on a given date
 *
 * @param integer $sid
 * @param date $date
 * @param enum $dayb
 * @return array
 *
 */
function list_student_attendance_bookings($sid,$date,$day='%',$attsession='%'){
	$bookings=array();

	/* The most recent (specific) date takes precedence so only use the first two returned */
	$d_b=mysql_query("SELECT id, community_id, session, status, code, startdate, enddate, day, comment 
						FROM attendance_booking  
						WHERE student_id='$sid' AND session LIKE '$attsession' AND startdate<='$date' 
						AND (enddate>='$date' OR enddate='0000-00-00') AND (day LIKE '$day' OR day='%') 
						ORDER BY startdate DESC, enddate DESC, day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$bookings[]=$b;
		}

	return $bookings;
	}



/**
 *
 * Returns a single booking for a given booking_id. 
 * 
 * @param integer $bookingid
 * @return array
 *
 */
function get_attendance_booking($bookid){
	$booking=array();

	$d_b=mysql_query("SELECT id, code, session, status, startdate, enddate, day, comment 
						FROM attendance_booking WHERE id='$bookid';");
	$booking=mysql_fetch_array($d_b,MYSQL_ASSOC);

	return $booking;
	}

/**
 *
 * @param array $booking
 * @return
 *
 */
function add_attendance_booking($sid,$date='',$attsession,$code,$dayrepeat='once',$comment=''){

	if($date==''){$date=date('Y-m-d');}
	$day=date('N',strtotime($date));
	$newenddate=date('Y-m-d',strtotime($date.' -1 day'));

	if($dayrepeat=='once'){$day=$day;$startdate=$date;$enddate=date('Y-m-d',strtotime($date.' +1 day'));}
	elseif($dayrepeat=='weekly'){$day=$day;$startdate=$date;$enddate='0000-00-00';}
	elseif($dayrepeat=='every'){$day='%';$startdate=$date;$enddate='0000-00-00';}


	/* Find existing bookings which conflict with the new one and delete. */
	$d_b=mysql_query("SELECT id, startdate, enddate, day FROM attendance_booking 
						WHERE student_id='$sid' AND session='$attsession' 
						AND ((startdate<='$startdate' AND (enddate>='$startdate' or enddate='0000-00-00'))
						OR (startdate>='$startdate' AND (startdate<='$enddate' OR $enddate='0000-00-00')));");
	while($oldb=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$oldbookid=$oldb['id'];
		if($dayrepeat=='once'){
			if($oldb['startdate']==$startdate and $oldb['enddate']==$enddate and $oldb['day']==$day){
				/*delete old*/
				delete_attendance_booking($sid,$oldbookid);
				}
			}
		elseif($dayrepeat=='weekly'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']==$day){
				/*update old enddate */
				delete_attendance_booking($sid,$oldbookid,$newenddate);
				}
			}
		elseif($dayrepeat=='every'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']=='%'){
				delete_attendance_booking($sid,$oldbookid,$newenddate);
				}
			}
		}


	mysql_query("INSERT INTO attendance_booking (student_id,status,session,code,day,startdate,enddate,comment) 
					 VALUES ('$sid','a','$attsession','$code','$day','$startdate','$enddate','$comment');");


	return;
	}



/**
 *
 * Will delete the booking specified by @bookid.
 * With an endate given the booking won't be deleted but merely set to end at that date.
 *
 * @param integer sid
 * @param integer bookid
 * @param date newenddate
 *
 */
function delete_attendance_booking($sid,$bookid,$newenddate=''){

	if($newenddate==''){
		mysql_query("DELETE FROM attendance_booking WHERE student_id='$sid' AND id='$bookid' LIMIT 1;");
		}
	else{
		mysql_query("UPDATE attendance_booking SET enddate='$newenddate' WHERE id='$bookid';");
		}

	return;
	}

/**
 *
 *
 * @param integer sid
 * @param integer $eveid
 * @return array
 *
 */
function fetchbookedAttendance($sid,$eveid=''){
	if($eveid==''){
		$secid=get_student_section($sid);
		$event=get_currentevent($secid);
		$eveid=$event['id'];
		}
	$day=date('N',strtotime($event['date']));

	$Attendance=array();

	$bookings=(array)list_student_attendance_bookings($sid,$event['date'],$day,$event['session']);

	if($eveid!=''){

		/* The bookings must be sorted so that the first is always the highest priority. */
		if(sizeof($bookings)>0){
			$a=(array)$bookings[0];
			$Attendance['id_db']=$a['id'];
			$Attendance['Date']=array('label'=>'date', 
									  'value'=>''.$event['date']);
			$Attendance['Session']=array('label'=>'session',
										 'value'=>''.$a['session']);
			$Attendance['Period']=array('label'=>'period',
										'value'=>'');
			$Attendance['Status']=array('label'=>'attendance',
										'value'=>''.$a['status']);
			$Attendance['Code']=array('label'=>'code',
									  'value'=>''.$a['code']);
			$Attendance['Late']=array('label'=>'late',
									  'value'=>'');
			$Attendance['Comment']=array('label'=>'comment',
										 'value'=>''.$a['comment']);
			$Attendance['Logtime']=array('label'=>'time',
										 'value'=>'');
			}
		else{
			$Attendance['id_db']=-1;
			}
		}
	return $Attendance;
	}

?>
