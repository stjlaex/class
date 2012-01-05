<?php
/**			  					new_staff_action.php
 *
 */

$action='new_staff.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	$user=array();
	$user['username']=$_POST['newtid'];
	$user['userno']=$_POST['no'];
	$user['surname']=$_POST['surname'];
	$user['forename']=$_POST['forename'];
	$user['email']=$_POST['email'];
	$user['role']=$_POST['role'];
	$result[]=update_user($user,'no',$CFG->shortkeyword);
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
