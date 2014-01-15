<?php
/**
 *			   					httpscripts/student_grades_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['sid'])){$sid=$_GET['sid'];}else{$sid=-1;}
if(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['xsltransform'])){$xsltransform=$_GET['xsltransform'];}else{$xsltransform=-1;}
if(isset($_POST['xsltransform'])){$xsltransform=$_POST['xsltransform'];}

if($sid==-1){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$asstable=array();
	/*
	$d_assessment=mysql_query("SELECT * FROM assessment JOIN
				rideid ON rideid.assessment_id=assessment.id 
				WHERE report_id='$rid' ORDER BY rideid.priority, assessment.label;");
	while($ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		$asstable['ass'][]=array('name' => ''.$ass['description'],
								 'label' => ''.$ass['label'],
								 'date' => ''.$ass['date'],
								 'element' => ''.$ass['element']);
		}
	*/

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
	$Student=fetchStudent_short($sid);
	$Assessments['Assessment']=(array)fetchAssessments_short($sid);
	$asseids=array();
	foreach($Assessments['Assessment'] as $index => $Assessment){
		$eid=$Assessment['id_db'];
		if(!in_array($eid,$asseids)){
				$asseids[]=$eid;
				$d_assessment=mysql_query("SELECT * FROM assessment WHERE id='$eid';");
				$ass=mysql_fetch_array($d_assessment,MYSQL_ASSOC);
				$asstable['ass'][]=array('id_db' => ''.$eid,
										 'name' => ''.$ass['description'],
										 'label' => ''.$ass['label'],
										 'date' => ''.$ass['deadline'],
										 'element' => ''.$ass['element']);
				}
			}
	/*
	for($ec=0;$ec<sizeof($AssDefs);$ec++){
		$Assessments['Assessment']=array_merge($Assessments['Assessment'],fetchAssessments_short($sid,$AssDefs[$ec]['id_db'],$bid,$pid));
		}
	*/
	$Student['Assessments']=xmlarray_indexed_check($Assessments,'Assessment');
	$Students['Student'][]=$Student;
	$Students['asstable']=(array)$asstable;

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']=$xsltransform;
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>