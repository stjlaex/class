<?php
/**                    httpscripts/delete_report.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	$rid=$xmlid;

   	$d_midcid=mysql_query("DELETE midcid, mark FROM midcid JOIN mark ON
	   	mark.id=midcid.mark_id WHERE mark.midlist='$rid' AND mark.marktype='report'");
   	$d_midcid=mysql_query("DELETE FROM mark WHERE midlist='$rid' AND marktype='report'");
   	$d_ridcatid=mysql_query("DELETE FROM ridcatid WHERE report_id='$rid'");
   	$d_ridcatid=mysql_query("DELETE FROM ridcatid WHERE
								categorydef_id='$rid' AND subject_id='wrapper'");
   	$d_rideid=mysql_query("DELETE FROM rideid WHERE report_id='$rid'");
   	$d_reportentry=mysql_query("DELETE FROM reportentry WHERE report_id='$rid'");
   	$d_report=mysql_query("DELETE FROM report WHERE id='$rid'");

$returnXML=fetchReportDefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>

















