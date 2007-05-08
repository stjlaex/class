<?php 
/**									 community_group_edit_action.php
 */

$action='community_group_edit.php';
$action_post_vars=array('newcomtype','newcomid','comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['oldsids'])){$oldsids=(array)$_POST['oldsids'];}
else{$oldsids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){

	/*sids to remove*/
	$currentcommunity=array('type'=>$newcomtype,'id'=>$comid);
   	while(list($index,$sid)=each($oldsids)){
		$oldcommunities=leave_community($sid,$currentcommunity);
		}

	/*sids to add*/
   	while(list($index,$sid)=each($newsids)){
		$oldcommunities=join_community($sid,$currentcommunity);
		}
	}

include('scripts/redirect.php');
?>
