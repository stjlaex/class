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
		//trigger_error($name.' '.$day,E_USER_WARNING);
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
		//trigger_error($journeyid,E_USER_WARNING);
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
	$d_s=mysql_query("SELECT * FROM transport_stop AS s JOIN transport_rtidstid AS rs ON rs.stop_id=s.id 
						WHERE rs.route_id=(SELECT route_id FROM transport_bus WHERE transport_bus.id='$busid') 
						ORDER BY rs.sequence ASC;");
	while($stop=mysql_fetch_array($d_s,MYSQL_ASSOC)){
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
	$d_b=mysql_query("SELECT DISTINCT name FROM transport_bus ORDER BY name ASC;");
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
 * @param enum $day
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
						ORDER BY b.startdate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$bookings[]=$b;
		}

	return $bookings;
	}

/**
 * Return a booking
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
	$newend=date('Y-m-d',strtotime($date.' -1 day'));

	if($dayrepeat=='once'){$day=$day;$startdate=$date;$enddate=date('Y-m-d',strtotime($date.' +1 day'));}
	elseif($dayrepeat=='weekly'){$day=$day;$startdate=$date;$enddate='0000-00-00';}
	elseif($dayrepeat=='every'){$day='%';$startdate=$date;$enddate='0000-00-00';}
	$bus=get_bus($busid);
	$direction=$bus['direction'];
	$busname=$bus['name'];

	$d_b=mysql_query("SELECT id, startdate, enddate, day FROM transport_booking 
						WHERE student_id='$sid' AND direction='$direction' 
						AND ((startdate<='$startdate' AND (enddate>='$startdate' or enddate='0000-00-00'))
						OR (startdate>='$startdate' AND (startdate<='$enddate' OR $enddate='0000-00-00')));");
	while($oldb=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$oldbookid=$oldb['id'];
		trigger_error($oldb['id'].' : '.$oldb['startdate'].' : '.$oldb['enddate'].mysql_error(),E_USER_WARNING);
		if($dayrepeat=='once'){
			if($oldb['startdate']==$oldb['enddate'] and $oldb['day']==$day){
				/*delete old*/
				delete_journey_booking($sid,$oldbookid);
				}
			}
		elseif($dayrepeat=='weekly'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']=='%'){
				/*do nothing*/
				}
			elseif($oldb['enddate']=='0000-00-00' and $oldb['day']!='%'){
				/*update old enddate, del com*/
				mysql_query("UPDATE transport_booking SET enddate='$newend' WHERE id='$oldbookid';");
				}
			}
		elseif($dayrepeat=='every'){
			if($oldb['enddate']=='0000-00-00'){
				/*update old enddate, del com*/
				mysql_query("UPDATE transport_booking SET enddate='$newend' WHERE id='$oldbookid';");
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
		//trigger_error($busid.' : '.$stopid.' '.mysql_error(),E_USER_WARNING);
		}

	/* add to the transport community for that bus */
	$com=array('id'=>'','type'=>'transport','name'=>$busname);
	$oldcommunities=set_community_stay($sid,$com,$startdate,$enddate);

	return;
	}


/**
 *
 * @param integer sid
 * @param integer bookid
 * @return
 *
 */
function delete_journey_booking($sid,$bookid){


	$d_j=mysql_query("SELECT journey_id, startdate, enddate FROM transport_booking WHERE transport_booking.id='$bookid';");
	$journeyid=mysql_result($d_j,0,0);
	$startdate=mysql_result($d_j,0,1);
	$enddate=mysql_result($d_j,0,2);
	$todate=date("Y-m-d");

	$d_b=mysql_query("SELECT b.name FROM transport_bus AS b JOIN transport_journey AS j ON
				   j.bus_id=b.id WHERE j.id='$journeyid';");
	$busname=mysql_result($d_b,0);

	mysql_query("DELETE FROM transport_booking WHERE student_id='$sid' AND id='$bookid' LIMIT 1;");

	$d_b=mysql_query("SELECT b.id FROM transport_booking AS b JOIN transport_journey AS j ON b.journey_id=j.id 
						WHERE b.student_id='$sid'  
						AND (b.startdate>='$todate' OR b.enddate='0000-00-00') 
						AND j.bus_id=ANY(SELECT id FROM transport_bus WHERE name='$busname');");
	if(mysql_num_rows($d_b)==0){
		//trigger_error('DELETE!!!!!!!!!!!!!!!' ,E_USER_WARNING);
		//$bus=get_bus($busid);
		$com=array('id'=>'','type'=>'transport','name'=>$busname);
		$oldcommunities=set_community_stay($sid,$com,$startdate,$startdate);
		}

	/*TODO: check for other bookings for this busname and update transport community appropriately */

	//trigger_error($sid.' '.$busname.' '.$startdate.' ' .$enddate,E_USER_WARNING);
	return;
	}
?>
