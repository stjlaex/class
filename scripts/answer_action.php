<?php 
/** 			   					answer_action.php
 */

if($_POST['answer']!='yes'){
	$current='';
	$action='';
	$choice='';
 	$result[]=get_string('noactiontaken');
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
?>