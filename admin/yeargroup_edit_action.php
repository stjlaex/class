<?php 
/**									   		yeargroup_edit_action.php
 */

$action='yeargroup_edit.php';
$action_post_vars=array('newcomid','comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['oldsids'])){$oldsids=(array)$_POST['oldsids'];}
else{$oldsids=array();}

include('scripts/sub_action.php');

		/*Check user has permission to edit*/
		$perm=getYearPerm($comname,$respons);
		$neededperm='w';
		include('scripts/perm_action.php');

if($sub=='Submit'){
	/*not currently offering this option to the user*/
	/*should really always be yes, surely?*/
	$classestoo='yes';

	/*sids to remove*/
	//$newcommunity=array('type'=>'year','name'=>'');
	$newcommunity=get_community($newcomid);
   	while(list($index,$sid)=each($oldsids)){
		$oldcommunities=join_community($sid,$newcommunity);
		if($classestoo=='yes' and isset($oldcommunities['form'])){
			$changecids=array();
			$changecids=list_forms_classes($oldcommunities['form'][0]['name']);
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
		   				student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}

	/*sids to add*/
	$currentcommunity=get_community($comid);
   	while(list($index,$sid)=each($newsids)){
		$oldcommunities=join_community($sid,$currentcommunity);
		if($classestoo=='yes' and isset($oldcommunities['form'])){
			$changecids=array();
			$changecids=list_forms_classes($oldcommunities['form'][0]['name']);
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}
	}

include('scripts/redirect.php');
?>
