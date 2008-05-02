<?php 
/** 			   					answer_action.php
 *
 * For the post result from the check_yesno.php script.
 *
 */

if($_POST['answer0']!='yes'){
	$current='';
	$action='';
	$choice='';
 	$result[]=get_string('noactiontaken');
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
?>