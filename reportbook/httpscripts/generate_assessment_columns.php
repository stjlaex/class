<?php
/**                    httpscripts/generate_assessment_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$eid=$xmlid;
$AssDef=fetchAssessmentDefinition($eid);
$crid=$AssDef['Course']['value'];
$gena=$AssDef['GradingScheme']['value'];
$subject=$AssDef['Subject']['value'];
$compstatus=$AssDef['ComponentStatus']['value'];
$strandstatus=$AssDef['StrandStatus']['value'];
$description=$AssDef['Description']['value'];
$stage=$AssDef['Stage']['value'];
$deadline=$AssDef['Deadline']['value'];
/* Any estimates or targets or whatever get assessment type = other. */
if($AssDef['ResultStatus']['value']=='R'){$asstype='yes';}
else{$asstype='other';}

$yearnow=get_curriculumyear($crid);

/* Where to apply this assessment in the MarkBook will be indicated in
 * a cohorts array with the cohort pointing to where the students are
 * now - and this could be for another course altogether.
 */
$cohorts=array();

/* Is this assessment for the current year of from a previous year? */
if($AssDef['Year']['value']!=$yearnow){
	$yeardiff=$yearnow-$AssDef['Year']['value'];
	$stagegones=array();
	$stages=list_course_stages($crid);
	$cridscourses=array();
	$courses=(array)list_courses();
	foreach($courses as $course){
		$cridscourses[$course['id']]=$course;
		}
	$nextcrid=$cridscourses[$crid]['nextcourse_id'];
	if($stage=='%'){
		$stagegones=(array)$stages;
		$sc=0;
		}
	else{
		$stagegones[]=array('id'=>$stage,'name'=>$stage);
		$c=0;
		foreach($stages as $stagegone){
			if($stagegone['id']==$stage){$sc=$c;}
			$c++;
			}
		unset($c);
		}
	foreach($stagegones as $stagegone){
		$stagediff=$sc+$yeardiff;
		if($stagediff<(sizeof($stages))){
			$cohorts[]=array('course_id'=>$crid,'stage'=>$stages[$stagediff]['id'],'year'=>$yearnow);
			}
		elseif($nextcrid!='' and $nextcrid!='1000'){
			/* This will identify the first stage of the next course. */
			$nextstages=list_course_stages($nextcrid);
			$cohorts[]=array('course_id'=>$nextcrid,'stage'=>$nextstages[0]['id'],'year'=>$yearnow);
			}
		$sc++;
		}
	}
else{
	$cohorts[]=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
	}

/* Check user has permission to configure */
$perm=getCoursePerm($crid,$respons);
$neededperm='x';
if($perm["$neededperm"]=1 and $AssDef['MarkCount']['value']==0){

	/*find the appropriate markdef_name*/
	if($gena!='' and $gena!=' '){
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$d_markdef=mysql_query("SELECT * FROM markdef WHERE
						grading_name='$gena' AND scoretype='grade' 
						AND (course_id='%' OR course_id='$crid');");
		$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
		}
	else{
		$d_markdef=mysql_query("SELECT * FROM markdef WHERE
						scoretype='value' AND (course_id='%' OR course_id='$crid');");
		$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
		}
	$markdef_name=$markdef['name'];
	mysql_free_result($d_markdef);
		
	if($deadline!='0000-00-00'){$entrydate=$deadline;}
	else{$entrydate=date('Y').'-'.date('n').'-'.date('j');}
	
	/*make a list of subjects that will need distinct new marks*/
	$subjects=array();
	if($subject!='%'){$subjects[]['id']=$subject;}
	else{
		$subjects=list_course_subjects($crid);
		}
		
	/*generate a mark for each bid/pid combination*/
	while(list($index,$subject)=each($subjects)){
		$bid=$subject['id'];
		$components=(array)list_subject_components($bid,$crid,$compstatus);
		/* If there is no component for this subject or
		   components are not requested then $pid is blank*/
		if(sizeof($components)==0){$components[0]['id']='';}
		while(list($index,$component)=each($components)){
			$strands=(array)list_subject_components($component['id'],$crid,$strandstatus);
			if(sizeof($strands)==0){$strands[0]['id']=$component['id'];}
			while(list($index,$strand)=each($strands)){
				$pid=$strand['id'];
				mysql_query("INSERT INTO mark (entrydate, marktype, topic, comment, author,
						 def_name, assessment, component_id) VALUES ('$entrydate', 'score', '$description', 
						 '', 'ClaSS', '$markdef_name', '$asstype', '$pid');");
				$mid=mysql_insert_id();

				/* Make entry in eidmid for this new mark. */
				mysql_query("INSERT INTO eidmid (assessment_id,mark_id) VALUES ('$eid', '$mid');");

				if($bid==' ' or $bid=='G'){$bid='%';}
				$cidno=0;
				foreach($cohorts as $cohort){
					$cridnow=$cohort['course_id'];
					$stagenow=$cohort['stage'];
					$d_class=mysql_query("SELECT id FROM class WHERE
						course_id='$cridnow' AND subject_id LIKE '$bid' AND stage LIKE '$stagenow';");
					/* Make entries in midcid for the new mark */
					while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
						$cid=$d_cid[0];
						mysql_query("INSERT INTO midcid (mark_id,class_id) VALUES ('$mid', '$cid');");
						$cidno++;
						}
					}

				if($cidno>0){
					$d_eidsids=mysql_query("SELECT student_id,result, value FROM eidsid WHERE
				   		subject_id LIKE '$bid' AND component_id='$pid' AND assessment_id='$eid';");
					$sids=array();
					while($eidsid=mysql_fetch_array($d_eidsids,MYSQL_ASSOC)){
						$sids[$eidsid['student_id']]=$eidsid;
						}
					while(list($sid,$score)=each($sids)){
						$out=$score['result'];
						$value=$score['value'];
						if($markdef['scoretype']=='grade'){
							$score=gradeToScore($out,$grading_grades);		
							}
						else{$score='';}
						mysql_query("INSERT INTO score (student_id,
							mark_id, grade, value) VALUES ('$sid','$mid', '$score', '$value');");
						}
					mysql_free_result($d_eidsids);
					mysql_free_result($d_class);
					}

				}
			}
		}
	}

$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
