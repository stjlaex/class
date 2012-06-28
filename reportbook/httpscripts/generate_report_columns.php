<?php
/**                    httpscripts/generate_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$rid=$xmlid;
$ReportDef=fetch_reportdefinition($rid);
$crid=$ReportDef['report']['course_id'];
$stage=$ReportDef['report']['stage'];
$substatus=$ReportDef['report']['subject_status'];
$compstatus=$ReportDef['report']['component_status'];
$title=$ReportDef['report']['title'];
$date=$ReportDef['report']['date'];
$deadline=$ReportDef['report']['deadline'];
if($ReportDef['report']['addcomment']=='no' and $ReportDef['report']['addcategory']=='yes'){
	$marktype='compound';
	}
else{
	$marktype='report';
	}


$yearnow=get_curriculumyear($crid);


/* Where to apply this assessment in the MarkBook will be indicated in
 * a cohorts array with the cohort pointing to where the students are
 * now - and this could be for another course altogether.
 */
$cohorts=array();
/* Always add the cohort for the assessment year */
if($stage=='%'){
	$stages=(array)list_course_stages($crid);
	}
else{
	$stages[]=array('id'=>$stage,'name'=>$stage);
	}
foreach($stages as $stage){
	$cohorts[]=array('course_id'=>$crid,'stage'=>$stage['id'],'year'=>$yearnow);
	}


	/* Only for profiles!!!!!!! This will identify the first stage of the next course.
	$d_course=mysql_query("SELECT nextsubject_id FROM report WHERE id='$rid'");
	$nextbid=mysql_result($d_course,0);
	if($nextbid!=''){
		$d_course=mysql_query("SELECT nextcourse_id FROM course WHERE id='$crid'");
		$nextcrid=mysql_result($d_course,0);
		$yearnow=get_curriculumyear($crid);
		if($nextcrid!='' and $nextcrid!='1000'){
			$nextstages=list_course_stages($nextcrid);
			$nextstage=$nextstages[0]['id'];
			}
		else{
			unset($nextcrid);
			}
		}
	*/

	/* Make a list of subjects that will need distinct new marks*/
	$subjects=array();
	if($substatus=='G' or $substatus=='%'){$subjects[]=array('id'=>'%');}/*special when bid is general*/
	else{$subjects=list_course_subjects($crid,$substatus);}


		/* Generate a report column for each subject*/
   	   	foreach($subjects as $subject){
			$bid=$subject['id'];
			$components=(array)list_subject_components($bid,$crid,$compstatus);
			if(sizeof($components)==0){$components[0]['id']='';}
			foreach($components as $component){
				/* If its a compound report then it will also work
				 * across strands with the same comp status as the
				 * components. TODO: Need to specify strand status
				 * explicity for reports.
				 */
				if($marktype=='compound'){$strands=(array)list_subject_components($component['id'],$crid,$compstatus);}
				else{$strands=array();}

				if(sizeof($strands)==0){$strands[0]['id']=$component['id'];}
				foreach($strands as $strand){

					/* NB. If there is no component for this subject or components are not
					 * requested then $pid is blank. 
					 */
					$pid=$strand['id'];
					
					/* The rid is stored in the midlist field for each mark*/
					mysql_query("INSERT INTO mark (entrydate, marktype, topic, comment, author,
									def_name, assessment, midlist, component_id) 
									VALUES ('$date', '$marktype', '$title', 
									'complete by $deadline', 'ClaSS', '', 'no', '$rid', '$pid')");
					$mid=mysql_insert_id();

					foreach($cohorts as $cohort){
						$cohid=update_cohort($cohort);
						$cridnow=$cohort['course_id'];
						$stagenow=$cohort['stage'];
				
						/* Entry in midcid for new mark and classes with crid and bid. */
						$d_class=mysql_query("SELECT id FROM class WHERE cohort_id='$cohid' AND subject_id LIKE '$bid';");
						while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
							$cid=$d_cid[0];
							mysql_query("INSERT INTO midcid (mark_id,class_id) VALUES ('$mid', '$cid')");
							}
						mysql_free_result($d_class);
						/* Only for profiles!!!!!!!!!!
						if($nextcrid){
							$nextbid='Inf';//This only functions EYFS profiles going to KS1....
							$d_class=mysql_query("SELECT id FROM class WHERE
									course_id LIKE '$nextcrid' AND subject_id LIKE
									'$nextbid' AND stage LIKE '$nextstage' ORDER BY subject_id;");
							while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
								$cid=$d_cid[0];
								mysql_query("INSERT INTO midcid (mark_id,class_id) VALUES ('$mid', '$cid')");
								}
							mysql_free_result($d_class);
							}
						*/
						}
					}
				}
			}

$returnXML=fetch_reportdefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
