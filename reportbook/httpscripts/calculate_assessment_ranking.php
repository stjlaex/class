<?php
/**                    httpscripts/calculate_assessment_ranking.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$eid=$xmlid;
	$AssDef=fetchAssessmentDefinition($eid);
	$crid=$AssDef['Course']['value'];
	$year=$AssDef['Year']['value'];
	$stage=$AssDef['Stage']['value'];
	$dertype='R';
	$der=$AssDef['Derivation']['value'];

	$steps=(array)derive_accumulator_steps($der,$eid);
	$cohorts=(array)list_course_cohorts($crid,$year);

	if($steps[0]['operation']=='RANK'){compute_assessment_ranking($AssDef,$steps,$cohorts);}


$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
