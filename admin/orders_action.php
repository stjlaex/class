<?php 
/**				   	   			   orders_action.php
 */

$action='orders.php';

include('scripts/sub_action.php');

if($sub=='search'){
	$action_post_vars=array('ordernumber');
	if(isset($_POST['ordernumber'])){$ordernumber=$_POST['ordernumber'];}
	$action='orders_list.php';
	}

include('scripts/redirect.php');
?>
