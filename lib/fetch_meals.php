<?php	
/**										lib/fetch_meals.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2013
 *
 **/

/**
 *
 * Returns the mealname and info for given @mealid from the database
 *
 * @param integer $mealid
 * @param string $name
 * @param enum $day
 * @return string
 *
 **/
function get_meal($mealid,$name='',$day=''){
	$meal=array();
	if($name!='' and $day!=''){
		$d_m=mysql_query("SELECT id, name, detail, time, type
							FROM meals_list WHERE name='$name' AND (day='$day' OR day='%');");
		$meal=mysql_fetch_array($d_m,MYSQL_ASSOC);
		}
	elseif($mealid!=' ' and $mealid!='' and $mealid!=-1){
		$d_m=mysql_query("SELECT name, detail,  time, type 
							FROM meals_list WHERE id='$mealid';");
		$meal=mysql_fetch_array($d_m,MYSQL_ASSOC);
		}
	return $meal;
	}

/**
 *
 * Returns the full details of a booking and its associated meal for a given @studentid
 *
 * @param integer $studentid
 * @param date $date
 * @param enum $day
 * @return array
 *
 **/
function get_student_booking($studentid,$date,$day='%'){
	$bookings=array();
	if($studentid!=' ' and $studentid!=''){
		if($date=='0000-00-00'){
			$d_b=mysql_query("SELECT l.id, l.name, l.detail, l.time, l.type, 
							b.id AS bookingid, b.meal_id, b.student_id, b.comment, b.day, b.startdate, b.enddate
							FROM meals_list AS l JOIN meals_booking AS b 
							ON l.id=b.meal_id WHERE b.student_id='$studentid' AND b.enddate='0000-00-00' AND b.day='%';");
			}
		else{
			$d_b=mysql_query("SELECT l.id, l.name, l.detail, l.time, l.type, 
							b.id AS bookingid, b.meal_id, b.student_id, b.comment, b.day, b.startdate, b.enddate
							FROM meals_list AS l JOIN meals_booking AS b 
							ON l.id=b.meal_id WHERE b.student_id='$studentid' AND b.startdate<='$date' 
							AND (b.enddate>='$date' OR b.enddate='0000-00-00') AND (b.day LIKE '$day' OR b.day='%') 
							ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
			}
		while($bookings[]=mysql_fetch_array($d_b,MYSQL_ASSOC)){}
		}
	return $bookings;
	}

/**
 *
 * Returns a list of meals
 *
 * @param enum $day
 * @param string $name
 * @return array
 *
 **/
function list_meals($day='%',$name='%'){
	$meals=array();
	$d_m=mysql_query("SELECT id,name,detail,type,time
						FROM meals_list WHERE (day LIKE '$day' OR day='%') 
							AND name LIKE '$name' ORDER BY id ASC;");
	while($meal=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$meals[$meal['id']]=$meal;
		}

	return $meals;
	}

/**
 *
 * Returns a list of unique mealnames
 *
 * @return array
 *
 **/
function list_mealsnames(){
	$meals=array();
	$d_m=mysql_query("SELECT DISTINCT name FROM meals_list ORDER BY id ASC;");
	while($meal=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$meals[]=$meal;
		}
	return $meals;
	}

/**
 *
 * Returns the info of a meal for a given @mealid
 *
 * @param integer $mealid
 * @return array
 *
 **/
function fetchMeal($mealid='-1'){
	$d_m=mysql_query("SELECT * FROM meals_list WHERE id='$mealid';");
	$m=mysql_fetch_array($d_m,MYSQL_ASSOC);
	$Meal=array();
	$Meal['id_db']=$mealid;
	$Meal['Name']=array('label' => 'name', 
					   'inputtype'=> 'required',
					   'table_db' => 'meals_list', 
					   'field_db' => 'name',
					   'type_db' => 'varchar(30)', 
					   'value' => ''.$m['name']
					   );
	$Meal['Detail']=array('label' => 'detail', 
						 //'inputtype'=> 'required',
					   'table_db' => 'meals_list', 
					   'field_db' => 'detail',
					   'type_db' => 'text', 
					   'value' => ''.$m['detail']
					   );
	$Meal['Comment']=array('label' => 'comment', 
					   'inputtype'=> 'required',
					   'table_db' => 'meals_booking', 
					   'field_db' => 'comment',
					   'type_db' => 'text', 
					   'value' => ''.$m['comment']
					   );
	$Meal['Time']=array('label' => 'time', 
					   //'inputtype'=> 'required',
					   'table_db' => 'meals_list', 
					   'field_db' => 'time',
					   'type_db' => 'time', 
					   'value' => ''.$m['time']
					   );
	return $Meal;
	}

/**
 *
 * Returns all meal bookings for a given @mealname on a given @date
 *
 * @param string $mealname
 * @param date $date
 * @param enum $dayno
 * 
 * @return array
 *
 **/
function list_meals_students($mealname,$date='',$dayno=1){

	if($date==''){$date=date('Y-m-d');}
	list($y,$m,$d)=explode('-',$date);
	$date1=date('Y-m-d',mktime(0, 0, 0, $m, $d-$dayno, $y));
	$date2=date('Y-m-d',mktime(0, 0, 0, $m, $d+$dayno, $y));

	$d_student=mysql_query("SELECT DISTINCT student.id, surname,
				forename, preferredforename, form_id, gender, dob FROM student 
				JOIN meals_booking AS b ON b.student_id=student.id 
				WHERE NOT((b.enddate<'$date1' AND b.enddate!='0000-00-00') 
						OR (b.startdate>'$date2' AND b.startdate!='0000-00-00')) 
						AND b.meal_id=ANY(SELECT meals_booking.meal_id FROM meals_booking 
						JOIN meals_list ON meals_list.id=meals_booking.meal_id WHERE meals_list.name='$mealname')
						ORDER BY surname, forename;");

	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}

/**
 *
 * Returns number of bookings for a given @mealname on a given @date
 *
 * @param string $mealname
 * @param date $date
 * @param enum $dayno
 * 
 * @return array
 *
 **/
function count_meals_students($mealname,$date='',$dayno=1){
	if($date==''){$date=date('Y-m-d');}
	list($y,$m,$d)=explode('-',$date);
	$date1=date('Y-m-d',mktime(0, 0, 0, $m, $d-$dayno, $y));
	$date2=date('Y-m-d',mktime(0, 0, 0, $m, $d+$dayno, $y));

	$d_b=mysql_query("SELECT COUNT(DISTINCT student_id) FROM meals_booking 
						WHERE (meals_booking.startdate>='$date' OR (meals_booking.enddate='0000-00-00' AND meals_booking.startdate<='$date'))
						AND meals_booking.meal_id=ANY(SELECT meals_booking.meal_id 
							FROM meals_booking JOIN meals_list 
							ON meals_booking.meal_id=meals_list.id 
							WHERE meals_list.name='$mealname');");

	$no=mysql_result($d_b,0);

	return $no;
	}

/**
 *
 * Returns a single booking and its associated info for a given @bookid.
 * 
 * @param integer $bookid
 * @return array
 *
 **/
function get_meal_booking($bookid){
	$booking=array();

	/* The most recent (specific) date takes precedence so only use the first two returned */
	$d_b=mysql_query("SELECT b.id, b.meal_id, b.student_id, b.startdate, b.comment, b.enddate, b.day 
						FROM meals_booking AS b JOIN meals_list AS l ON b.meal_id=l.id 
						WHERE b.id='$bookid';");
	$booking=mysql_fetch_array($d_b,MYSQL_ASSOC);

	return $booking;
	}

/**
 *
 * Adds a booking for a given @mealid and a given @sid
 *
 * @param integer $sid
 * @param integer $mealid
 * @param string $dayrepeat
 * @param date $date
 * @param string $comment
 * @return
 *
 **/
function add_meal_booking($sid,$mealid,$dayrepeat='once',$date='',$comment=''){
	if($date==''){$date=date('Y-m-d');}
	$mealids=array();
	$day=date('N',strtotime($date));
	$newenddate=date('Y-m-d',strtotime($date.' -1 day'));

	if($dayrepeat=='once'){$day=$day;$startdate=$date;$enddate=date('Y-m-d',strtotime($date.' +1 day'));}
	elseif($dayrepeat=='every'){$day='%';$startdate=$date;$enddate='0000-00-00';}

	$meal=get_meal($mealid);
	$mealname=$meal['name'];

	/* Find existing bookings which conflict with the new one and delete. */
	$d_b=mysql_query("SELECT id, startdate, enddate, day FROM meals_booking 
						WHERE student_id='$sid' AND ((startdate<='$startdate' 
						AND (enddate>='$startdate' or enddate='0000-00-00'))
						OR (startdate>='$startdate' AND (startdate<='$enddate' OR $enddate='0000-00-00')));");
	while($oldb=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$oldbookid=$oldb['id'];
		if($dayrepeat=='once'){
			if($oldb['startdate']==$startdate and $oldb['enddate']==$enddate and $oldb['day']==$day){
				/*delete old*/
				delete_booking($sid,$oldbookid);
				}
			}
		elseif($dayrepeat=='every'){
			if($oldb['enddate']=='0000-00-00' and $oldb['day']=='%'){
				delete_booking($sid,$oldbookid,$newenddate);
				}
			}
		}

	$meals=array();
	if($dayrepeat=='once' and $meal['day']!='%'){$meals=(array)list_meals('%',$mealname);}
	else{$meals[$mealid]=$meal;$mealday=$day;}

	foreach($meals as $mealid => $meal){
		if(!isset($mealday)){$bookday=$meal['day'];}
		else{$bookday=$mealday;}
		mysql_query("INSERT INTO meals_booking (student_id,meal_id,day,startdate,enddate,comment) 
					 VALUES ('$sid','$mealid','$day','$startdate','$enddate','$comment');");
		}

	return;
	}

/**
 *
 * Will delete the booking specified by @bookid.
 *
 * @param integer $sid
 * @param integer $bookid
 * @param date $newenddate
 * @return
 *
 **/
function delete_booking($sid,$bookid,$newenddate=''){
	if($newenddate==''){
		mysql_query("DELETE FROM meals_booking WHERE student_id='$sid' AND id='$bookid' LIMIT 1;");
		}
	else{
		mysql_query("UPDATE meals_booking SET enddate='$newenddate' WHERE id='$bookid';");
		}

	return;
	}

/**
 *
 * Search for bookings and deletes them.
 *
 * @param integer $sid
 * @param date $date
 * @param integer $mealid
 * @param enum $day
 *
 **/
function delete_booking_all($sid,$date='',$mealid='',$day=''){

	if($date==''){$date=date("Y-m-d");}
	/*TODO: Select for blanks variables*/
	$d_b=mysql_query("SELECT b.id, b.meal_id, b.startdate, b.enddate, b.comment, b.day 
						FROM meals_booking AS b JOIN meals_list AS l ON b.meal_id=l.id 
						WHERE b.student_id='$sid' AND b.meal_id='$mealid' AND b.day='$day'
						AND ((b.startdate>='$date' AND (b.enddate>='$date' OR b.enddate='0000-00-00')) 
						OR (b.startdate<='$date' AND b.enddate='0000-00-00'))
						ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		delete_booking($sid,$b['id']);
		}

	}

/**
 *
 * Search and delete all meals bookings for given @sid.
 *
 * @param integer $sid
 * @param date $date
 *
 **/
function delete_bookings($sid,$date){
	$d_b=mysql_query("SELECT b.id, b.meal_id, b.startdate, b.enddate, b.day 
						FROM meals_booking AS b 
						WHERE b.student_id='$sid' AND ((b.startdate>='$date' AND (b.enddate>='$date' OR b.enddate='0000-00-00')) 
						OR (b.startdate<='$date' AND b.enddate='0000-00-00'))
						ORDER BY b.startdate DESC, b.enddate DESC, b.day ASC;");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		delete_booking($sid,$b['id']);
		}

	}

/**
 *
 * Gets the comment for a booking
 *
 * @param integer $sid
 * @param date $date
 *
 **/
function get_booking_comments($sid,$mealid='%'){
	$d_c=mysql_query("SELECT * FROM meals_booking AS b 
						WHERE b.student_id='$sid' 
						AND b.meal_id LIKE '$mealid';");
	while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$comments[]=$c;
		}
	return $comments;
	}

/**
 *
 * Adds a comment to a booking
 *
 * @param integer $sid
 * @param date $date
 *
 **/
function add_booking_comment($sid,$bookingid,$newcomment){
	mysql_query("UPDATE meals_booking SET comment='$newcomment' WHERE id='$bookingid' AND student_id='$sid';");
	}

?>
