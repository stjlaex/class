<?php 
/**									 report_reports_action.php
 *
 */

$action='report_reports.php';
$action_post_vars=array('yid','formid','houseid','comid','wrapper_rid');

if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid'])){$formid=$_POST['formid'];}
if(isset($_POST['houseid'])){$houseid=$_POST['houseid'];}else{$houseid='';}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}else{$wrapper_rid='';}

if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if($formid!=''){$comid=$formid;}
elseif($houseid!=''){$comid=$houseid;}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='report_reports_list.php';
	}

include('scripts/redirect.php');
?>
