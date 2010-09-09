<?php
/**									register_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

		/*TODO: get the section for this register?*/
	if(!isset($secid)){$secid=1;}
	$currentevent=get_currentevent($secid);

	$Students=array();
	$Students['Community'];

  	$AttendanceEvent=fetchAttendanceEvent($currentevent['id']);
 	$Students['AttendanceEvent']=$AttendanceEvent;

	while(list($index,$comid)=each($sids)){
		//trigger_error($comid,E_USER_WARNING);
		if($comid!=''){
			$Community=(array)fetchCommunity($comid);
			$Community['Student']=array();
			$students=(array)listin_community(array('id'=>$comid));
			while(list($index,$student)=each($students)){
				$Student=fetchStudent_short($student['id']);
				$Student['Attendances']['Attendance'][]=fetchcurrentAttendance($student['id']);
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
