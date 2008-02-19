<?php
/**			   					httpscripts/report_attendance_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}else{$startdate='';}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
if(isset($_GET['enddate'])){$enddate=$_GET['enddate'];}else{$enddate='';}
if(isset($_POST['enddate'])){$enddate=$_POST['enddate'];}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			$Attendance=fetchAttendanceSummary($sid,$startdate,$enddate);
			$Student['Attendance']=$Attendance;
			$Student['Attendance']['Paper']='portrait';
			$Student['Attendance']['Transform']='attendance_summary';
			$Students['Student'][]=$Student;
			}
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>