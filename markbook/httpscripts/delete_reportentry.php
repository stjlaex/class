<?php
/**                    httpscripts/delete_reportentry.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($rid,$sid,$bid,$pid,$entn)=explode('-',$xmlid);
	$comn=$entn-1;/*TODO: the xmlid must have the real entryn not the index!!!!*/
	$reportdef=fetch_reportdefinition($rid);
	$Report['Comments']=fetchSkillLog($reportdef, $sid, $bid, $pid);
	$Comment=$Report['Comments']['Comment'][$comn];
	$entryn=$Comment['id_db'];
	$d_incidents=mysql_query("DELETE FROM report_skill_log WHERE
						 report_id='$rid' AND
						 student_id='$sid' LIMIT 1;");
$returnXML=array('id_db'=>$xmlid,'exists'=>'false');
$rootName='Comment';
require_once('../../scripts/http_end_options.php');
exit;
?>
