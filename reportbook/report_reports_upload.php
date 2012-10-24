<?php
/**
 *									report_reports_upload.php
 *
 * Upload a file...
 *
 */

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

require_once('lib/eportfolio_functions.php');
include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

$doingepf=true;
$pubtype='pdf';


foreach($sids as $sid){

	$filename='grade_descriptor.pdf';
	$S=fetchStudent_singlefield($sid,'EPFUsername');
	$epfusername=$S['EPFUsername']['value'];	
	$targetdir='files/' . substr($epfusername,0,1) . '/' . $epfusername;

	$publishdata=array();
	$publish_batch=array();
	$publishdata['foldertype']='report';
	$publishdata['description']='descriptors';
	$publishdata['title']='Grade dscriptors for reports.';
	$publish_batch[]=array('epfusername'=>$epfusername,'filename'=>$filename);
	$publishdata['batchfiles']=$publish_batch;
	if(elgg_upload_files($publishdata,true)){
		}
	else{
		$success=false;
		}
	}

include('scripts/redirect.php');
?>
