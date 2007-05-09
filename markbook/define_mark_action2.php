<?php 
/** 									define_mark_action2.php
 */

$action='new_mark.php';

include('scripts/sub_action.php');

$name=$_POST['name'];
$type=$_POST['type'];
$comment=$_POST['comment'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];
if(isset($_POST['gena'])){$gena=$_POST['gena'];}else{$gena='';}
if(isset($_POST['total'])){$total=$_POST['total'];}else{$total='';}

mysql_query("INSERT INTO markdef SET
	     name='$name', scoretype='$type', grading_name='$gena',
	     comment='$comment', outoftotal='$total', author='$tid', 
		course_id='$crid', subject_id='$bid'");

include('scripts/redirect.php');
?>
