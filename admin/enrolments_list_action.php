<?php 
/**			       		 enrolments_list_action.php
 *
 */

$action='enrolments_matrix.php';
$action_post_vars=array('comid','enrolyear','enrolstage','enrolstatus');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['enrolstage'])){$enrolstage=$_POST['enrolstage'];}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
else{$sids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){


	if($enrolstage=='RE'){
		$todate=date('Y-m-d');
		$AssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		$eid=$AssDefs[0]['id_db'];
		$AssDef=(array)fetchAssessmentDefinition($eid);
		$grading_grades=$AssDef['GradingScheme']['grades'];
		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			$reenrol_boarder_eid=$AssDefs[1]['id_db'];
			$AssDef=(array)fetchAssessmentDefinition($reenrol_boarder_eid);
			$boarder_grades=$AssDef['GradingScheme']['grades'];
			}
		}

	foreach($sids as $sid){
		if(isset($_POST["yid$sid"])){
			$yid=clean_text($_POST["yid$sid"]);

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
				$oldcommunities=join_community($sid,$newcom);
				}
			elseif($enrolstage=='C'){
				if(isset($_POST["C$sid"])){
					$in=clean_text($_POST["C$sid"]);
					if($in=='P'){
						$newcom=array('id'=>'','type'=>'alumni', 
									  'name'=>'P:'.$yid,'year'=>$enrolyear);
						}
					elseif($in=='C'){
						$newcom=array('id'=>'','type'=>'year','name'=>$yid);
						}
					$oldcommunities=join_community($sid,$newcom);
					}
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
				else{
					/*TODO: set a real default value?*/
					$in='1';$result='P';
					}
				$score=array('result'=>$result,'value'=>$in,'date'=>$todate);
				update_assessment_score($eid,$sid,'G','',$score);

				if(isset($_POST["ACRE$sid"])){
					$in=clean_text($_POST["ACRE$sid"]);
					if($boarder_grades!='' and $boarder_grades!=' '){
						$result=scoreToGrade($in,$boarder_grades);
						}
					else{
						$result=$in;
						}
					$score=array('result'=>$result,'value'=>$in,'date'=>$todate);
					update_assessment_score($reenrol_boarder_eid,$sid,'G','',$score);
					}
				}
			}
		}
	}

include('scripts/redirect.php');
?>
