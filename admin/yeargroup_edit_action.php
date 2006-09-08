<?php 
/**									   		yeargroup_edit_action.php
 */

$action='yeargroup_edit.php';

if(isset($_POST['yid'])){$yid=$_POST['yid'];}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['oldsids'])){$oldsids=(array)$_POST['oldsids'];}
else{$oldsids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){
	/*not currently offering this option to the user*/
	/*should really always be yes, surely?*/
	$classestoo='yes';

	/*sids to remove*/
	$yearcommunity=array('type'=>'year','name'=>'');
   	while(list($index,$sid)=each($oldsids)){
		$oldcommunities=joinCommunity($sid,$yearcommunity);
		$oldfid=$oldcommunities['form'][0]['name'];
		if($classestoo=='yes' and $oldfid!=''){
			$changecids=array();
			$changecids=formsClasses($oldfid);
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
		   				student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}

	/*sids to add*/
	$yearcommunity=array('type'=>'year','name'=>$yid);
   	while(list($index,$sid)=each($newsids)){
		$oldcommunities=joinCommunity($sid,$yearcommunity);
		$oldfid=$oldcommunities['form'][0]['name'];
		if($classestoo=='yes' and $oldfid!=''){
			$changecids=array();
			$changecids=formsClasses($oldfid);
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$otherchangecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}
	}

include('scripts/redirect.php');
?>
