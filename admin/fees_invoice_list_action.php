<?php
/**									fees_invoice_list_action.php
 */

$action='fees_invoice_list.php';
$feeyear=$_POST['feeyear'];
$remid=$_POST['remid'];
if(isset($_POST['startno'])){$startno=$_POST['startno'];}

$action_post_vars=array('feeyear','startno','remid');


include('scripts/sub_action.php');

if(isset($_POST['nextrow']) and $_POST['nextrow']=='plus'){
	$startno=$startno+$_POST['nextrowstep'];
	}
elseif(isset($_POST['nextrow']) and $_POST['nextrow']=='minus'){
	$startno=$startno-$_POST['nextrowstep'];
	}

include('scripts/redirect.php');
?>
