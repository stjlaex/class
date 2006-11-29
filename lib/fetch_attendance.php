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
	$Attendance['Teacher']=array('label' => 'teacher',
							  'table_db' => 'attendance', 
							  'field_db' => 'teacher_id',
							  'type_db' => 'varchar(14)', 
							  'value' => ''.$attendance['teacer_id']);
	return $Attendance;
	}

function fetchAttendances($sid,$date='',$period='%'){
	$Attendances=array();
	$evetable=array();
	/*if no date set choose this week*/
	if($date==''){
		$date=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-5,date('Y')));
		}
	$d_attendance=mysql_query("SELECT attendance.status,
			attendance.code, attendance.late, attendance.comment, event.id,
			event.period, event.date FROM attendance JOIN
			event ON event.id=attendance.event_id WHERE
			attendance.student_id='$sid' AND event.date > '$date' 
			AND event.period LIKE '$period' ORDER BY event.date");
	$index=0;
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
		$Attendances['Attendance'][]=$Attendance;
		$evetable[$attendance['id']]=$index++;
		}

	$Attendances['evetable']=$evetable;
	return nullCorrect($Attendances);
	}

function fetchAttendanceEvents($date=''){
	$AttendanceEvents=array();
	$evetable=array();
	/*if no date set choose this week*/
	if($date==''){
		$date=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-14,date('Y')));
		}
	$d_event=mysql_query("SELECT id, period, date FROM event WHERE date > '$date' 
								ORDER BY date, period");
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
		//		$evetable[$event['date']][$event['period']]=$index++;
		$evetable[$event['id']]=$index++;
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

function currentEvent(){
	$date=date('Y-m-d');
	$session=date('A');
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
?>
