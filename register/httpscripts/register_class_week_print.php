<?php
/**			   					httpscripts/register_class_week_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$cid=$_GET['sids'][0];}else{$cid=-1;}
if(isset($_POST['sids'])){$cid=$_POST['sids'][0];}
if(isset($_GET['eveid'])){$eveid=$_GET['eveid'];}else{$eveid='';}
if(isset($_POST['eveid'])){$eveid=$_POST['eveid'];}
if(isset($_GET['evedate'])){$date=$_GET['evedate'];}else{$date='';}
if(isset($_POST['evedate'])){$date=$_POST['evedate'];}
if(isset($_GET['transform'])){$transform=$_GET['transform'];}else{$transform='';}
if(isset($_POST['transform'])){$transform=$_POST['transform'];}

if($cid<1 or $cid==''){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	if($eveid==''){
		$currentevent=get_currentevent();
		$eveid=$currentevent['id'];
		}
	/* The class for which attendnace is being reported. */
	$thisclass=(array)get_this_class($cid);
	$students=(array)listin_class($cid,true);
	foreach($students as $student){
		$sid=$student['id'];
		$Student=(array)fetchStudent_short($sid);
		$startdate=date( "Y-m-d", strtotime('-7 days'));
		if($date=='' and isset($currentevent)){$enddate=$currentevent['date'];}else{$enddate=$date;}
		$AttendanceSummary=(array)fetch_classAttendanceSummary($cid,$sid,$startdate,$enddate);
		$sectionid=get_student_section($sid);
		for($i=0;$i<7;$i++){
			$weekday=date( "Y-m-d", strtotime('-'.$i.' days'));
			if(date('l',strtotime($weekday))!='Saturday' and date('l',strtotime($weekday))!='Sunday'){
				$classAttendances=fetch_classAttendances($cid,$sid,-$i,1000,1);
				foreach($classAttendances['Attendance'] as $Attendance){
					if($Attendance['Status']['value']=='p'){
						if($Attendance['Late']['value']>0){
							$Attendance['Status']['display']='L ';
							}
						else{
							$Attendance['Status']['display']='P'.$Attendance['Period']['value'].' ';
							}
						}
					elseif($Attendance['Status']['value']=='a'){
						$absent_event=get_event($Attendance['Date']['value'],$Attendance['Session']['value']);
						$SessionAttendance=fetchcurrentAttendance($sid,$absent_event['id']);
						if($SessionAttendance['Status']['value']=='p'){
							$Attendance['Status']['display']=' X ';
							}
						else{
							$Attendance['Status']['display']=$Attendance['Code']['value'];
							}
						if(!empty($Attendance['Comment']['value'])){
							$Note=array('Date'=>display_date($Attendance['Date']['value']),
										'Session'=>$Attendance['Session']['value'],
										'Code'=>$Attendance['Code']['value'],
										'Comment'=>$Attendance['Comment']['value']
										);
							$Notes['Note'][]=$Note;
							$Attendance['AttendanceNotes']=$Notes;
							}
						}
					}
				if(count($classAttendances['Attendance'])==0){
					$Student['Attendances']['Attendance'][]['Date']['value']=$weekday;
					}
				else{
					$Student['Attendances']['Attendance'][]=$Attendance;
					}
				}
			$week[$weekday]=$weekday;
			}
		$Student['AttendanceSummary']=$AttendanceSummary;
		$Students['Student'][]=$Student;
		}

	foreach($week as $day){
		if(date('l',strtotime($day))!='Saturday' and date('l',strtotime($day))!='Sunday'){
			$Students['Dates'][]=array('display'=>display_date($day),'value'=>date('Y-m-d',strtotime($day)),'day'=>date('l',strtotime($day)));
			}
		}

	$Students['Class']=$thisclass['name'];
	$AttendanceEvent=fetchAttendanceEvent($eveid);
	$Students['AttendanceEvent']=$AttendanceEvent;
	$Students['Paper']='landscape';
	$Students['Transform']='register_class_week_print';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
