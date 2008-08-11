<?php
/**				   				student_view_enrolment_action.php
 *
 */

$action='student_view_enrolment.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	//$neededperm='w';
	include('scripts/perm_action.php');

	if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
	if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}else{$enrolyear='';}
	if(isset($_POST['enrolyid'])){$enrolyid=$_POST['enrolyid'];}else{$enrolyid='';}
	if(isset($_POST['eids'])){$eids=$_POST['eids'];}else{$eids=array();}

	$Enrolment=fetchEnrolment($sid);

	/* Only update enrolment community if it's changed*/
	if($enrolyid!=$Enrolment['YearGroup']['value'] or 
	   $enrolyear!=$Enrolment['Year']['value'] or 
	   $enrolstatus!=$Enrolment['EnrolmentStatus']['value']){
			/*see community_list_action for the same - needs to be moved out*/
			/*crucial to the logic of enrolments*/
			if($enrolstatus=='EN'){$newtype='enquired';}
			elseif($enrolstatus=='AC'){$newtype='accepted';}
			elseif($enrolstatus=='P'){$newtype='alumni';}
			else{$newtype='applied';}
			$newcom=array('id'=>'','type'=>$newtype, 
					  'name'=>$enrolstatus.':'.$enrolyid,'year'=>$enrolyear);
			if($enrolstatus=='C'){
				$newcom=array('id'=>'','type'=>'year', 'name'=>$enrolyid);
				}
			$oldcommunities=join_community($sid,$newcom);
			}

	reset($Enrolment);
	while(list($index,$val)=each($Enrolment)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='info'){
				mysql_query("UPDATE info SET
							$field='$inval' WHERE student_id='$sid'");
				}
			}
		}

	/* These are the entry assessments, the list $eids is posted from
	 * the form.
	 */
	$todate=date('Y-m-d');
	while(list($index,$eid)=each($eids)){
		$AssDef=fetchAssessmentDefinition($eid);
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$scorevalue=clean_text($_POST[$eid]);
		/*$$eid are the names of score values posted by the form*/
		/*if the value is empty then score will be unset and no entry made*/
		if($scorevalue==''){$result='';}
		elseif($grading_grades!='' and $grading_grades!=' '){
			$result=scoreToGrade($scorevalue,$grading_grades);
			}
		else{
			$result=$scorevalue;
			}
		$score=array('result'=>$result,'value'=>$scorevalue,'date'=>$todate);
		update_assessment_score($eid,$sid,'G','',$score);
		}
	if($CFG->enrol_assess=='yes'){
		$enaid=$_POST['enaid'];
		$enadetail=$_POST['enadetail'];
		if($enaid!=''){
			mysql_query("UPDATE background SET 
				detail='$enadetail', entrydate='$todate', teacher_id='$tid'
				WHERE id='$enaid';");
			}
		else{
			mysql_query("INSERT INTO background SET student_id='$sid', type='ena',
				detail='$enadetail', entrydate='$todate', teacher_id='$tid';");
			}

		}

	}

	include('scripts/redirect.php');
?>
