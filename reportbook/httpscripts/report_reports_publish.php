<?php
/**
 *									httpscripts/report_reports_publish.php
 *
 * Publishes the selected reports to html files
 * and then schedules a cron event for pdf conversion 
 * (if the html2ps package is available through $CFG -> html2psscript)
 *
 */

require_once('../../scripts/http_head_options.php');


if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
if(isset($_GET['rids'])){$rids=(array)$_GET['rids'];}else{$rids=array();}
if(isset($_POST['rids'])){$rids=(array)$_POST['rids'];}
if(isset($_GET['wrapper_rid'])){$wrapper_rid=$_GET['wrapper_rid'];}else{$wrapper_rid=$rids[0];}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}


	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		if($wrapper_rid!=''){
			$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
		$rids=array();
		$rids[]=$wrapper_rid;
		while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
			$rids[]=$rid['report_id'];
			}
		}


		/* Find the definition specific to each report */
		$reportdefs=array();
		foreach($rids as $rid){ 
			$reportdefs[]=fetch_reportdefinition($rid);

			/* TODO: need to lock individual reports not just wrapper? */
			}
		
		$pubdate=$reportdefs[0]['report']['date'];
		foreach($sids as $sid){
			/* Log to the event table for publication. */
			if(mysql_query("INSERT INTO report_event SET report_id='$wrapper_rid', 
							student_id='$sid',date='$pubdate',success='0';")){}
			else{mysql_query("UPDATE report_event SET success='0', try='0' 
					WHERE report_id='$wrapper_rid' AND student_id='$sid';");}
			}

		$returnXML=$sids;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>