<?php
/**                    httpscripts/calculate_assessment_statistics.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$eid=$xmlid;
	$AssDef=fetchAssessmentDefinition($eid);
	$crid=$AssDef['Course']['value'];
	$grading_grades=$AssDef['GradingScheme']['grades'];
	$step=array('op'=>'+','operation'=>'AVE','operandids'=>array($eid));
	$steps[]=$step;
	$accumulators=array();
	$d_ass=mysql_query("SELECT DISTINCT student_id FROM eidsid
				WHERE assessment_id='$eid'");
	while($ass=mysql_fetch_array($d_ass,MYSQL_NUM)){
		$sid=$ass[0];
		$accumulators=compute_accumulators($sid,$AssDef,$steps,$accumulators);
		}
	mysql_free_result($d_ass);

	reset($accumulators);
	while(list($bid,$componentaccs)=each($accumulators)){
		reset($componentaccs);
		while(list($pid,$acc)=each($componentaccs)){
			if($pid==' '){$pid='';}
			$value=$acc['value']/$acc['count'];
			$value=round($value);
			if($grading_grades!=' ' and $grading_grades!=''){
				$res=scoreToGrade($value,$grading_grades);
				}
			else{
				$res=$value;
				}
			if($bid=='Art'){trigger_error('Subject '.$bid.' '.$pid. '
	count'.$acc['count']. ' value '.$acc['value']. ' result '.$res,E_USER_WARNING);}
			$score=array('result'=>$res,'value'=>$value);
			/*all statistics go into eidsid with sid=0*/
			update_assessment_score($eid,0,$bid,$pid,$score);
			}
		}

	mysql_query("UPDATE assessment SET statistics='Y' WHERE id='$eid'");

$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
