<?php
/**									fees_remittance_list_action.php
 */

$action='fees_remittance_list.php';
$choice='fees.php';

$feeyear=$_POST['feeyear'];
$paymenttype='';

if(isset($_GET['remid'])){$remid=$_GET['remid'];}else{$remid=-1;}
if(isset($_POST['recordid'])){$remid=$_POST['recordid'];}
$action_post_vars=array('feeyear','remid','paymenttype');

include('scripts/sub_action.php');

if($sub=='edit'){
	$action='fees_new_remittance.php';
	}
elseif($sub=='export'){
	$action='fees_remittance_export.php';
	}
elseif($sub=='invoice'){
	$action='fees_remittance_invoice.php';
	$paymenttype='';
	}
elseif($sub=='list'){
	$action='fees_invoice_list.php';
	$paymenttype='2';
	}
elseif($sub=='delete'){

	mysql_query("DELETE FROM fees_remittance WHERE id='$remid' LIMIT 1;");
	mysql_query("DELETE FROM fees_charge WHERE remittance_id='$remid';");

	}


include('scripts/redirect.php');
?>
