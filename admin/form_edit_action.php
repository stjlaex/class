<?php 
/**									   		form_edit_action.php
 */

$action='form_edit.php';

if(isset($_POST['fid'])){$fid=$_POST['fid'];}
if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}
if(isset($_POST['newsids'])){$newsids=(array)$_POST['newsids'];}
else{$newsids=array();}
if(isset($_POST['oldsids'])){$oldsids=(array)$_POST['oldsids'];}
else{$oldsids=array();}
if(isset($_POST['classestoo'])){$classestoo=$_POST['classestoo'];}

include('scripts/sub_action.php');

if($sub=='Submit'){

    $changecids=array();
	$changecids=formsClasses($fid);

	/*sids to remove*/
   	while(list($index,$sid)=each($oldsids)){
		$oldcommunities=joinCommunity($sid,array('type'=>'form','name'=>''));
		$oldfid=$oldcommunities['form'][0]['name'];
		if($classestoo=='yes' and $oldfid==$fid){
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			}
		}

	/*sids to add*/
   	while(list($index,$sid)=each($newsids)){
		$oldcommunities=joinCommunity($sid,array('type'=>'form','name'=>$fid));
		$oldfid=$oldcommunities['form'][0]['name'];
		if($classestoo=='yes' and $oldfid!=$fid){
			$otherchangecids=array();
			$otherchangecids=formsClasses($oldfid);
			for($c=0;$c<sizeof($otherchangecids);$c++){
				$cid=$otherchangecids[$c];
				mysql_query("DELETE FROM cidsid WHERE
								student_id='$sid' AND class_id='$cid' LIMIT 1");
				}
			for($c=0;$c<sizeof($changecids);$c++){
				$cid=$changecids[$c];
				mysql_query("INSERT INTO cidsid
		   				(student_id, class_id) VALUES ('$sid', '$cid')");
				}
			}
		}

	}

include('scripts/redirect.php');
?>
