<?php
/**                    httpscripts/delete_assessment.php
 */

require_once('common.php');

if(isset($_GET{'eid'})){$eid=$_GET{'eid'};}
elseif(isset($_POST{'eid'})){$eid=$_POST{'eid'};}
else{print "Failed"; exit;}

   	$d_midcid=mysql_query("DELETE midcid, eidmid FROM midcid JOIN eidmid ON
	   	eidmid.mark_id=midcid.mark_id WHERE eidmid.assessment_id='$eid'");
   	$d_eidmid=mysql_query("DELETE FROM eidmid WHERE assessment_id='$eid'");
   	$d_eidsid=mysql_query("DELETE FROM eidsid WHERE assessment_id='$eid'");
   	$d_ass=mysql_query("DELETE FROM assessment WHERE id='$eid'");


$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('commonreturn.php');
exit;
?>

















