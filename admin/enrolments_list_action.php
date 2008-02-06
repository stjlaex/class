<?php 
/**									 enrolments_list_action.php
 */

$action='enrolments_matrix.php';
$action_post_vars=array('comid','enrolyear','enrolstage');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['enrolstage'])){$enrolstage=$_POST['enrolstage'];}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
else{$sids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){

	$com=get_community($comid);
	$comtype=$com['type'];
	if($comtype!='year'){list($enrolstatus,$yid)=split(':',$com['name']);}
	else{$yid=$com['name'];}


	/*Check user has permission to edit*/
	$perm=getYearPerm($yid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	if($enrolstage=='RE'){
		$todate=date('Y-m-d');
		$AssDefs=fetch_enrolmentAssessmentDefinitions('','RE');
		$eid=$AssDefs[0]['id_db'];
		$AssDef=fetchAssessmentDefinition($eid);
		$grading_grades=$AssDef['GradingScheme']['grades'];
		}

	while(list($index,$sid)=each($sids)){

		if($enrolstage=='E'){
			if(isset($_POST["E$sid"])){$in=clean_text($_POST["E$sid"]);}
			else{$in='';}
			/*see student_view_enrolment_action for the same - needs to be moved out*/
			/*crucial to the logic of enrolments*/
			if($in=='EN'){$newtype='enquired';}
			elseif($in=='AC'){$newtype='accepted';}
			else{$newtype='applied';}
			$newcom=array('id'=>'','type'=>$newtype, 
						  'name'=>$in.':'.$yid,'year'=>$enrolyear);
			if($in=='C'){
				$newcom=array('id'=>'','type'=>'year', 'name'=>$yid);
				}
			/***********************/
			$oldcommunities=join_community($sid,$newcom);
			}
		elseif($enrolstage=='RE'){
			if(isset($_POST["RE$sid"])){
				$in=clean_text($_POST["RE$sid"]);
				if($grading_grades!='' and $grading_grades!=' '){
					$result=scoreToGrade($in,$grading_grades);
					}
				else{
					$result=$in;
					}
				}
			else{$in='';$result='';}
			$score=array('result'=>$result,'value'=>$in,'date'=>$todate);
			update_assessment_score($eid,$sid,'G','',$score);
			}
		}

	}

include('scripts/redirect.php');
?>
