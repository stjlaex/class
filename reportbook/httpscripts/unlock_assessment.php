<?php
/**                                      httpscripts/block_assessment.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$eid=$xmlid;

mysql_query("UPDATE assessment SET lock_level='0' WHERE id='$eid';");


$AssCount=(array)fetchAssessmentCount($eid);
$returnXML=array_merge($AssDef,$AssCount);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
