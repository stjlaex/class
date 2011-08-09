<?php 
/**									 community_group_edit_action.php
 */

$action='community_group_edit.php';
$action_post_vars=array('newcomtype','newcomid','comid','yid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['oldsids'])){$oldsids=(array)$_POST['oldsids'];}
else{$oldsids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){

	if($newcomtype=='TRANSPORT'){
		$currentcommunity=get_community($comid);
		$buses=(array)list_buses('%','%',$currentcommunity['name']);

		foreach($newsids as $sid){
			foreach($buses as $bus){
				add_journey_booking($sid,$bus['id'],0,'','every');
				}
			}

		//$oldcommunities=set_community_stay($sid,$currentcommunity,$startdate,$enddate);
		//delete_journey_booking($sid,$bookid);
		}
	else{

		$currentcommunity=array('type'=>$newcomtype,'id'=>$comid);

		/*sids to remove*/
		while(list($index,$sid)=each($oldsids)){
			$oldcommunities=leave_community($sid,$currentcommunity);
			}
		/*sids to add*/
		while(list($index,$sid)=each($newsids)){
			$oldcommunities=join_community($sid,$currentcommunity);
			}
		}

	}

include('scripts/redirect.php');
?>
