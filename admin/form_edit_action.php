<?php 
/**									   		form_edit_action.php
 *
 *
 */

$action='form_edit.php';
$action_post_vars=array('comid');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['sids'])){$oldsids=(array)$_POST['sids'];}
else{$oldsids=array();}
if(isset($_POST['classestoo'])){$classestoo=$_POST['classestoo'];}
else{$classestoo='no';}

include('scripts/sub_action.php');


$community=get_community($comid);
$formname=$community['name'];
/*Check user has permission to edit*/
$perm=getFormPerm($formname);
$neededperm='w';
include('scripts/perm_action.php');


if($sub=='Submit'){

	if($classestoo=='yes'){
		$changeclasses=(array)list_forms_classes($formname);
		}

	/*sids to remove*/
	foreach($oldsids as $sid){

		$oldcommunities=join_community($sid,array('id'=>'','type'=>'form','name'=>''));

		if(isset($oldcommunities['form'][0]['name'])){
			$oldformname=$oldcommunities['form'][0]['name'];
			}
		else{
			$oldformname='';
			}

		if($classestoo=='yes' and $oldformname==$formname){
			foreach($changeclasses as $class){
				$cid=$class['id'];
				mysql_query("DELETE FROM cidsid WHERE student_id='$sid' AND class_id='$cid' LIMIT 1;");
				}
			}

		}

	/*sids to add*/
	foreach($newsids as $sid){

		$oldcommunities=join_community($sid,$community);

		if(isset($oldcommunities['form'][0]['name'])){
			$oldformname=$oldcommunities['form'][0]['name'];
			}
		else{
			$oldformname='';
			}

		if($classestoo=='yes' and $oldformname!=$formname){
			$other_changeclasses=(array)list_forms_classes($oldformname);
			foreach($other_changeclasses as $class){
				$cid=$class['id'];
				mysql_query("DELETE FROM cidsid WHERE student_id='$sid' AND class_id='$cid' LIMIT 1;");
				}
			foreach($changeclasses as $class){
				$cid=$class['id'];
				mysql_query("INSERT INTO cidsid (student_id, class_id) VALUES ('$sid', '$cid');");
				}
			}
		}

	}

include('scripts/redirect.php');
?>
