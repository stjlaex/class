<?php 
/**									   		form_edit_action.php
 */

$action='form_edit.php';
$action_post_vars=array('comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['sids'])){$oldsids=(array)$_POST['sids'];}
else{$sids=array();}
if(isset($_POST['classestoo'])){$classestoo=$_POST['classestoo'];}
else{$classestoo='no';}

include('scripts/sub_action.php');


	$community=get_community($comid);
	$fid=$community['name'];
	/*Check user has permission to edit*/
	$perm=getFormPerm($fid);
	$neededperm='w';
	include('scripts/perm_action.php');

if($sub=='Submit'){

	$changecids=(array)list_forms_classes($fid);

	/*sids to remove*/
	foreach($oldsids as $sid){
		$oldcommunities=join_community($sid,array('id'=>'','type'=>'form','name'=>''));
		$oldfid=$oldcommunities['form'][0]['name'];
		if($classestoo=='yes' and $oldfid==$fid){
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1;");
				}
			}
		}

	/*sids to add*/
	foreach($newsids as $sid){
		$oldcommunities=join_community($sid,$community);
		if(isset($oldcommunities['form'][0]['name'])){
			$oldfid=$oldcommunities['form'][0]['name'];}
		else{$oldfid='';}
		if($classestoo=='yes' and $oldfid!=$fid){
			$otherchangecids=array();
			$otherchangecids=list_forms_classes($oldfid);
			for($c=0;$c<sizeof($otherchangecids);$c++){
				$cid=$otherchangecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1;");
				}
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("INSERT INTO cidsid
		   				(student_id, class_id) VALUES ('$sid', '$cid');");
				}
			}
		}

	}

include('scripts/redirect.php');
?>
