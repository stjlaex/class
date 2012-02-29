<?php
/**									register_notice_action.php
 *
 */

$action='completion_list.php';


if(isset($_POST['noticebody'])){$noticebody=clean_text($_POST['noticebody']);}
if(isset($_POST['date0'])){$noticedate=$_POST['date0'];}else{$noticedate=$currentevent['date'];}
if(isset($_POST['session'])){$noticesession=$_POST['session'];}else{$noticesession='AM';}
if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}else{$comids=array();}

include('scripts/sub_action.php');

if($sub==''){
	$cancel=$action;
	include('scripts/redirect.php');
	exit;
	}

if($sub=='Submit' and sizeof($comids)>0){

	$d_n=mysql_query("INSERT INTO event_notice (date,session,comment) VALUES ('$noticedate','$noticesession','$noticebody');");

	$notid=mysql_insert_id();
	foreach($comids as $comid){
		list($comid,$yid)=explode(':::',$comid);
		$d_n=mysql_query("INSERT INTO event_notidcomid (notice_id,community_id,yeargroup_id) VALUES ('$notid','$comid','$yid');");
		}


	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
