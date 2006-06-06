<?php 
/** 									column_edit_action.php
 */

$action='class_view.php';

$mid=$_POST['mid'];
$total=clean_text($_POST['total']);
$oldcids=$_POST['newcids'];
$newcids=$_POST['selcids'];
$topic=clean_text($_POST['topic']);
$comment=clean_text($_POST['comment']);
if(isset($_POST['assbut'])){$assbut=$_POST['assbut'];}else{$assbut='';}
if(isset($_POST['newpid'])){$newpid=$_POST['newpid'];}else{$newpid='';}
$hidden=$_POST['hidden'];

include('scripts/sub_action.php');

	if($sub=='Submit'){
		$entrydate=$_POST['date0'];
		if(!isset($total)){$total=0;}	
		if(mysql_query("UPDATE mark SET
	     entrydate='$entrydate', topic='$topic', total='$total',
				hidden='$hidden', comment='$comment', component_id='$newpid' WHERE id='$mid'"))
	     {}
	     else{$result[]='Failed mark may not exist!';	
					$error[]=mysql_error();}
			

		for($c=0;$c<sizeof($oldcids);$c++){
			$oldcid=$oldcids[$c];
			/*check for those cids deselected and delete from midcid*/
			if(!in_array($oldcid,$newcids)){
				if(mysql_query("DELETE FROM midcid WHERE mark_id='$mid'
					AND class_id='$oldcid' LIMIT 1")){}
				else{$error[]=mysql_error();}
				}
			}

		$currentcids=array();
		$d_cids = mysql_query("SELECT class_id  FROM midcid 
				WHERE mark_id='$mid' ORDER BY class_id");
		while($cid = mysql_fetch_array($d_cids,MYSQL_ASSOC)){
			$currentcids[]=$cid['class_id'];
			}
		for($c=0;$c<sizeof($newcids);$c++){
			$newcid=$newcids[$c];
			/*check for those cids newly selected and add to midcid*/
			if(!in_array($newcid,$currentcids)){
				if(mysql_query("INSERT INTO midcid SET mark_id='$mid',
					class_id='$newcid'")){}
				else{$error[]=mysql_error();}
				}
			}
		}

	elseif($assbut=='Assess'){
		if(mysql_query("UPDATE mark SET
	     assessment='yes' WHERE id='$mid'")){}
	     else{$result[]='Failed mark may not exist!';	
					$error[]=mysql_error();}
		$action='column_edit.php';
		}

	elseif($assbut=='Unassess'){
	  $eid=$_POST{'eid'};
	  if($eid=='unassess'){
			 mysql_query("DELETE FROM eidmid  
				WHERE mark_id='$mid' LIMIT 1");
			 if(mysql_query("UPDATE mark SET
				assessment='no' WHERE id='$mid'")){}
			 else{$result[]='Failed mark may not exist!';	
					$error[]=mysql_error();}
			 }
	  else{
			 if(mysql_query("INSERT INTO eidmid SET
				assessment_id='$eid', mark_id='$mid'")){}
			 else{$result[]='Failed mark may not exist!';	
					$error[]=mysql_error();}
			}
		$action='column_edit.php';
		}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>