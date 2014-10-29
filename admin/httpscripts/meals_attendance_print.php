<?php
/**									Meals_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

/*NB. The mealnames are pulled by checksidsAction hence they honestly really are called sids!*/
if(isset($_GET['sids'])){$mealnames=(array)$_GET['sids'];}else{$mealnames=array();}
if(isset($_POST['sids'])){$mealnames=(array)$_POST['sids'];}
if((isset($_POST['date0']) and $_POST['date0']!='')){$printdate=$_POST['date0'];}else{$printdate=date('Y-m-d');}
if((isset($_GET['date0']) and $_GET['date0']!='')){$printdate=$_GET['date0'];}
if((isset($_POST['length']) and $_POST['length']!='')){$length=$_POST['length'];}else{$length='full';}
if((isset($_GET['length']) and $_GET['length']!='')){$length=$_GET['length'];}

/*day in number*/
$day=date('N',strtotime($printdate));
/* calculate difference in days from now for past attendance */
$d=explode('-',$printdate);
$diff=mktime(0,0,0,date('m'),date('d'),date('Y'))-mktime(0,0,0,$d[1],$d[2],$d[0]);
$attday=-round($diff/(60*60*24));

/*all the meals*/
$meals=list_meals();

if(sizeof($mealnames)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$Students=array();
	$Students['Meals']=array();

	/*Detects the type of list is going to print (m-all students for a meal, f-a form group with every meal)*/
	foreach($mealnames as $typemealname){
		list($type,$mealname)=explode('-',$typemealname);
		if($mealname!='' and $type=='f'){
			/*get the community and the students for a formgroup id (id=mealname)*/
			$com=get_community($mealname);
			$display_name=$com['name'];
			$students=(array)listin_community($com);
			$meal='';
			$template='register_meals_month_print';
			}
		if(sizeof($students)>0){
			$MealsAttendance=array();
			$MealsAttendance['FormGroup']=array('value'=>$display_name);
			$MealsAttendance['Type']=array('value'=>$type);
			$MealsAttendance['Day']=array('value'=>get_string(displayEnum($day,'dayofweek'),'admin'));
			$MealsAttendance['Date']=array('value'=>display_date($printdate));

			$MealsAttendance['Student']=array();
			$MealsAttendance['selectname']='date0';
			$MealsAttendance['Paper']='landscape';

			$startdate=date('Y-m-01',strtotime($printdate));
			$enddate=date('Y-m-t',strtotime($printdate));

			$dates=array();
			$weekno=1;
			for($d=$startdate;$d<=$enddate;$d=date('Y-m-d',strtotime($d.' +1 day'))){
				if(date('l',strtotime($d))!='Saturday' and date('l',strtotime($d))!='Sunday'){
					$Date['value']=$d;
					$Date['day']=date('d',strtotime($d));
					$Date['dayno']=date('N',strtotime($d));
					$Date['week']=$weekno;
					$dates['Date'][]=$Date;
					}
				if(date('l',strtotime($d))=='Saturday'){$weekno++;}
				}

			foreach($students as $student){
				$sid=$student['id'];
				$Student=(array)fetchStudent_short($sid);
				$Attendances=array();
				$d_ma=mysql_query("SELECT date, session, booking_id, event_id, status, meals_attendance.comment,
										  meal_id, meals_booking.day, name
									FROM event JOIN meals_attendance ON meals_attendance.event_id=event.id 
											 JOIN meals_booking ON meals_booking.id=meals_attendance.booking_id 
											 JOIN meals_list ON meals_list.id=meal_id
									WHERE student_id='$sid' AND period='lunch' AND date>='$startdate' AND date<='$enddate';");
				while($mattendance=mysql_fetch_array($d_ma,MYSQL_ASSOC)){
					$Attendance=array();
					$Attendance['Status']['value']=$mattendance['status'];
					$Attendance['Comment']['value']=$mattendance['comment'];
					$Attendance['Event']['id_db']=$mattendance['event_id'];
					$Attendance['Event']['Date']['value']=$mattendance['date'];
					$Attendance['Event']['Session']['value']=$mattendance['session'];
					$Attendance['Meal']['booking_id']=$mattendance['booking_id'];
					$Attendance['Meal']['meal_id']=$mattendance['meal_id'];
					$Attendance['Meal']['Name']['value']=$mattendance['name'];
					$Attendance['Meal']['Day']['value']=$mattendance['day'];
					$Attendances['Attendance'][]=$Attendance;
					}

				$Student['Attendances']=$Attendances;
				$Students['Student'][]=$Student;
				}
			$Students['MealsAttendance'][]=$MealsAttendance;
			$Students['Transform']=$template;
			$Students['Dates']=$dates;
			}
		}
		
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
