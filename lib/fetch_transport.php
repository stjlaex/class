<?php	
/**								 lib/fetch_transport.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2010
 *	@version	
 *	@since		
 *
 */


/**
 * Returns the busname for that busid from the database
 *
 * @param integer $busid
 * @return string
 */
function get_bus($busid,$name='',$direction='',$day=''){
	$bus=array();
	if($name!='' and $direction!='' and $day!=''){
		$d_b=mysql_query("SELECT id, name, route_id, direction, day, departuretime, teacher_id, detail 
							FROM transport_bus WHERE name='$name' AND direction='$direction' 
							AND (day='$day' OR day='%');");
		$bus=mysql_fetch_array($d_b,MYSQL_ASSOC);
		}
	elseif($busid!=' ' and $busid!='' and $busid!=-1){
		$d_b=mysql_query("SELECT id, name, route_id, direction, day, departuretime, teacher_id, detail 
							FROM transport_bus WHERE id='$busid';");
		$bus=mysql_fetch_array($d_b,MYSQL_ASSOC);
		//$name=mysql_result($d_b,0);
		}
	return $bus;
	}


/**
 * Returns the full details of a journey and its associated bus for that journeyid
 *
 * @param integer $journeyid
 * @return array
 */
function get_journey($journeyid){
	$journey=array();
	if($journeyid!=' ' and $journeyid!=''){
		$d_j=mysql_query("SELECT j.id, j.bus_id, j.stop_id, b.name AS busname, b.direction AS direction, 
							b.route_id AS routeid FROM transport_journey AS j 
							JOIN transport_bus AS b ON j.bus_id=b.id WHERE j.id='$journeyid';");
		$journey=mysql_fetch_array($d_j,MYSQL_ASSOC);
		}
	else{
		}
	return $journey;
	}



/**
 * Returns a list of buses
 *
 * @param string $direction
 * @param string $day
 * @param string $name
 * @return array
 *
 */
function list_buses($direction='%',$day='%',$name='%'){
	$buses=array();
	$d_b=mysql_query("SELECT id,name,detail,route_id,direction,day,departuretime 
						FROM transport_bus WHERE direction LIKE '$direction' AND (day LIKE '$day' OR day='%') 
							AND name LIKE '$name' ORDER BY name ASC;");
	while($bus=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$buses[$bus['id']]=$bus;
		}

	return $buses;
	}


/**
 * Returns a list of stops for the given busid
 *
 * @param integer $busid
 * @return array
 *
 */
function list_bus_stops($busid){
	$stops=array();

	$bus=(array)get_bus($busid);
	list($h,$m,$s)=explode(':',$bus['departuretime']);
	$deptime=$h*3600+$m*60+$s;

	$d_s=mysql_query("SELECT s.id, s.name, s.detail, s.lat, s.lng, rs.sequence, rs.traveltime 
						FROM transport_stop AS s JOIN transport_rtidstid AS rs ON rs.stop_id=s.id 
						WHERE rs.route_id=(SELECT route_id FROM transport_bus WHERE transport_bus.id='$busid') 
						ORDER BY rs.sequence ASC;");
	while($stop=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$deptime+=$stop['traveltime']*60;
		$stop['departuretime']=gmdate('H:i',$deptime);
		$stops[$stop['id']]=$stop;
		}

	return $stops;
	}


/**
 * Returns a list of unique busnames
 *
 * 
 * @return array
 *
 */
function list_busnames(){
	$buses=array();
	$d_b=mysql_query("SELECT DISTINCT name FROM transport_bus WHERE route_id!='0' ORDER BY name ASC;");
	while($bus=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$buses[]=$bus;
		}
	return $buses;
	}



/**
 *
 * 
 * @return array
 */
function fetchBus($busid='-1'){
	$d_b=mysql_query("SELECT * FROM transport_bus WHERE id='$busid';");
	$b=mysql_fetch_array($d_b,MYSQL_ASSOC);
	$Bus=array();
	$Bus['id_db']=$busid;
	$Bus['Name']=array('label' => 'name', 
					   'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'name',
					   'type_db' => 'varchar(30)', 
					   'value' => ''.$b['name']
					   );
	$Bus['Detail']=array('label' => 'detail', 
						 //'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'detail',
					   'type_db' => 'text', 
					   'value' => ''.$b['detail']
					   );
	$Bus['Direction']=array('label' => 'direction', 
					   'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'direction',
					   'type_db' => 'enum', 
					   'value' => ''.$b['direction']
					   );
	$Bus['Day']=array('label' => 'day', 
					   'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'day',
					   'type_db' => 'enum', 
					   'value' => ''.$b['day']
					   );
	$Bus['Time']=array('label' => 'dparturetime', 
					   //'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'departuretime',
					   'type_db' => 'time', 
					   'value' => ''.$b['departuretime']
					   );
	$Bus['Monitor']=array('label' => 'monitor', 
						  //'inputtype'=> 'required',
					   'table_db' => 'transport_bus', 
					   'field_db' => 'teacher_id',
					   'type_db' => 'varchar(14)', 
					   'value' => ''.$b['teacher_id']
					   );
	return $Bus;
	}




/**
 * Returns all journey bookings for a sid on a given date
 *
 * @param integer $sid
 * @param date $date
 * @param enum $dayb
 * @param enum $direction
 * @return array
 *
 */
function list_student_journey_bookings($sid,$date,$day='%',$direction='%'){
	$bookings=array();

	/* The most recent (specific) date takes precedence so only use the first two returned */
	$d_b=mysql_query("SELECT b.id, b.journey_id, b.direction, j.bus_id, j.stop_id, b.startdate, b.enddate, b.day, b.comment 
						FROM transport_journey AS j JOIN transport_booking AS b ON b.journey_id=j.id 
						WHERE b.student_id='$sid' AND b.direction LIKE '$direction' AND b.startdate<='$date' 
						AND (b.enddate>='$date' OR b.enddate='0000-00-00') AND (b.day LIKE '$day' OR b.day='%') 
						ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$bookings[$b['id']]=$b;
		//if($sid==617){trigger_error($sid.' : '.$b['bus_id'].' : '.$b['startdate'].' '.$b['enddate'],E_USER_WARNING);}
		}

	return $bookings;
	}

/**
 * Returns all journey bookings for a sid on a week given start date and end date
 *
 * @param integer $sid
 * @param date $startdate
 * @param date $enddate
 * @param enum $day
 * @param enum $direction
 * @return array
 *
 */
function list_student_journey_week_bookings($sid,$startdate,$enddate='',$day='%',$direction='%'){
	$bookings=array();

	$d_b=mysql_query("SELECT b.id, b.journey_id, b.direction, j.bus_id, j.stop_id, b.startdate, b.enddate, b.day, b.comment 
						FROM transport_journey AS j JOIN transport_booking AS b ON b.journey_id=j.id 
						WHERE b.student_id='$sid' AND (b.startdate>='$startdate' OR (b.startdate<='$startdate' 
						AND (b.enddate>='$startdate' OR b.enddate='0000-00-00')))
						ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$bookings[$b['id']]=$b;
		}

	return $bookings;
	}


/**
 *
 * Returns all journey bookings for a given busname on a given date
 *
 * @param string $busname
 * @param date $date
 * @param enum $dayno
 * 
 * @return array
 *
 */
function list_bus_journey_students($busname,$date='',$dayno=1){

	if($date==''){$date=date('Y-m-d');}
	list($y,$m,$d)=explode('-',$date);
	$date1=date('Y-m-d',mktime(0, 0, 0, $m, $d-$dayno, $y));
	$date2=date('Y-m-d',mktime(0, 0, 0, $m, $d+$dayno, $y));

	$d_student=mysql_query("SELECT DISTINCT student.id, surname,
				forename, preferredforename, form_id, gender, dob FROM student 
				JOIN transport_booking AS b ON b.student_id=student.id 
   				WHERE NOT((b.enddate<'$date1' AND b.enddate!='0000-00-00') 
						OR (b.startdate>'$date2' AND b.startdate!='0000-00-00')) 
						AND b.journey_id=ANY(SELECT transport_journey.id FROM transport_journey 
						JOIN transport_bus ON transport_bus.id=transport_journey.bus_id WHERE transport_bus.name='$busname')
						ORDER BY surname, forename;");

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}




/**
 *
 * Returns all journey bookings for a given busname on a given date
 *
 * @param string $busname
 * @param date $date
 * @param enum $dayno
 * 
 * @return array
 *
 */
function count_bus_journey_students($busname,$direction,$date='',$dayno=1){

	if($date==''){$date=date('Y-m-d');}
	list($y,$m,$d)=explode('-',$date);
	$date1=date('Y-m-d',mktime(0, 0, 0, $m, $d-$dayno, $y));
	$date2=date('Y-m-d',mktime(0, 0, 0, $m, $d+$dayno, $y));

	$d_s=mysql_query("SELECT COUNT(DISTINCT student_id) FROM transport_booking AS b 
						WHERE NOT((b.enddate<'$date1' AND b.enddate!='0000-00-00') 
						OR (b.startdate>'$date2' AND b.startdate!='0000-00-00')) 
						AND b.direction='$direction' AND b.journey_id=ANY(SELECT transport_journey.id FROM transport_journey 
						JOIN transport_bus ON transport_bus.id=transport_journey.bus_id WHERE transport_bus.name='$busname');");

	$no=mysql_result($d_s,0);

	return $no;
	}


/**
 *
 * Returns a single booking and its associated journey for a given booking_id. 
 * 
 * @param integer $bookingid
 * @return array
 *
 */
function get_journey_booking($bookid){
	$booking=array();

	/* The most recent (specific) date takes precedence so only use the first two returned */
	$d_b=mysql_query("SELECT b.id, b.journey_id, b.direction, j.bus_id, j.stop_id, b.startdate, b.enddate, b.day, b.comment 
						FROM transport_booking AS b JOIN transport_journey AS j ON b.journey_id=j.id 
						WHERE b.id='$bookid';");
	$booking=mysql_fetch_array($d_b,MYSQL_ASSOC);

	return $booking;
	}



/**
 *
 * @param array $booking
 * @return
 *
 */
function add_journey_booking($sid,$busid,$stopid,$date='',$dayrepeat='once',$comment=''){

	if($date==''){$date=date('Y-m-d');}
	$busids=array();
	$day=date('N',strtotime($date));
	$newenddate=date('Y-m-d',strtotime($date.' -1 day'));

	if($dayrepeat=='once'){$day=$day;$startdate=$date;$enddate=date('Y-m-d',strtotime($date.' +1 day'));}
	elseif($dayrepeat=='weekly'){$day=$day;$startdate=$date;$enddate='0000-00-00';}
	elseif($dayrepeat=='every'){$day='%';$startdate=$date;$enddate='0000-00-00';}

	$bus=get_bus($busid);
	$direction=$bus['direction'];
	$busname=$bus['name'];

	/* Find existing bookings which conflict with the new one and delete. */
	$d_b=mysql_query("SELECT id, startdate, enddate, day FROM transport_booking 
						WHERE student_id='$sid' AND direction='$direction' 
						AND ((startdate<='$startdate' AND (enddate>='$startdate' or enddate='0000-00-00'))
						OR (startdate>='$startdate' AND (startdate<='$enddate' OR $enddate='0000-00-00')));");
	while($oldb=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$oldbookid=$oldb['id'];
		//trigger_error($oldb['id'].' : '.$oldb['startdate'].' : '.$oldb['enddate'].mysql_error(),E_USER_WARNING);
		if($dayrepeat=='once'){
			if($oldb['startdate']==$startdate and $oldb['enddate']==$enddate and $oldb['day']==$day){
				/*delete old*/
				delete_journey_booking($sid,$oldbookid);
				}
			}
		elseif($dayrepeat=='weekly'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']==$day){
				/*update old enddate */
				delete_journey_booking($sid,$oldbookid,$newenddate);
				}
			}
		elseif($dayrepeat=='every'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']=='%'){
				delete_journey_booking($sid,$oldbookid,$newenddate);
				}
			}
		}

	$buses=array();
	if($dayrepeat=='every' and $bus['day']!='%'){$buses=(array)list_buses($direction,'%',$busname);}
	else{$buses[$busid]=$bus;$busday=$day;}

	foreach($buses as $busid => $bus){
		if(!isset($busday)){$bookday=$bus['day'];}
		else{$bookday=$busday;}
		/* Probably to be moved to adding new bus */
		$d_j=mysql_query("SELECT id FROM transport_journey WHERE bus_id='$busid' AND stop_id='$stopid';");
		if(mysql_num_rows($d_j)>0){
			$jid=mysql_result($d_j,0);
			}
		else{
			$d_j=mysql_query("INSERT INTO transport_journey (bus_id,stop_id) VALUES ('$busid','$stopid');");
			$jid=mysql_insert_id();
			}
		mysql_query("INSERT INTO transport_booking (student_id,journey_id,direction,day,startdate,enddate,comment) 
					 VALUES ('$sid','$jid','$direction','$bookday','$startdate','$enddate','$comment');");
		}


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
function delete_journey_booking($sid,$bookid,$newenddate=''){

	$d_j=mysql_query("SELECT journey_id, startdate, enddate FROM transport_booking WHERE transport_booking.id='$bookid';");
	$journeyid=mysql_result($d_j,0,0);
	$startdate=mysql_result($d_j,0,1);
	$enddate=mysql_result($d_j,0,2);
	$todate=date("Y-m-d");

	$d_b=mysql_query("SELECT b.name FROM transport_bus AS b JOIN transport_journey AS j ON
				   j.bus_id=b.id WHERE j.id='$journeyid';");
	$busname=mysql_result($d_b,0);

	if($newenddate==''){
		mysql_query("DELETE FROM transport_booking WHERE student_id='$sid' AND id='$bookid' LIMIT 1;");
		}
	else{
		mysql_query("UPDATE transport_booking SET enddate='$newenddate' WHERE id='$bookid';");
		}

	return;
	}


/**
 *
 *  Delete all transport bookings for given sid.
 *  Likely only used when a student leaves the school roll.
 *  Works by setting the last date of the booking to today's date.
 *
 *	@param integer sid
 */
function delete_journey_booking_all($sid,$date=''){

	if($date==''){$date=date("Y-m-d");}

	$d_b=mysql_query("SELECT b.id, b.journey_id, b.direction, j.bus_id, j.stop_id, b.startdate, b.enddate, b.day, b.comment 
						FROM transport_journey AS j JOIN transport_booking AS b ON b.journey_id=j.id 
						WHERE b.student_id='$sid' AND b.startdate<='$date' 
						AND (b.enddate>='$date' OR b.enddate='0000-00-00') 
						ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		delete_journey_booking($sid,$b['id'],$date);
		}

	}

/**
 *
 * Returns the dates for a week given a selected date.
 * 
 * @param date $date
 * @return array
 *
 */
function get_week_dates($date){
	$days=getEnumArray('dayofweek');
	$dates=array();
	$startdate=$date;
	$startdayno=date('N',strtotime($startdate));
	$before=1-$startdayno;$after=1;
	foreach($days as $day => $dayname){
		if($day<$startdayno){$daydiff=$before++;}
		elseif($day>$startdayno){$daydiff=$after++;}
		elseif($day==$startdayno){$daydiff=0;}
		$date=date('Y-m-d',strtotime($startdate.' '.$daydiff.' days'));
		$dates[$day]=$date;
		}
	return $dates;
	}

?>
