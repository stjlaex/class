<?php
/**                    httpscripts/transport_display.php
 *
 */

require_once('../../scripts/http_head_options.php');

$sid=$_GET['sid'];
if(isset($_GET['date'])){$date=$_GET['date'];}
if(isset($_GET['viewbook'])){$viewbook=$_GET['viewbook'];}

$todate=date('Y-m-d');
$today=date('N',strtotime($todate));

/* Make use of the passed date to calculate the dates being viewed. */
$startdate=strtotime($date);
$firstmonday=strtotime('first Monday',$startdate);//Monday next week
$datediff=$firstmonday-strtotime($todate)-86400*7;//difference between today and Monday
$weekdiff=round($datediff/(86400*7));// removed +1 but is this correct?
$startday=$weekdiff*7;

$Student=fetchStudent_short($sid);
$buses=list_buses();
$days=getEnumArray('dayofweek');
$dates=array();

	foreach($days as $day => $dayname){
		$daydiff=$startday+$day-$today+7;
		$date=date('Y-m-d',strtotime($daydiff.' day'));
		$dates[$day]=$date;
		}

	$html='';
   	$html.='<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown.'</td><td></td>';
   	$html.='<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_transport.php&sid='.$sid.'">'.$Student['Surname']['value'].', '. $Student['Forename']['value'].'</a></td>';
   	$html.='<td>'.$Student['RegistrationGroup']['value'].'</td>';
	foreach($days as $day=>$dayname){
		$bookings=array();
		$bookings=(array)list_student_journey_bookings($sid,$dates[$day],$day);
		$divin='';$divout='';
		$openId=$sid.'-'.$day;
		foreach($bookings as $bindex => $booking){
			if($buses[$booking['bus_id']]['direction']=='I'){$divname='divin';$divclass='midlite';}
			else{$divname='divout';$divclass='gomidlite';}
			if($$divname==''){
				$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\',\''.$viewbook.'\');"';
				if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
				$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$buses[$booking['bus_id']]['name'].'</div>';
				if($booking['comment']!=''){$$divname.='</span>';}
				}
			}
		if($divin==''){$divin='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\',\''.$viewbook.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
		if($divout==''){$divout='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\',\''.$viewbook.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
		$html.='<td class="clicktoaction">'.$divin . $divout.'</td>';
		}

$returnText=$html;
require_once('../../scripts/http_end_options.php');
exit;
?>
