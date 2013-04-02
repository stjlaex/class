<?php 
/**									 report_attendance_action.php
 *
 */

$action='report_attendance.php';
$action_post_vars=array('yid','formid','houseid','comid','wrapper_rid','date0','date1','reporttype');

if(isset($_POST['date0'])){$date0=$_POST['date0'];}else{$date0='';}
if(isset($_POST['date1'])){$date1=$_POST['date1'];}else{$date1='';}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid'])){$formid=$_POST['formid'];}
if(isset($_POST['houseid'])){$houseid=$_POST['houseid'];}else{$houseid='';}
if(isset($_POST['reporttype'])){$reporttype=$_POST['reporttype'];}else{$reporttype='';}

if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if($formid!=''){$comid=$formid;}
elseif($houseid!=''){$comid=$houseid;}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='report_attendance_list.php';
	}

include('scripts/redirect.php');
?>
