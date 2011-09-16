<?php
/**									register_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$comids=(array)$_GET['sids'];}else{$comids=array();}
if(isset($_POST['sids'])){$comids=(array)$_POST['sids'];}
if(isset($_GET['eveid'])){$eveid=$_GET['eveid'];}else{$eveid='';}
if(isset($_POST['eveid'])){$eveid=$_POST['eveid'];}

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

  	$AttendanceEvent=fetchAttendanceEvent($eveid);
 	$Students['AttendanceEvent']=$AttendanceEvent;

	foreach($comids as $comid){
		if($comid!=''){
			$Community=(array)fetchCommunity($comid);
			$Community['Student']=array();
			$students=(array)listin_community(array('id'=>$comid));
			foreach($students as $student){
				$Student=fetchStudent_short($student['id']);
				$Student['Attendances']['Attendance'][]=fetchcurrentAttendance($student['id'],$eveid);
				$Community['Student'][]=$Student;
				}
			$Students['Community'][]=$Community;
			}
		}
	$Students['Transform']='register_print';
	$Students['Paper']='portait';

	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
