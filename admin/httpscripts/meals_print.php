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
		if($mealname!='' and $type=='m'){
			/*get info and the students for the meal (mealname=id)*/
			$meal=(array)get_meal($mealname);
			$display_name=$meal['name'];
			$students=(array)list_meals_students($meal['name'],$printdate,0);
			}
		elseif($mealname!='' and $type=='f'){
			/*get the community and the students for a formgroup id (id=mealname)*/
			$com=get_community($mealname);
			$display_name=$com['name'];
			$students=(array)listin_community($com);
			$meal='';
			}

		if(sizeof($students)>0){
			$Meals=array();
			$Meals['Name']=array('value'=>$display_name);
			$Meals['Type']=array('value'=>$type);
			$Meals['Day']=array('value'=>get_string(displayEnum($day,'dayofweek'),'admin'));
			$Meals['Date']=array('value'=>display_date($printdate));

			$Meals['Student']=array();

			$clubcommunity=array('id'=>'','name'=>'','type'=>'tutor');

			foreach($students as $student){
				$sid=$student['id'];
				$Student=(array)fetchStudent_short($sid);
				$communities=(array)list_member_communities($sid,$clubcommunity);
				foreach($communities as $club){
					$pos=strpos($club['sessions'],"A$day");
					if($pos!==false){
						$Student['Club']['value']=$club['name'];
						}
					}

				$Student['Journey']=array();
				$bookings=(array)get_student_booking($sid,$printdate,$day);
				$absence=false;
				/*fetch the attendance so it will not display the absents*/
				$attendances[$sid]=(array)fetchAttendances($sid,$attday,1);
				/*in case the student's attendance is not defined the script stores it as present*/
				if(!is_array($attendances)){$attendances[$sid]['attendance']['Status']['value']='p';}
				if(is_array($attendances)){
					foreach($attendances as $stdid=>$std){
						foreach($std as $attendance){
							if(!($attendances[$sid]['attendance']['Status'])){$attendances[$sid]['attendance']['Status']['value']='p';}
							foreach($attendance as $att){
								/*student is absent when: is not present and is absent but not late*/
								if(($att['Status']['value']!='p' and ($att['Status']['value']=='a' and ($att['Code']['value']!='L' and $att['Code']['value']!='UA' and $att['Code']['value']!='UB' and $att['Code']['value']!='U'))) and $att['Date']['value']==$printdate and $stdid==$sid){$absence=true;}
								if($att['Status']['value']!='p' and $att['Status']['value']!='a'){$attendances[$sid]['attendance']['Status']['value']='p';}
								}
							}
						}
					}
				$atlunch=false;
				$mealid='';
				foreach($bookings as $booking){
					if($meal!=''){$mealid=$meal['id'];}
					if($mealid==$booking['meal_id']){$atlunch=true;}
					if($booking['student_id']==$sid and !$absence){
						/*Stores all details for the meal journey*/
						$Journey=array();
						$Journey['Id']=$booking['meal_id'];
						$Journey['Day']=$booking['day'];
						$Journey['Date']=$booking['startdate'];
						$Journey['Detail']=$booking['detail'];
						$Journey['Time']=$meals[$booking['meal_id']]['time'];
						$Journey['Comment']=$booking['comment'];
						$Journey['Meal']=array('id_db'=>$booking['meal_id'],
											  'value'=>$meals[$booking['meal_id']]['name']
											  );
						$Student['Journey'][]=$Journey;
						$Student['Attendances']=$attendances[$sid];
						}
					}
				/* Only include the student in the list if they have a journey of some sort for this day. */
				/* If the list is for a form then include all */
				if(($atlunch or $type=='f') and $Student['EnrolmentStatus']['value']=='C'){
					$Meals['Student'][]=$Student;
					}
				
				}
			$Students['Meals'][]=$Meals;
			}
		}

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
