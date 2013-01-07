<?php
/**									fees_remittance_view_action.php
 */

$action='fees_remittance_view.php';
$feeyear=$_POST['feeyear'];
$action_post_vars=array('feeyear','remid','paymenttype','conid','payment');

if(isset($_POST['sids'])){$charids=(array)$_POST['sids'];}else{$charids=array();}

if((isset($_POST['conid']) and $_POST['conid']!='')){$conid=$_POST['conid'];}else{$conid='';}
if((isset($_GET['conid']) and $_GET['conid']!='')){$conid=$_GET['conid'];}
if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}
if((isset($_POST['payment']) and $_POST['payment']!='')){$payment=$_POST['payment'];}else{$payment='';}
if((isset($_GET['payment']) and $_GET['payment']!='')){$payment=$_GET['payment'];}
if((isset($_POST['paymenttype']) and $_POST['paymenttype']!='')){$paymenttype=$_POST['paymenttype'];}else{$paymenttype='';}
if((isset($_GET['paymenttype']) and $_GET['paymenttype']!='')){$paymenttype=$_GET['paymenttype'];}


include('scripts/sub_action.php');

if(sizeof($charids)>0){

	if($sub=='paid'){
		foreach($charids as $charid){
			set_charge_payment($charid,'1');
			}
		}
	elseif($sub=='notpaid'){
		foreach($charids as $charid){
			set_charge_payment($charid,'2');
			}
		}
	}


include('scripts/redirect.php');
?>
