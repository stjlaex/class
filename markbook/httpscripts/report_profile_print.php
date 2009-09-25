<?php
/**
 *			   					httpscripts/report_profile_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}


if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$profile=get_assessment_profile($xmlid);
	$curryear=get_curriculumyear($profile['course_id']);
	$cohort=array('id'=>'','course_id'=>$profile['course_id'],'stage'=>'R','year'=>$curryear);
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['name']);
	//$students=(array)listin_cohort($cohort);

	$Students=array();

	$asstable=array();
	for($ec=0;$ec<sizeof($AssDefs);$ec++){
		$asstable['ass'][]=array('label'=>''.$AssDefs[$ec]['PrintLabel']['value'],
								 'date'=>''.display_date($AssDefs[$ec]['Deadline']['value']),
								 'id_db'=>''.$AssDefs[$ec]['id_db'],
								 'bands'=>array(array('name'=>'C','value'=>'5'),
												array('name'=>'B','value'=>'6'),
												array('name'=>'A','value'=>'8')
												)
								 );
		}

	$Students['asstable']=$asstable;

	$restable=array();
	$grading_grades=$AssDefs[1]['GradingScheme']['grades'];
	$pairs=explode(';', $grading_grades);
	for($c=0;$c<sizeof($pairs);$c++){
		list($levelgrade,$level)=split(':',$pairs[$c]);
		$restable['res'][]=array('label'=>''.$levelgrade,
								 'value'=>''.$level);
		}
	$Students['restable']=$restable;
	
	
	$Students['Student']=array();
	for($sc=0;$sc<sizeof($sids);$sc++){
		$Assessments['Assessment']=array();
		//$sid=$students[$sc]['id'];
		$sid=$sids[$sc];
		$Student=fetchStudent_short($sid);
		
		for($ec=0;$ec<sizeof($AssDefs);$ec++){
			$Assessments['Assessment']=array_merge($Assessments['Assessment'],fetchAssessments_short($sid,$AssDefs[$ec]['id_db']));
			}

		$Student['Assessments']=$Assessments;
		$Students['Student'][]=$Student;
		}

	$Students['Paper']='landscape';
	$Students['Transform']=$profile['transform'];
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>