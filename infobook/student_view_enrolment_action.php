<?php
/**				   				student_view_enrolment_action.php
 *
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	if($perm['r']!=1){$perm=getSENPerm($yid,$respons);}
	//$neededperm='w';
	include('scripts/perm_action.php');

	if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
	if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}else{$enrolyear='';}
	if(isset($_POST['enrolyid'])){$enrolyid=$_POST['enrolyid'];}else{$enrolyid='';}
	if(isset($_POST['leavingdate'])){$leavingdate=$_POST['leavingdate'];}else{$leavingdate='';}
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


	/* TODO: set the leaving date using join_community BUT need to
	   remove enrolstatus from info table to achieve this.
	if($leavingdate!=$Enrolment['LeavingDate']['value']){
		$newcom=array('id'=>'','type'=>'P', 
					  'name'=>'P:'.$enrolyid,'year'=>$enrolyear);
		$oldcommunities=join_community($sid,$newcom);
		}
	*/

	reset($Enrolment);
	while(list($index,$val)=each($Enrolment)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='info'){
				mysql_query("UPDATE info SET
							$field='$inval' WHERE student_id='$sid';");
				$Enrolment["$index"]['value']=$inval;
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
		/* $$eid are the names of score values posted by the form
		 * if the value is empty then score will be unset and no entry made
		 */
		$scorevalue=clean_text($_POST[$eid]);
		if($scorevalue==''){$result='';}
		elseif($grading_grades!='' and $grading_grades!=' '){
			$result=scoreToGrade($scorevalue,$grading_grades);
			}
		else{
			$result=$scorevalue;
			}
		$score=array('result'=>$result,'value'=>$scorevalue,'date'=>$todate);
		update_assessment_score($eid,$sid,'G','',$score);
		/* Option to automatically flag student as SEN */
		//trigger_error('SEN:'.$result.':'.$scorevalue,E_USER_WARNING);
		if($CFG->enrol_assess_sen!='' and $CFG->enrol_assess_sen==$result){
			mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid';");
			$todate=$Enrolment['EntryDate']['value'];
			mysql_query("INSERT INTO senhistory SET startdate='$todate', student_id='$sid';");
			$senhid=mysql_insert_id();
			mysql_query("INSERT INTO sencurriculum SET
					senhistory_id='$senhid', subject_id='General'");
			mysql_query("INSERT INTO sentype SET
						student_id='$sid',sentype='ENC',senranking='1';");
			}
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
