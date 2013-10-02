<?php
/**                    httpscripts/meals_editor_action.php
 *
 */

require_once('../../scripts/http_head_options.php');

/*Variables*/
$sub=$_GET['sub'];
$sid=$_GET['sid'];
if(isset($_GET['openid'])){$openerId=$_GET['openid'];}
if(isset($_GET['sid'])){$sid=$_GET['sid'];}
if(isset($_POST['date'])){$date=$_POST['date'];}else{$date='';}
if(isset($_GET['date'])){$date=$_GET['date'];}
if(isset($_GET['new_comment'])){$new_comment=$_GET['new_comment'];}
if(isset($_GET['booking_id'])){$booking_id=$_GET['booking_id'];}
if(isset($_GET['mealid'])){$mealid=$_GET['mealid'];}
if(isset($_GET['day'])){$day=$_GET['day'];}
if(isset($_GET['everyday'])){$everyday=$_GET['everyday'];}else{$everyday='no';}
if(isset($_GET['everydaychange'])){$everychange=$_GET['everydaychange'];}else{$everydaychange='no';}
if(isset($_GET['allselect'])){$allselect=$_GET['allselect'];}else{$allselect=false;}
if(isset($_GET['addcomment'])){$addcomment=$_GET['addcomment'];}else{$addcomment=false;}
if(isset($_GET['sids'])){$sids=$_GET['sids'];}else{$sids='';}



/*Get all the bookings for a student*/
$bookings=get_student_booking($sid,$date,'%');

if($addcomment){
	add_booking_comment($sid,$booking_id,$new_comment);
	}

if($allselect){
	foreach($sids as $sid){
		delete_bookings($sid,$date);
		add_meal_booking($sid,$mealid,'every',$date);
		}
	}

/*It is not an everyday check/uncheck option*/
if($everyday=='no' and !$allselect){
	/*It will add or modify a booking*/
	if($sub=='Submit'){
		add_meal_booking($sid,$mealid,'once',$date);
		foreach($bookings as $booking){
			/*If it finds another simple booking on same day deletes that one and add the new one*/
			if($booking['enddate']!='0000-00-00' and $booking['meal_id']!=$mealid and $booking['startdate']==$date){
				delete_booking_all($sid,$date,$booking['meal_id'],$day);
				add_meal_booking($sid,$mealid,'once',$date,$booking['comment']);
				}
			/*If it finds an everyday booking and the wanted booking is part of that booking*/
			if($booking['enddate']=='0000-00-00' and $booking['meal_id']!=$mealid and $booking['startdate']<=$date){
				/*deletes the everyday booking*/
				delete_booking_all($sid,$date,$booking['meal_id'],'%');
				$startdate=$booking['startdate'];
				$deleteddate=$date;
				$diff=floor(abs(strtotime($deleteddate)-strtotime($startdate))/86400);
				for($i=0;$i<$diff;$i++){
					$d=date('Y-m-d',strtotime($startdate.' +'.$i.' day'));
					/*from startdate to modified date -1 it adds single bookings*/
					if(date('N',strtotime($d))!=6 and date('N',strtotime($d))!=7 and date('Y-m-d',strtotime($d))<$date){add_meal_booking($sid,$booking['meal_id'],'once',$d,'falsus');}
					}
				$everystart=date('Y-m-d',strtotime($deleteddate.' +1 day'));
				/*from modified date +1 it adds an everyday option*/
				add_meal_booking($sid,$booking['meal_id'],'every',$everystart,$booking['comment']);
				/*adds the modified booking as single booking*/
				add_meal_booking($sid,$mealid,'once',$date);
				}
			}
		}
	/*Will delete a day of booking*/
	elseif($sub=='Delete'){
		foreach($bookings as $booking){
			/*if wanted booking is a single choice it simply deletes it*/
			if($booking['enddate']!='0000-00-00' and $booking['meal_id']==$mealid and $booking['startdate']==$date){
				delete_booking_all($sid,$date,$mealid,$day);
				}
			/*If the wanted booking is part of an everyday option*/
			if($booking['enddate']=='0000-00-00' and $booking['meal_id']==$mealid and $booking['startdate']<=$date){
				/*deletes the everyday option*/
				delete_booking_all($sid,$date,$mealid,'%');
				$startdate=$booking['startdate'];
				$deleteddate=$date;
				$diff=floor(abs(strtotime($deleteddate)-strtotime($startdate))/86400);
				for($i=0;$i<$diff;$i++){
					$d=date('Y-m-d',strtotime($startdate.' +'.$i.' day'));
					/*from startdate to date-1 it adds single bookings*/
					if(date('N',strtotime($d))!=6 and date('N',strtotime($d))!=7 and date('Y-m-d',strtotime($d))<$date){add_meal_booking($sid,$mealid,'once',$d,$booking['comment']);}
					}
				$everystart=date('Y-m-d',strtotime($deleteddate.' +1 day'));
				/*and from date+1 it adds an everyday option*/
				add_meal_booking($sid,$mealid,'every',$everystart,$booking['comment']);
				}
			}
		}
	}
/*If it is an everyday option*/
if($everyday=='yes' and !$allselect){
	/* to delete*/
	if($sub=='Delete'){
		/*if the user want to remove the everyday option*/
		if($everychange=='yes'){
			/*deletes all bookings*/
			delete_bookings($sid,$date);
			}
		foreach($bookings as $booking){
			if($booking['enddate']=='0000-00-00' and $booking['meal_id']==$mealid){
				$startdate=$booking['startdate'];
				$deleteddate=$date;
				$diff=floor(abs(strtotime($deleteddate)-strtotime($startdate))/86400);
				/*if it is only modified*/
				if($everychange=='modify'){
					delete_booking_all($sid,$startdate,$mealid,'%');
					for($i=0;$i<$diff;$i++){
						$d=date('Y-m-d',strtotime($startdate.' +'.$i.' day'));
						/*adds single bookings*/
						if(date('N',strtotime($d))!=6 and date('N',strtotime($d))!=7){add_meal_booking($sid,$mealid,'once',$d,$booking['comment']);}
						}
					$everystart=date('Y-m-d',strtotime($deleteddate.' +1 day'));
					/*adds an everyday booking*/
					add_meal_booking($sid,$mealid,'every',$everystart,$booking['comment']);
					}
				}
			}
		}
	/*if the everyday option is checked*/
	if($sub=='Submit'){
		/*deletes all bookings and adds an everyday booking*/
		delete_bookings($sid,$date);
		add_meal_booking($sid,$mealid,'every',$date);
		}
	}
$bs=get_student_booking($sid,$date,$day);
print $bs[0]['bookingid'];
?>
