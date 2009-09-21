<?php 
/** 		   						edit_scores_action.php
 *
 */

$action='edit_scores.php';
$action_post_vars=array('eid','bid','pid','curryear','profid');

$eid=$_POST['eid'];
$bid=$_POST['bid'];
$pid=$_POST['pid'];
$curryear=$_POST['curryear'];
$profid=$_POST['profid'];
$todate=date('Y-m-d');


include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');


	$AssDef=fetchAssessmentDefinition($eid);
	$crid=$AssDef['Course']['value'];
	$compstatus=$AssDef['ComponentStatus']['value'];
	$grading_grades=$AssDef['GradingScheme']['grades'];
	$crid=$AssDef['Course']['value'];
	$stage=$AssDef['Stage']['value'];
	$year=$AssDef['Year']['value'];

	$strands=(array)list_subject_components($pid,$crid,$compstatus);
	$strandno=sizeof($strands);
	$labels=array();
	$mids=array();
	if($strandno>0){
		foreach($strands as $sindex => $strand){
			$labels[]=$strand['id'];
			$mids[]=get_assessment_mid($eid,$crid,$bid,$strand['id']);
			}
		}
	else{
		$labels[]=$pid;
		$mids[]=get_assessment_mid($eid,$crid,$bid,$pid);
		}


	$students=array();
	if($stage!='%'){
		$cohorts[]=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$year);
		}
	else{
		$cohorts=(array)list_course_cohorts($crid,$year);
		}
	while(list($index,$cohort)=each($cohorts)){
		$students=array_merge($students,listin_cohort($cohort));
		}

	for($c=0;$c<sizeof($students);$c++){
		$sid=$students[$c]['id'];
		foreach($labels as $lindex => $label){
			$scorevalue=clean_text($_POST[$sid.$lindex]);
			/*$$sid are the names of score values posted by the form*/
			/*if the value is empty then score will be unset and no entry made*/
			if($scorevalue==''){$result='';$scoretype='';}
			elseif($grading_grades!='' and $grading_grades!=' '){
				$result=scoreToGrade($scorevalue,$grading_grades);
				$scoretype='grade';
				}
			else{
				$result=$scorevalue;
				$scoretype='value';
				}
			$score=array('type'=>$scoretype,'result'=>$result,'value'=>$scorevalue,'date'=>$todate);
			update_assessment_score($eid,$sid,$bid,$label,$score);
			update_mark_score($mids[$lindex],$sid,$score);
			}
		}
	}

if(isset($_POST['newbid'])){$bid=$_POST['newbid'];}
if(isset($_POST['newpid'])){$pid=$_POST['newpid'];}

include('scripts/redirect.php');
?>
