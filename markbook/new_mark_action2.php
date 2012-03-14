<?php 
/**								new_mark_action2.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid');

$comment=clean_text($_POST['comment']);
$def_name=$_POST['def_name'];
$topic=clean_text($_POST['topic']);
$newcids=(array)$_POST['newcids'];
$entrydate=$_POST['date0'];
if(!isset($_POST['total'])){$total=0;}else{$total=clean_text($_POST['total']);}
if(!isset($_POST['newpid'])){$newpid='';}else{$newpid=$_POST['newpid'];}

include('scripts/sub_action.php');

	/*	Create the new entry in table:mark */
	mysql_query("INSERT INTO mark (entrydate, marktype, topic, total, comment, author, def_name, component_id) 
	     VALUES ('$entrydate', 'score', '$topic', '$total', '$comment', '$tid', '$def_name', '$newpid')");

	/* Get the new marks $mid */
	$mid=mysql_insert_id();
	$displaymid=$mid;
	/* Create an entry in table:midcid for each class */	
	foreach($newcids as $newcid){
			mysql_query("INSERT INTO midcid (mark_id, class_id) VALUES ('$mid','$newcid');");
			}

include('scripts/results.php');
include('scripts/redirect.php');
?>
