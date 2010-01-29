<?php
/**
 *			   					httpscripts/student_grades_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid=-1;}
if(isset($_POST['sid'])){$sid=$_POST['sid'];}

if($sid==-1){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	/*
	$crid='GCSE';
	$stage='Y11';
	$curryear=get_curriculumyear($crid);
	$cohort=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$curryear);
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort);
	$bid='%';
	$pid='%';
	*/
	$Students=array();	
	$Students['Student']=array();
	//$Assessments['Assessment']=array();
	$Student=fetchStudent_short($sid);
	$Assessments['Assessment']=(array)fetchAssessments_short($sid);
	/*
	for($ec=0;$ec<sizeof($AssDefs);$ec++){
		$Assessments['Assessment']=array_merge($Assessments['Assessment'],fetchAssessments_short($sid,$AssDefs[$ec]['id_db'],$bid,$pid));
		}
	*/
	$Student['Assessments']=xmlarray_indexed_check($Assessments,'Assessment');
	$Students['Student'][]=$Student;

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']='';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>