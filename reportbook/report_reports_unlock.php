<?php
/**
 *									report_reports_unlock.php
 *
 * Unlocks a published report
 *
 */
require_once($CFG->dirroot.'/lib/eportfolio_functions.php');

$action='report_reports_list.php';
$action_post_vars=array('wrapper_rid','yid','comid');

if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
if(isset($_GET['rids'])){$rids=(array)$_GET['rids'];}else{$rids=array();}
if(isset($_POST['rids'])){$rids=(array)$_POST['rids'];}
if(isset($_GET['wrapper_rid'])){$wrapper_rid=$_GET['wrapper_rid'];}else{$wrapper_rid=$rids[0];}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

if(isset($CFG->wkhtml2pdf) and $CFG->wkhtml2pdf!=''){
	$pubtype='pdf';
	$pubmethod='wkhtml2pdf';
	}
else{
	$pubtype='html';
	}

$reportdef=(array)fetch_reportdefinition($wrapper_rid);
$pubdate=$reportdef['report']['date'];

foreach($sids as $sid){
	/* Log to the event table for publication. */

	$d_r=mysql_query("SELECT success FROM report_event
					WHERE report_id='$wrapper_rid' AND student_id='$sid';");
	if(mysql_num_rows($d_r)>0){
		$pub=mysql_result($d_r,0);
		if($pub==1){
			/* The report has already been published to file so need to delete and unpublish. */
			mysql_query("UPDATE report_event SET success='0', try='0' 
					WHERE report_id='$wrapper_rid' AND student_id='$sid';");

			$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
			$S=fetchStudent_singlefield($sid,'EPFUsername');
			$epfusername=$S['EPFUsername']['value'];

			$targetdir='files/' . substr($epfusername,0,1) . '/' . $epfusername;
			unlink($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.'.$pubtype);
			unlink($CFG->eportfolio_dataroot.'/'.$targetdir.'/'.$filename.'.'.$pubtype);
			}
		else{
			mysql_query("DELETE FROM report_event  
					WHERE report_id='$wrapper_rid' AND student_id='$sid' LIMIT 1;");
			}
		}
	}

include('scripts/redirect.php');
?>
