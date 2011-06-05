<?php
/**                    httpscripts/generate_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print 'Failed'; exit;}

$rid=$xmlid;
$ReportDef=fetch_reportdefinition($rid);
$crid=$ReportDef['report']['course_id'];
$stage=$ReportDef['report']['stage'];
$substatus=$ReportDef['report']['subject_status'];
$compstatus=$ReportDef['report']['component_status'];
$title=$ReportDef['report']['title'];
$date=$ReportDef['report']['date'];
$deadline=$ReportDef['report']['deadline'];
if($ReportDef['report']['addcomment']=='no' and $ReportDef['report']['addcategory']=='yes'){
	$marktype='compound';
	}
else{
	$marktype='report';
	}

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
	$subjects=array();
	if($substatus=='G' or $substatus=='%'){$subjects[]=array('id'=>'%');}/*special when bid is general*/
	else{$subjects=list_course_subjects($crid,$substatus);}

		/* Generate a report column for each subject*/
   	   	foreach($subjects as $subject){
			$bid=$subject['id'];
			$components=(array)list_subject_components($bid,$crid,$compstatus);
			if(sizeof($components)==0){$components[0]['id']='';}
			foreach($components as $component){
				/* If its a compound report then it will also work
				 *   across strands with the same comp status as the
				 *   components. TODO: Need to specify strand status
				 *   explicity for reports.
				 */
				if($marktype=='compound'){$strands=(array)list_subject_components($component['id'],$crid,$compstatus);}else{$strands=array();}
				if(sizeof($strands)==0){$strands[0]['id']=$component['id'];}
				foreach($strands as $strand){

					$pid=$strand['id'];
					
					/* NB. If there is no component for this subject or components are not
					   requested then $pid is blank. */
					/* The rid is stored in the midlist field for each mark*/
					mysql_query("INSERT INTO mark 
				(entrydate, marktype, topic, comment, author,
				 def_name, assessment, midlist, component_id) 
					VALUES ('$date', '$marktype', '$title', 
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
			}

$returnXML=fetch_reportdefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>
