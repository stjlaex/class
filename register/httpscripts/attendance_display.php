<?php
/**                    httpscripts/attendance_display.php
 *
 */

require_once('../../scripts/http_head_options.php');

$sid=$_GET['sid'];
if(isset($_GET['date'])){$date=$_GET['date'];}
if(isset($_GET['viewbook'])){$viewbook=$_GET['viewbook'];}

$todate=date('Y-m-d');
$today=date('N');
$Student=fetchStudent_short($sid);
	$days=getEnumArray('dayofweek');
	$dates=array();
	foreach($days as $day => $dayname){
		$daydiff=$day-$today;
		$date=date('Y-m-d',strtotime($daydiff.' day'));
		$dates[$day]=$date;
		}

	$html='';
   	$html.='<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown.'</td><td></td>';
   	$html.='<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_transport.php&sid='.$sid.'">'.$Student['Surname']['value'].', '. $Student['Forename']['value'].'</a></td>';
   	$html.='<td>'.$Student['RegistrationGroup']['value'].'</td>';
	foreach($days as $day=>$dayname){
		$bookings=array();
		$ambookings=(array)list_student_attendance_bookings($sid,$dates[$day],$day,'AM');
		$pmbookings=(array)list_student_attendance_bookings($sid,$dates[$day],$day,'PM');
		$bookings=array_merge($ambookings,$pmbookings);
		$divam='';$divpm='';
		$openId=$sid.'-'.$day;
		foreach($bookings as $b){
			if($b['session']=='AM'){$divname='divam';$divclass='pauselite';}
			else{$divname='divpm';$divclass='pauselite';}
			if($$divname==''){
				$divaction='onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\''.$b['id'].'\',\''.$openId.'\',\''.$viewbook.'\');"';
				if($b['comment']!=''){$$divname='<span title="'.$b['comment'].'">';}
				$$divname.='<div '.$divaction.' class="'.$divclass.' center" style="text-align:center;font-weight:600;">'.$b['code'].'</div>';
				if($b['comment']!=''){$$divname.='</span>';}
				}
			}
		if($divam==''){$divam='<div onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\',\''.$viewbook.'\');" class="lowlite">'.'ADD'.'</div>';}
		if($divpm==''){$divpm='<div onClick="clickToEditAttendance('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\',\''.$viewbook.'\');" class="lowlite">'.'ADD'.'</div>';}
		$html.='<td class="clicktoaction">'.$divam . $divpm.'</td>';
		}

$returnText=$html;
require_once('../../scripts/http_end_options.php');
exit;
?>
