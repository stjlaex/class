<?php
/**                    httpscripts/generate_assessment_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$eid=$xmlid;
$AssDef=fetchAssessmentDefinition($eid);
$resq=$AssDef['ResultQualifier']['value'];
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

	/*Check user has permission to configure*/
	$perm=getCoursePerm($crid,$respons);
	$neededperm='x';
	if($perm["$neededperm"]=1){

			/*find the appropriate markdef_name*/
		   	if($gena!='' and $gena!=' '){
	   			$grading_grades=$AssDef['GradingScheme']['grades'];
				$d_markdef=mysql_query("SELECT * FROM markdef WHERE
						grading_name='$gena' AND scoretype='grade' 
						AND (course_id='%' OR course_id='$crid')");
				$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
				}
			else{
				$d_markdef=mysql_query("SELECT * FROM markdef WHERE
						scoretype='value' 
						AND (course_id='%' OR course_id='$crid')");
				$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
				}
			$markdef_name=$markdef['name'];
			mysql_free_result($d_markdef);
			$result[]=$markdef_name;

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
					if(mysql_query("INSERT INTO mark 
					  (entrydate, marktype, topic, comment, author,
						 def_name, assessment, component_id) 
							VALUES ('$entrydate', 'score', '$description', 
						 '', 'ClaSS', '$markdef_name', '$asstype', '$pid')"))
					{$result[]='Created mark for '.$bid.'-'.$pid;}
					else{$error[]='Failed on new mark for:'.$bid. $pid.' '.mysql_error();}
					$mid=mysql_insert_id();

					/*entry in eidmid for this new mark*/
					mysql_query("INSERT INTO eidmid (assessment_id, 
						mark_id) VALUES ('$eid', '$mid')");

					/*entry in midcid for new mark and classes with crid and bid*/
					if($bid==' ' or $bid=='G'){$bid='%';}
					$d_class=mysql_query("SELECT id FROM class WHERE
						course_id='$crid' AND subject_id LIKE '$bid' 
						AND stage LIKE '$stage'");
					while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
						$cid=$d_cid[0];
						mysql_query("INSERT INTO midcid (mark_id,
						class_id) VALUES ('$mid', '$cid')");
						}

					if(mysql_num_rows($d_class)>0){
   		  			/*now enter scores for this mark from matching
						eidsid entries*/
   					$d_eidsids=mysql_query("SELECT student_id,
				   		result, value FROM eidsid WHERE
				   		subject_id LIKE '$bid' AND component_id='$pid' 
						AND assessment_id='$eid'");
					$sids=array();
   					while($eidsid=mysql_fetch_array($d_eidsids,MYSQL_ASSOC)){
						$sids[$eidsid['student_id']]=$eidsid;
						/*ensures if their is a duplicate value only
						the most recent id is used*/
						}
					while(list($sid,$score)=each($sids)){
   						$out=$score['result'];
  						$value=$score['value'];
						if($markdef['scoretype']=='grade'){
							$score=gradeToScore($out,$grading_grades);		
							}
						else{$score='';}
						if(mysql_query("INSERT INTO score (student_id,
							mark_id, grade, value) VALUES ('$sid',
							'$mid', '$score', '$value')")){}
						else{
							$error[]='Failed:' .$sid.'-'.$bid.'-'.$pid.'-'.$mid.mysql_error();
							}
   						}
   					mysql_free_result($d_eidsids);
					}
					mysql_free_result($d_class);
					}
				  }
				}
		}
	else{
		$error[]=get_string('nopermissions');
		}

$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
