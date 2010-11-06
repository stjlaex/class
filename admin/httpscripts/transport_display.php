<?php
/**                    httpscripts/transport_display.php
 *
 */

require_once('../../scripts/http_head_options.php');
include('../../lib/fetch_transport.php');

$sid=$_GET['sid'];
if(isset($_GET['date'])){$date=$_GET['date'];}

$todate=date('Y-m-d');
$today=date('N');
$Student=fetchStudent_short($sid);
	$buses=list_buses();
	$days=getEnumArray('dayofweek');
	$dates=array();
	foreach($days as $day => $dayname){
		$daydiff=$day-$today;
		$date=date('Y-m-d',strtotime($daydiff.' day'));
		$dates[$day]=$date;
		}

	$html='';
   	$html.='<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td><td></td>';
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
				$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\');"';
				if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
				$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$buses[$booking['bus_id']]['name'].'</div>';
				if($booking['comment']!=''){$$divname.='</span>';}
				}
			}
		if($divin==''){$divin='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
		if($divout==''){$divout='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
		$html.='<td class="clicktoaction">'.$divin . $divout.'</td>';
		}

$returnText=$html;
require_once('../../scripts/http_end_options.php');
exit;
?>
