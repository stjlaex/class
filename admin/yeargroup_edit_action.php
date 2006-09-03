<?php 
/**									   		form_edit_action.php
 */

$action='yeargroup_edit.php';

if(isset($_POST{'yid'})){$yid=$_POST{'yid'};}
if(isset($_POST{'newcomid'})){$newcomid=$_POST{'newcomid'};}else{$newcomid='';}
if(isset($_POST{'newtid'})){$newtid=$_POST{'newtid'};}
if(isset($_POST{'newsids'})){$newsids=(array)$_POST{'newsids'};}
else{$newsids=array();}
if(isset($_POST{'oldsids'})){$oldsids=(array)$_POST{'oldsids'};}
else{$oldsids=array();}

include('scripts/sub_action.php');

if($sub=='Unassign'){
   	mysql_query("UPDATE form SET teacher_id='' WHERE 
		teacher_id='$newtid' AND id='$fid'");
    $action=$cancel;
	}

elseif($sub=='Submit'){

	//    $changecids=array();
	//	$changecids=formsClasses($fid);
	$yearcommunity=array('type'=>'year','name'=>$yid);

	/*sids to remove*/
   	while(list($index,$sid)=each($oldsids)){
		leaveCommunity($sid,$yearcommunity);
		if($classestoo=='yes'){
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}

	/*sids to add*/
   	while(list($index,$sid)=each($newsids)){
		$oldyid=joinCommunity($sid,$yearcommunity);
		$result[]=$oldyid;
		if($classestoo=='yes'){
			if($oldfid!=''){
				$otherchangecids=array();
				$otherchangecids=formsClasses($oldfid);
				for($c=0;$c<sizeof($otherchangecids);$c++){
					$cid=$otherchangecids[$c];
					mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
					}
				}
			if($oldfid!=$fid){
				for($c=0;$c<sizeof($changecids);$c++){
					$cid=$changecids[$c];
					mysql_query("INSERT INTO cidsid
		   				(student_id, class_id) VALUES ('$sid', '$cid')");
					}
				}
			}
		}

	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
