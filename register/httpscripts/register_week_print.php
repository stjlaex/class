<?php
/**									register_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$comids=(array)$_GET['sids'];}else{$comids=array();}
if(isset($_POST['sids'])){$comids=(array)$_POST['sids'];}
if(isset($_GET['eveid'])){$eveid=$_GET['eveid'];}else{$eveid='';}
if(isset($_POST['eveid'])){$eveid=$_POST['eveid'];}
if(isset($_GET['evedate'])){$date=$_GET['evedate'];}else{$date='';}
if(isset($_POST['evedate'])){$date=$_POST['evedate'];}

if(sizeof($comids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	if($eveid==''){
		$currentevent=get_currentevent();
		$eveid=$currentevent['id'];
		}

	$Students=array();
	$Students['Community'];


	foreach($comids as $comid){

		/* Passed as comid:::yid so yid is already defined*/
		list($comid,$yid)=explode(':::',$comid);

		if($comid!=''){
			$com=(array)get_community($comid);
			$com['yeargroup_id']=$yid;
			$Community=(array)fetchCommunity($comid);
			$Community['Student']=array();
			$students=(array)listin_community($com);
			foreach($students as $student){
				$Student=fetchStudent_short($student['id']);
				for($i=0;$i<7;$i++){
					$weekday=date( "Y-m-d", strtotime('-'.$i.' days'));
					$sessions=array('AM','PM');
					foreach($sessions as $session){
						$e=get_event($weekday,$session);
						if($e['id']!='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
							$att=fetchcurrentAttendance($student['id'],$e['id']);
							if($att['Date']['value']==''){$att['Date']['value']=$weekday;$att['Session']['value']=$session;}
							$Student['Attendances']['Attendance'][]=$att;
							}
						if($e['id']=='' and date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
							$Student['Attendances']['Attendance'][]['Date']['value']=$weekday;
							}
						}
						$week[$weekday]=$weekday;
					}
					$Community['Student'][]=$Student;
				}
				foreach($week as $day){
					if(date('l',strtotime($day))!='Saturday' and date('l',strtotime($day))!='Sunday'){$Students['Dates'][]=array('value'=>display_date($day),'day'=>date('l',strtotime($day)));}
					}
				$Students['Community'][]=$Community;
			}
		}
	$AttendanceEvent=fetchAttendanceEvent($eveid);
	$Students['AttendanceEvent']=$AttendanceEvent;
	$Students['Transform']='register_week_print';
	$Students['Paper']='landscape';

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
