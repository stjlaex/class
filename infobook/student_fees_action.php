<?php
/**								student_transport_action.php
 *
 */

$action='student_fees.php';
require_once('lib/fetch_fees.php');

if(isset($_POST['newconceptid'])){$newconceptid=$_POST['newconceptid'];}else{$newconceptid='';}
if(isset($_POST['feeids'])){$feeids=(array)$_POST['feeids'];}else{$feeids=array();}
if(isset($_POST['gid'])){$gid=$_POST['gid'];}
if(isset($_POST['paytype'])){$default_paymenttype=$_POST['paytype'];}

include('scripts/sub_action.php');


if($sub=='Submit'){

	/* Set the repsonsible guardian and their preferred paymnet method for fees. */
	if(isset($gid) and isset($default_paymenttype)){
		$success=update_student_payee($sid,$gid,$default_paymenttype);
		}

	$fees=(array)list_student_fees($sid);
	foreach($fees as $conid => $fee){
		foreach($fee as $index => $c){
			$feeid=$c['id'];
			if(isset($_POST['feetarif'.$feeid]) and $_POST['feetarif'.$feeid]!=''){
				$tarifid=$_POST['feetarif'.$feeid];
				$paymenttype=$_POST['feepaymenttype'.$feeid];
				mysql_query("UPDATE fees_applied SET tarif_id='$tarifid', paymenttype='$paymenttype' WHERE id='$feeid';");
				}
			}
		}


	$charges=array();
	$todate=date('Y-m-d');
	$chargestatuses=array('P','1','2');
	foreach($chargestatuses as $chargestatus){
		$charges=(array)list_student_charges($sid,$chargestatus);
		foreach($charges as $conid => $charge){
			foreach($charge as $c){
				$charid=$c['id'];
				/*
				if(isset($_POST['tarif'.$charid]) and $_POST['tarif'.$charid]!=''){
					$tarifid=$_POST['tarif'.$charid];
					$paymenttype=$_POST['paymenttype'.$charid];
					}
				*/
				if(isset($_POST['payment'.$charid]) and ($_POST['payment'.$charid]==1 or $_POST['payment'.$charid]==2)){
					$payment=$_POST['payment'.$charid];
					set_charge_payment($charid,$payment);
					}
				}
			}
		}
	}


/**
 *  Adding a new fee.
 */
if(isset($_POST['newconcept']) and $_POST['newconcept']=='add' and $newconceptid!=''){
	apply_student_fee($sid,$newconceptid);
	}

/**
 *  Adding a new fee.
 */
if($_POST['oldfees']=='delete' and sizeof($feeids)>0){
	foreach($feeids as $feeid){
		delete_fee($feeid);
		}
	}

include('scripts/redirect.php');
?>
