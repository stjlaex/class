<?php
/**                    httpscripts/generate_assessment_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$eid=$xmlid;
$AssDef=fetchAssessmentDefinition($eid);
$AssCount=fetchAssessmentCount($eid);
$AssDef=array_merge($AssDef,$AssCount);
$crid=$AssDef['Course']['value'];
$gena=$AssDef['GradingScheme']['value'];
$subject=$AssDef['Subject']['value'];
$compstatus=$AssDef['ComponentStatus']['value'];
$strandstatus=$AssDef['StrandStatus']['value'];
$description=$AssDef['Description']['value'];
$label=$AssDef['PrintLabel']['value'];
$total=$AssDef['Total']['value'];
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

/**
 * Is this assessment for the current year or from a previous year?
 * If for a previous year then we need to find where the students are
 * now: (1) the next stage in the same course; (2) the first stage of
 * the following course it appropriate.
 *
 */
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
			if(isset($nextstages[$yeardiff-1]['id'])){
				$cohorts[]=array('course_id'=>$nextcrid,'stage'=>$nextstages[$yeardiff-1]['id'],'year'=>$yearnow);
				//trigger_error($stagegone['id'].' '.$stagediff.' -------->'.$yeardiff.' : '.$nextstages[$yeardiff-1]['id'],E_USER_WARNING);
				}
			}
		$sc++;
		}
	}
else{
	/* Assessment is for current year so not much to do */
	$cohorts[]=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
	}

/* Check user has permission to configure */
$perm=getCoursePerm($crid,$respons);
$neededperm='x';
if($perm["$neededperm"]=1 and $AssDef['MarkCount']['value']==0){

	/*find the appropriate markdef_name*/
	if($gena!='' and $gena!=' '){
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$d_m=mysql_query("SELECT name FROM markdef WHERE
						grading_name='$gena' AND scoretype='grade' 
						AND (course_id='%' OR course_id='$crid');");
		if(mysql_num_rows($d_m)==0){
			/* If none exists then create keeping the name field unique for this crid.  */
			$markdef_name=$crid.' '.$gena;
			mysql_query("INSERT INTO markdef SET
						name='$markdef_name', scoretype='grade', grading_name='$gena',
						comment='$description', outoftotal='$total', author='ClaSS', 
						course_id='$crid', subject_id='$subject';");
			}
		else{
			$markdef_name=mysql_result($d_m,0);
			}
		$markdef_scoretype='grade';
		}
	else{
		$d_m=mysql_query("SELECT name FROM markdef WHERE
						scoretype='value' AND (course_id='%' OR course_id='$crid');");
		$markdef_name=mysql_result($d_m,0);
		$markdef_scoretype='value';
		}
	mysql_free_result($d_m);

	if($deadline!='0000-00-00'){$entrydate=$deadline;}
	else{$entrydate=date('Y').'-'.date('n').'-'.date('j');}

	/*make a list of subjects that will need distinct new marks*/
	$subjects=array();
	if($subject!='%'){$subjects[]['id']=$subject;}
	else{
		$subjects=list_course_subjects($crid);
		}
		
	/* Generate a mark for each bid/pid combination */
	foreach($subjects as $subject){
		$bid=$subject['id'];
		$components=(array)list_subject_components($bid,$crid,$compstatus);
		/* If there is no component for this subject or
		   components are not requested then $pid is blank*/
		if(sizeof($components)==0){$components[0]['id']='';}
		foreach($components as $component){
			$strands=(array)list_subject_components($component['id'],$crid,$strandstatus);
			if(sizeof($strands)==0){$strands[0]['id']=$component['id'];}
			foreach($strands as $strand){
				$pid=$strand['id'];

				if($bid==' ' or $bid=='G'){$bid='%';}
				$cidno=0;
				foreach($cohorts as $cohort){
					$cridnow=$cohort['course_id'];
					$stagenow=$cohort['stage'];
					$bidnow='';
					$pidnow='';
					if($cridnow!=$crid){
						/* If carrying forward to another course need
						   to accomodate changes in currilucum
						   structure ie. hunt for equivalent subject/component/strand combination */
						$d_s=mysql_query("SELECT DISTINCT subject_id FROM component 
									WHERE course_id='$cridnow' AND id='$pid' AND subject_id='$bid' AND status!='U';");
						if(mysql_num_rows($d_s)>0){$bidnow=$bid;$pidnow=$pid;}
						else{
							if($pid!=''){
								$d_s=mysql_query("SELECT DISTINCT subject_id FROM component 
											WHERE course_id='$cridnow' AND id='' AND subject_id='$pid' AND status!='U';");
								if(mysql_num_rows($d_s)>0){$bidnow=$pid;$pidnow='';}
								else{
									if($pid!=''){
										$d_s=mysql_query("SELECT DISTINCT subject_id FROM component 
															WHERE course_id='$cridnow' AND id='$pid' AND status!='U';");
										if(mysql_num_rows($d_s)>0){$bidnow=mysql_result($d_s,0);$pidnow=$pid;}
										if($bidnow!=''){
											$d_s=mysql_query("SELECT DISTINCT subject_id FROM component 
														   WHERE course_id='$cridnow' AND id='$bidnow' AND status!='U';");
											if(mysql_num_rows($d_s)>0){$bidnow=mysql_result($d_s,0);$pidnow=$pid;}
											}
										}
									}
								}
							}
						}
					else{
						$bidnow=$bid;
						$pidnow=$pid;
						}

					/* Can only carry forward to next course if their is a correpsonding subject */
					if($bidnow!=''){
						trigger_error($stage.' '.$crid.': '.$bid.' : '.$pid.' -------->'.$cridnow.' : '.$bidnow.' : '.$pidnow.' : '.$stagenow,E_USER_WARNING);
	
						mysql_query("INSERT INTO mark (entrydate, marktype, topic, comment, author,
						 def_name, assessment, component_id) VALUES ('$entrydate', 'score', '$description', 
						 '', 'ClaSS', '$markdef_name', '$asstype', '$pidnow');");
						$mid=mysql_insert_id();

						/* Make entry in eidmid for this new mark. */
						mysql_query("INSERT INTO eidmid (assessment_id,mark_id) VALUES ('$eid', '$mid');");

						$d_class=mysql_query("SELECT id FROM class WHERE
						course_id='$cridnow' AND subject_id LIKE '$bidnow' AND stage LIKE '$stagenow';");
						/* Make entries in midcid for the new mark */
						while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
							$cid=$d_cid[0];
							mysql_query("INSERT INTO midcid (mark_id,class_id) VALUES ('$mid', '$cid');");
							$cidno++;
							}
						mysql_free_result($d_class);
						//trigger_error($stagenow.' '.$cridnow.': '.$bidnow.' : '.$pidnow. ' '.$cidno,E_USER_WARNING);
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
							if($markdef_scoretype=='grade'){
								$score=gradeToScore($out,$grading_grades);		
								}
							else{$score='';}
							mysql_query("INSERT INTO score (student_id,
										mark_id, grade, value) VALUES ('$sid','$mid', '$score', '$value');");
							}
						mysql_free_result($d_eidsids);
						}
					}
				}
			}
		}
	}


$AssCount=(array)fetchAssessmentCount($eid);
$returnXML=array_merge($AssDef,$AssCount);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
