<?php
/**                    httpscripts/generate_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

$rid=$xmlid;
$ReportDef=fetchReportDefinition($rid);
$crid=$ReportDef['report']['course_id'];
$stage=$ReportDef['report']['stage'];
$compstatus=$ReportDef['report']['component_status'];
$title=$ReportDef['report']['title'];
$date=$ReportDef['report']['date'];
$deadline=$ReportDef['report']['deadline'];

		/*the rid is stored in the midlist field for each mark*/

		/*make a list of subjects that will need distinct new marks*/
		$bids=array();
		$d_cridbid=mysql_query("SELECT DISTINCT subject_id FROM cridbid
				WHERE course_id LIKE '$crid' ORDER BY subject_id");
		while($bid=mysql_fetch_array($d_cridbid,MYSQL_NUM)){
			$bids[]=$bid[0];
			}
		mysql_free_result($d_cridbid);

		/*generate a mark for each crid and bid combination*/
   	   	while(list($index,$bid)=each($bids)){
			$pids=array();
   			if($compstatus=='A'){$compstatus='%';}
   			$d_component=mysql_query("SELECT DISTINCT id FROM component
					WHERE course_id='$crid' AND subject_id='$bid'
						AND status LIKE '$compstatus'");
   			while($pid=mysql_fetch_array($d_component,MYSQL_NUM)){
				$pids[]=$pid[0];
				}
 			mysql_free_result($d_component);

			if(sizeof($pids)==0){$pids[0]='';}
		   	while(list($index,$pid)=each($pids)){
				/*if there is no component for this subject or componenets are not
					requested then $pid is blank*/
				mysql_query("INSERT INTO mark 
				(entrydate, marktype, topic, comment, author,
				 def_name, assessment, midlist, component_id) 
					VALUES ('$date', 'report', '$title', 
				 'complete by $deadline', 'ClaSS', '', 'no', '$rid', '$pid')");
				$mid=mysql_insert_id();

				/*entry in midcid for new mark and classes with crid and bid*/
				$d_class=mysql_query("SELECT id FROM class WHERE
						course_id LIKE '$crid' AND subject_id LIKE
						'$bid' AND stage LIKE '$stage' ORDER BY subject_id");
				while($d_cid=mysql_fetch_array($d_class,MYSQL_NUM)){
						$cid=$d_cid[0];
						mysql_query("INSERT INTO midcid (mark_id,
							class_id) VALUES ('$mid', '$cid')");
						}
				mysql_free_result($d_class);
				}
			}

$returnXML=fetchReportDefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
