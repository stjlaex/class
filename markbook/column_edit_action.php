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
if(isset($_POST['newpid'])){$newpid=$_POST['newpid'];}else{$newpid='';}

include('scripts/sub_action.php');

	if($sub=='Submit'){
		$entrydate=$_POST['date0'];
		if(!isset($total)){$total=0;}
		mysql_query("UPDATE mark SET
			entrydate='$entrydate', topic='$topic', total='$total',
			comment='$comment', component_id='$newpid' WHERE id='$mid'");

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

	include('scripts/results.php');
	include('scripts/redirect.php');
?>