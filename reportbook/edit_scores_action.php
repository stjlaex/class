<?php 
/** 		   						edit_scores_action.php
 *
 */

$action='edit_scores.php';
$action_post_vars=array('eid','bid','pid');

$eid=$_POST['eid'];
$bid=$_POST['bid'];
$pid=$_POST['pid'];
$scoretype=$_POST['scoretype'];
$todate=date('Y-m-d');
$newbid=$_POST['newbid'];


include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');


	$AssDef=fetchAssessmentDefinition($eid);
	$grading_grades=$AssDef['GradingScheme']['grades'];
	$crid=$AssDef['Course']['value'];
	$stage=$AssDef['Stage']['value'];
	$year=$AssDef['Year']['value'];
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
		unset($res);
		$sid=$students[$c]['id'];
		$inscore=clean_text($_POST[$sid]);
		$ingrade='';
		/*$$sid are the names of score values posted by the form*/
		/*if the value is empty then score will be unset and no entry made*/
		if($inscore==''){unset($inscore);}
		elseif($scoretype=='grade'){
			$ingrade=$inscore;
			$res=scoreToGrade($inscore,$grading_grades);
			}
		elseif($scoretype=='value'){
			$res=$inscore;
			}

		if(isset($eid) and isset($res)){
			$score=array('result'=>$res,'value'=>$inscore,'date'=>$todate);
			update_assessment_score($eid,$sid,$bid,$pid,$score);
			}
		}
	}

elseif($newbid!=$bid){$bid=$newbid;}

include('scripts/redirect.php');
?>
