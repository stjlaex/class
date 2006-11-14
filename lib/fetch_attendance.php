<?php	
/**									 fetch_attendance.php
 *
 */	

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
	$Attendance['Code']=array('label' => 'code',
							  'inputtype'=> 'required',
							  'table_db' => 'attendance', 
							  'field_db' => 'code',
							  'type_db' => 'enum', 
							  'value' => ''.$attendance['code']);
	$Attendance['Comment']=array('label' => 'comment',
							  'inputtype'=> 'required',
							  'table_db' => 'attendance', 
							  'field_db' => 'comment',
							  'type_db' => 'text', 
							  'value' => ''.$attendance['comment']);
	$Attendance['Teacher']=array('label' => 'teacher',
							  'inputtype'=> 'required',
							  'table_db' => 'attendance', 
							  'field_db' => 'teacher_id',
							  'type_db' => 'varchar(14)', 
							  'value' => ''.$attendance['teacer_id']);
	return $Attendance;
	}

function fetchAttendances($sid,$date='',$period='%'){
	$Attendances=array();
	/*if no date set choose this week*/
	if($date==''){
		$today=date('d');
		$tomonth=date('m');
		$toyear=date('Y');
		$date=$toyear.'-'.$tomonth.'-'.$today-7;
		}
	$d_attendance=mysql_query("SELECT attendance.code, event.id,
			event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.date > '$date' 
			AND event.period LIKE '$period' ORDER BY event.date");
	while($attendance=mysql_fetch_array($d_attendance,MYSQL_ASSOC)){
		$Attendance=array();
		$Attendance['id_db']=$attendance['id'];
	   	$Attendance['Period']=array('label' => 'period',
								  'value' => ''.$attendance['period']);
	   	$Attendance['Date']=array('label' => 'date', 
									'type_db'=>'date', 
									'value' => ''.$attendance['date']);
	   	$Attendance['Code']=array('label' => 'period',
								  'value' => ''.$attendance['code']);
		$Attendances['Attendance'][]=$Attendance;
		}

	return nullCorrect($Attendances);
	}

function fetchAttendanceEvents($date=''){
	$AttendanceEvents=array();
	$evetable=array();
	$index=0;
	/*if no date set choose this week*/
	if($date==''){
		$today=date('d');
		$tomonth=date('m');
		$toyear=date('Y');
		$date=$toyear.'-'.$tomonth.'-'.$today-7;
		}
	$d_event=mysql_query("SELECT id,
			period, date FROM event WHERE date > '$date' 
			ORDER BY date, period");
	while($event=mysql_fetch_array($d_event,MYSQL_ASSOC)){
		$Event=array();
		$Event['id_db']=$event['id'];
	   	$Event['Period']=array('label' => 'period',
								  'value' => ''.$event['period']);
	   	$Event['Date']=array('label' => 'date', 
									'type_db'=>'date', 
									'value' => ''.$event['date']);
		$AttendanceEvents['Event'][]=$Event;
		$evetable[$event['date']][$event['period']]=$index++;
		}
	$AttendanceEvents['evetable']=$evetable;

	return nullCorrect($AttendanceEvents);
	}

/*used to decorate the student's name with attendance status*/
function attendanceDisplay($sid,$Attendances=''){
	$display=array();
	if($Attendances==''){
		$Attendances=fetchAttendances($sid);
		}
	if(is_array($Attendances['Attendance'])){
		if($Attendances['Attendance'][0]['Code']['value']!='P'){
			$display['class']='positive';
			}
		else{$display['class']='negative';}
		$header=$Attendances['Attendance'][0]['Period']['value']. 
				' ('.$Attendances['Attendance'][0]['EntryDate']['value'].')';
		$display['body']=$header.'<br />'.$Attendances['Attendance'][0]['Comment']['value'];
		}
	else{$display['class']='';$display['body']='';}

	return $display;
	}
?>
