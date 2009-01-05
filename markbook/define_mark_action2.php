<?php 
/** 									define_mark_action2.php
 */

$action='new_mark.php';

include('scripts/sub_action.php');

$name=clean_text($_POST['name']);
$type=clean_text($_POST['type']);
$comment=clean_text($_POST['comment']);
$crid=clean_text($_POST['crid']);
$bid=clean_text($_POST['bid']);
if(isset($_POST['gena'])){$gena=clean_text($_POST['gena']);}else{$gena='';}
if(isset($_POST['total'])){$total=clean_text($_POST['total']);}else{$total='';}

mysql_query("INSERT INTO markdef SET
	     name='$name', scoretype='$type', grading_name='$gena',
	     comment='$comment', outoftotal='$total', author='$tid', 
		course_id='$crid', subject_id='$bid'");

include('scripts/redirect.php');
?>
