<?php
/**                    httpscripts/delete_report_columns.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

$rid=$xmlid;
$ReportDef=fetch_reportdefinition($rid);
$crid=$ReportDef['report']['course_id'];
$year=$ReportDef['report']['year'];
//$curryear=get_curriculumyear($crid);
$stages=array();
if($ReportDef['report']['stage']=='%'){
	$stages=list_course_stages($crid);
	}
else{
	$stages[]=array('id'=>$ReportDef['report']['stage']);
	}


foreach($stages as $stage){
	$cohort=array('course_id'=>$crid,'stage'=>$stage['id'],'year'=>$year);
	$cohid=update_cohort($cohort);
   	$d_m=mysql_query("SELECT id FROM mark WHERE midlist='$rid' 
				AND (marktype='report' OR marktype='compound');");
	while($m=mysql_fetch_array($d_m,MYSQL_NUM)){
		$mid=$m[0];
		$d_midcid=mysql_query("DELETE FROM midcid WHERE midcid.mark_id='$mid' 
						AND midcid.class_id=ANY(SELECT id FROM class WHERE cohort_id='$cohid');");
		$d_count=mysql_query("SELECT COUNT(class_id) FROM midcid WHERE mark_id='$mid';");
		$count=mysql_result($d_count,0);
		if($count==0){
			mysql_query("DELETE FROM mark WHERE id='$mid';");
			}
		}
	}

$returnXML=fetch_reportdefinition($rid);
$rootName='ReportDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>

















