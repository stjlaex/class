<?php
/**                    httpscripts/delete_assessment.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$eid=$xmlid;
   	$d_midcid=mysql_query("DELETE midcid, eidmid FROM midcid JOIN eidmid ON
	   	eidmid.mark_id=midcid.mark_id WHERE eidmid.assessment_id='$eid'");
   	$d_eidmid=mysql_query("DELETE FROM eidmid WHERE assessment_id='$eid'");
   	$d_eidsid=mysql_query("DELETE FROM eidsid WHERE assessment_id='$eid'");
   	$d_ass=mysql_query("DELETE FROM assessment WHERE id='$eid'");
   	$d_der=mysql_query("DELETE FROM derivation WHERE resultid='$eid'");

$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>

















