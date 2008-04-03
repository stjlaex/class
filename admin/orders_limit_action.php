<?php 
/**									 orders_limit_action.php
 */

$action='orders.php';
//$action_post_vars=array('');

if(isset($_POST['budid'])){$budid=$_POST['budid'];}
if(isset($_POST['limit'])){$limit=$_POST['limit'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	mysql_query("UPDATE orderbudget SET costlimit='$limit' WHERE id='$budid';");
	}

include('scripts/redirect.php');
?>
