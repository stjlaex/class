<?php
/**                    httpscripts/delete_reportentry.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($rid,$sid,$bid,$pid,$entn)=explode('-',$xmlid);
	$comn=$entn-1;/*TODO: the xmlid must have the real entryn not the index!!!!*/
	$reportdef=fetch_reportdefinition($rid);
	$Report['Comments']=fetchReportEntry($reportdef, $sid, $bid, $pid);
	$Comment=$Report['Comments']['Comment'][$comn];
	$entryn=$Comment['id_db'];
	$d_incidents=mysql_query("DELETE FROM reportentry WHERE
							report_id='$rid' AND
							student_id='$sid' AND subject_id='$bid' AND
							component_id='$pid' AND entryn='$entryn' LIMIT 1;");

$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Comment';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>
