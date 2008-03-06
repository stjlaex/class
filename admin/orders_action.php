<?php 
/**				   	   			   orders_action.php
 */

$action='orders.php';
$action_post_vars=array('budgetyear');

include('scripts/sub_action.php');

if($sub=='Next'){
	$budgetyear=$_POST['budgetyear'];
	$budgetyear++;
	}
elseif($sub=='Previous'){
	$budgetyear=$_POST['budgetyear'];
	$budgetyear--;
	}

include('scripts/redirect.php');
?>
