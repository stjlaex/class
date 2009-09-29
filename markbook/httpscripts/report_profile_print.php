<?php
/**
 *			   					httpscripts/report_profile_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid='%';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$pid='%';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['stage'])){$stage=$_GET['stage'];}else{$stage='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	$profile=get_assessment_profile($xmlid);
	$crid=$profile['course_id'];
	$profilename=$profile['name'];
	$curryear=get_curriculumyear($crid);
	$cohort=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$curryear);
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profilename);

	/* Bands */
  	$d_stats=mysql_query("SELECT DISTINCT * FROM statvalues JOIN stats ON stats.id=statvalues.stats_id 
				WHERE stats.course_id='$crid' AND stats.profile_name='$profilename' 
				AND statvalues.stage='$stage' AND statvalues.subject_id='$bid' 
				AND (statvalues.component_id LIKE '$pid' OR statvalues.component_id='%');");
	$bands=array();
	while($stat=mysql_fetch_array($d_stats,MYSQL_ASSOC)){
		$bands[$stat['date']]=$stat;
		}
	arsort($bands);


	$Students=array();

	$asstable=array();
	for($ec=0;$ec<sizeof($AssDefs);$ec++){
		$assdate=$AssDefs[$ec]['Deadline']['value'];
		foreach($bands as $date=>$band){
			if($assdate<$date){$bdate=$date;}
			}
		$asstable['ass'][]=array('label'=>''.$AssDefs[$ec]['PrintLabel']['value'],
								 'date'=>''.display_date($assdate),
								 'id_db'=>''.$AssDefs[$ec]['id_db'],
								 'bands'=>array(array('name'=>'C','value'=>$bands[$bdate]['value3']),
												array('name'=>'B','value'=>$bands[$bdate]['value2']),
												array('name'=>'A','value'=>$bands[$bdate]['value1'])
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
			$Assessments['Assessment']=array_merge($Assessments['Assessment'],fetchAssessments_short($sid,$AssDefs[$ec]['id_db'],$bid,$pid));
			}

		$Student['Assessments']=$Assessments;
		$Students['Student'][]=$Student;
		}

	$Students['Paper']='landscape';
	$Students['Transform']=$profile['transform'];
	$Students['Subject']['value_db']=$bid;
	$Students['Subject']['value']=get_subjectname($bid);
	$Students['Component']['value_db']=$pid;
	$Students['Component']['value']=get_subjectname($pid);
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>