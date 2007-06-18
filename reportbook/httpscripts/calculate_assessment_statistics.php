<?php
/**                    httpscripts/calculate_assessment_statistics.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$eid=$xmlid;
	$AssDef=fetchAssessmentDefinition($eid);
	$crid=$AssDef['Course']['value'];
	$gena=$AssDef['GradingScheme']['value'];
	$grading_grades=$AssDef['GradingScheme']['grades'];
	$element=$AssDef['Element']['value'];
	$assdescription=$AssDef['Description']['value'];
	$assstage=$AssDef['Stage']['value'];
	$assyear=$AssDef['Year']['value'];
	$dertype='S';
	mysql_query("UPDATE assessment SET statistics='Y' WHERE id='$eid'");

	$step=array('op'=>'+','operation'=>'AVE','operandids'=>array($eid));
	$steps[]=$step;
	$accumulators=array();
	$cohorts=(array)list_course_cohorts($crid,$assyear);
	while(list($index,$cohort)=each($cohorts)){
		$cohid=$cohort['id'];
		$cohortstage=$cohort['stage'];
		if($assstage=='%' or $assstage==$cohortstage){
			/* need to check if S entries for this eid exist in derivation and if not create*/
			/* new assessments for this cohortstage, take the new eid as resultid*/
			/* and insert into derivation for operandid=eid, type=S. There is one */
			/* for each stage and the derivation field element records the stage*/
			$d_der=mysql_query("SELECT resultid FROM derivation WHERE
				operandid='$eid' AND type='S' AND element='$cohortstage'");
			if(mysql_num_rows($d_der)==0){
				$todate=date('Y-m-d');
				mysql_query("INSERT INTO assessment (stage, year, subject_id,  
					description, course_id, resultstatus,
					component_status, label, grading_name, creation) 
					VALUES ('$cohortstage', '$assyear', '%',
					'Averages ($assdescription)', '$crid', 'S',
					'A', 'Average', '$gena', '$todate');");
				$resultid=mysql_insert_id();
				mysql_query("INSERT INTO derivation (resultid,
					operandid, type, element) VALUES ('$resultid','$eid','S','$cohortstage')");
				}
			else{
				$resultid=mysql_result($d_der,0);
				}

			$accumulators=array();
			$cohortstudents=(array)listin_cohort($cohort);
			trigger_error('Cohort '.$cohortstage.' cohid '.$cohid .' students
			'. sizeof($cohortstudents) ,E_USER_WARNING);
			while(list($index,$student)=each($cohortstudents)){
				$sid=$student['id'];
				$accumulators=compute_accumulators($sid,$AssDef,$steps,$accumulators);

				if($accumulators['G']['count']>0){
					$value=$accumulators['G']['value']/$accumulators['G']['count'];
					$value=round($value,2);
					if($grading_grades!=' ' and $grading_grades!=''){
						$res=scoreToGrade($value,$grading_grades);
						}
					else{
						$res=$value;
						}
					$score=array('result'=>$res,'value'=>$value);
					update_assessment_score($resultid,$sid,'G','',$score);
					}
				}

			reset($accumulators);
			while(list($bid,$componentaccs)=each($accumulators)){
				reset($componentaccs);
				while(list($pid,$acc)=each($componentaccs)){
					if($pid==' '){$pid='';}
					if($acc['count']>0){
						$value=$acc['value']/$acc['count'];
						$value=round($value,2);
						if($grading_grades!=' ' and $grading_grades!=''){
							$res=scoreToGrade($value,$grading_grades);
							}
						else{
							$res=$value;
							}
						$score=array('result'=>$res,'value'=>$value);
						/*all subject averagess go into eidsid with sid=0*/
						update_assessment_score($resultid,0,$bid,$pid,$score);
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
