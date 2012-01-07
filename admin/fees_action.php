<?php 
/**				   	   			   fees_action.php
 */

$action='fees.php';
$action_post_vars=array('feeyear');

include('scripts/sub_action.php');

if($sub=='Next'){
	$feeyear=$_POST['feeyear'];
	$feeyear++;
	}
elseif($sub=='Previous'){
	$feeyear=$_POST['feeyear'];
	$feeyear--;
	}

include('scripts/redirect.php');
?>
