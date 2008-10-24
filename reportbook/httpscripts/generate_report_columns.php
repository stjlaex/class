<?php
/**                    httpscripts/generate_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$rid=$xmlid;
$ReportDef=fetch_reportdefinition($rid);
$crid=$ReportDef['report']['course_id'];
$stage=$ReportDef['report']['stage'];
$compstatus=$ReportDef['report']['component_status'];
$title=$ReportDef['report']['title'];
$date=$ReportDef['report']['date'];
$deadline=$ReportDef['report']['deadline'];

		/*EXPERIMENTAL! and no longer required.*/
		/* First generate the associated assessment columns for this report
   	   	while(list($index,$eid)=each($ReportDef['eids'])){
			$url='http://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/reportbook/httpscripts/generate_assessment_columns.php?uniqueid='.$eid;
			//trigger_error('url:'.$url,E_USER_WARNING);
			$postdata['process_mode']='batch';
			$curl=curl_init();
			curl_setopt($curl,CURLOPT_URL,$url);
			$result[]=curl_exec($curl);
			curl_close($curl);
			}
		*/

		/* Make a list of subjects that will need distinct new marks*/
		$subjects=list_course_subjects($crid);

		/* Generate a report column for each subject*/
   	   	while(list($index,$subject)=each($subjects)){
			$bid=$subject['id'];
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
				/* NB. If there is no component for this subject or components are not
					requested then $pid is blank. */
				/* The rid is stored in the midlist field for each mark*/
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

$returnXML=fetch_reportdefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
