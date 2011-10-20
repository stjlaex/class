<?php 
/**									 report_assessments_action.php
 *
 */

$action='report_assessments.php';
$action_post_vars=array('year','stage','comid','yid','cids','limitbid','profid','profid','gender','eids','template');

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}
if(isset($_POST['profid']) and $_POST['profid']!=''){$profid=$_POST['profid'];}
if(isset($_POST['template'])){$template=$_POST['template'];}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}
if(isset($_POST['yid']) and $_POST['yid']!=''){$yid=$_POST['yid'];}
if(isset($_POST['limitbid']) and $_POST['limitbid']!=''){$limitbid=$_POST['limitbid'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['cids'])){$cids=(array)$_POST['cids'];}else{$cids=array();}


include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='report_assessments_view.php';
	}

include('scripts/redirect.php');
?>
