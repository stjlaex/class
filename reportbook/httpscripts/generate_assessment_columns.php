<?php
/**                    httpscripts/generate_assessment_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$eid=$xmlid;

$AssDef=generate_assessment_columns($eid);


$AssCount=(array)fetchAssessmentCount($eid);
$returnXML=array_merge($AssDef,$AssCount);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
