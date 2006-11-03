<?php
/**                    httpscripts/delete_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$rid=$xmlid;
   	$d_midcid=mysql_query("DELETE midcid, mark FROM midcid JOIN mark ON
	   	mark.id=midcid.mark_id WHERE mark.midlist='$rid' AND mark.marktype='report'");
   	$d_midcid=mysql_query("DELETE FROM mark
	   	WHERE midlist='$rid' AND marktype='report'");

$returnXML=fetchReportDefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>

















