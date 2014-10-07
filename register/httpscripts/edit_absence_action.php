<?php 
/** 		  							edit_absence_action.php
 */


require_once('../../scripts/http_head_options.php');

if(isset($_POST['colid']) and $_POST['colid']!=''){$columnid=$_POST['colid'];}else{$columnid='';}
if(isset($_POST['sid']) and $_POST['sid']!=''){$sid=$_POST['sid'];}else{$sid='';}
$event=explode('-',$columnid);
if(count($event)>0){$eveid=$event[1];}
else{$eveid=0;}

if(isset($_POST['status']) and $_POST['status']!=''){$instatus=$_POST['status'];}else{$instatus='';}
if(isset($_POST['code-'.$sid]) and $_POST['code-'.$sid]!=''){$incode=$_POST['code-'.$sid];}else{$incode='';}
if(isset($_POST['late-'.$sid]) and $_POST['late-'.$sid]!=''){$inlate=$_POST['late-'.$sid];}else{$inlate='';}
if(isset($_POST['comm-'.$sid]) and $_POST['comm-'.$sid]!=''){$incomm=$_POST['comm-'.$sid];}else{$incomm='';}
if(isset($_POST['date']) and $_POST['date']!=''){$eventdate=$_POST['date'];}else{$eventdate='';}
if(isset($_POST['session']) and $_POST['session']!=''){$eventsession=$_POST['session'];}else{$eventsession='';}
if(isset($_POST['period']) and $_POST['period']!=''){$period=$_POST['period'];}else{$period='';}
$tid=$_SESSION['username'];
$storecid='';

if($eveid==0){
	$d_event=mysql_query("SELECT id FROM event WHERE date='$eventdate' AND session='$eventsession' 
										AND period='$period';");
	if(mysql_num_rows($d_event)==0){
		mysql_query("INSERT INTO event (date,session,period) VALUES ('$eventdate','$eventsession','$period');");
		$eveid=mysql_insert_id();
		}
	else{
		$eveid=mysql_result($d_event,0);
		}
	}

if($period=='lunch' and $instatus!='' and $instatus!='n'){
	$mealname='';
	$mealsnb=get_student_booking($sid,$eventdate,date('w', strtotime($eventdate)));
	if($instatus=='p' and (count($meals)==0 or (count($meals)>0 and count($meals[0])<=1))){add_meal_booking($sid,1,'once',$eventdate);}
	$meals=get_student_booking($sid,$eventdate,date('w', strtotime($eventdate)));
	if(count($meals)>0 and count($meals[0])>0){
		$bookingid=$meals[0]['bookingid'];
		$mealname=$meals[0]['name'];
		if($bookingid!=''){
			$d_m=mysql_query("SELECT * FROM meals_attendance WHERE booking_id='$bookingid' AND event_id='$eveid';");
			if(mysql_num_rows($d_m)==0){
				mysql_query("INSERT INTO meals_attendance SET status='$instatus', comment='$incomm', event_id='$eveid', booking_id='$bookingid';");
				}
			else{
				mysql_query("UPDATE meals_attendance SET status='$instatus', comment='$incomm' WHERE booking_id='$bookingid' AND event_id='$eveid';");
				}
			}
		}
	$attvalue=$instatus;
	$attsession=$eventsession;
	}
else{
	$d_attendance=mysql_query("SELECT status, code, late, comment FROM attendance
									WHERE student_id='$sid' AND event_id='$eveid';");
	if(mysql_num_rows($d_attendance)==0){
		mysql_query("INSERT INTO attendance (event_id,
					student_id, status, code, late, comment, teacher_id, class_id) 
					VALUES ('$eveid','$sid','$instatus','$incode','$inlate','$incomm','$tid','$storecid');");
		}
	else{
		$att=mysql_fetch_array($d_attendance,MYSQL_ASSOC);
		if($att['status']!=$instatus or $att['code']!=$incode or 
		   $att['late']!=$inlate or $att['comment']!=$incomm){
			mysql_query("UPDATE attendance SET status='$instatus', code='$incode', 
						late='$inlate', comment='$incomm', teacher_id='$tid', class_id='$storecid' 
						WHERE event_id='$eveid' AND student_id='$sid';");
			}
		}

	$d_attendance=mysql_query("SELECT status, code, late, comment, logtime, session, date, period FROM attendance
								JOIN event ON event.id=attendance.event_id 
								WHERE student_id='$sid' AND event_id='$eveid';");
	$att=mysql_fetch_array($d_attendance,MYSQL_ASSOC);
	$attcode=$att['code'];
	$attvalue=$att['status'];
	$attlate=$att['late'];
	$atttime=date('H:i',$att['logtime']);
	$attcomm=$att['comment'];
	$attsession=$att['session'];
	}
$onclick="parent.openModalWindow('register.php?current=edit_absence.php&cancel=class_view.php&eveid=$eveid&sid=$sid&colid=$columnid&date=$eventdate&session=$eventsession&period=$period','','');";
$attodds=array('AM'=>'forstroke','PM'=>'backstroke');

if(isset($eveid)){
	if($attvalue=='a' and ($attcode==' ' or $attcode=='O')){
		$out='<span title="? : <br />'. $atttime.' '.$attcomm.'<br />'. $subjectclass.'" >';
		$out.='<img src="images/ostroke.png" /></span>';
		}
	elseif($attvalue=='a' and $attcode!=' ' and $attcode!='O'){
		$des=displayEnum($attcode,'absencecode');
		$des=get_string($des,'register');
		$out='<span title="'.$attcode .': '. $des
				.'<br />'.$atttime.' '.$attcomm.'<br />'. $subjectclass.'" >';
		$out.=$attcode.' &nbsp '.'</span>';
		}
	else{
		$out='<img src="images/'.$attodds[$attsession].'.png" />';
		}
	}
else{
	$attvalue='n';
	$attcode='';
	$attlate='';
	$attcomm='';
	}

if($period=='lunch' and $mealname!=''){
	$out.="<input type='hidden' id='lunch-$sid' value='$mealname'>";
	}

$Student['sid']=$sid;
$Student['colid']=$columnid;
$Student['cellid']=$columnid.'-'.$sid;
$Student['cellparams']['status']=$attvalue;
$Student['cellparams']['late']=$attlate;
$Student['cellparams']['code']=$attcode;
$Student['cellparams']['comm']=$attcomm;
$Student['cellparams']['onclick']=$onclick;
$Student['newval']=$out;
$Students['Student'][]=$Student;


$returnXML=$Students;
$rootName='Students';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
