<?php
/**								   fees_remittance_invoice_action.php
 *
 */

$action='fees_invoice_list.php';
$cancel='fees_remittance_list.php';

$action_post_vars=array('remid','paymenttype');

/* CFG->feeslib needs to be set depending on your provider */
require_once($CFG->dirroot.'/lib/'.$CFG->feeslib);

$remid=$_POST['remid'];
$paymenttype=$_POST['paymenttype'];

include('scripts/sub_action.php');


if($_POST['payment0']=='yes'){

	$todate=date('Y-m-d');

	$rown=1;
	$Students=array();
	/* Only include charges which are not yet invoiced (0=pending). */
	$charges=(array)list_remittance_charges($remid,'','0');
	foreach($charges as $charge){
		$okay=false;
		$charid=$charge['id'];
		$charge=get_charge($charid);
		/* Filter out any charges which are intended for bank payment (paymenttype=1) */
		if($charge['paymenttype']!='1'){
			$sid=$charge['student_id'];
			/* Do certain things once only for a student... */
			if(!array_key_exists($sid,$Students)){
				$Student=(array)fetchStudent_short($sid);
				$guardians=(array)list_student_payees($sid);
				/* TODO: Check the guardians preferred payment
				 *  method... but if there is not one is who
				 *  mathces??? The following would filter out payees
				 *  who only do bank: "and $guardians[0]['paymenttype']!='1'" but then you
				 *  get no payees
				 */
				if(sizeof($guardians)>0){
					/* Only add the student record if their is a valid payee account. */
					$Student['payee']=$guardians[0];
					$Student['charges']=array();
					$Students[$sid]=$Student;
					$okay=true;
					}
				}
			else{
				$okay=true;
				}
			if($okay){
				$Students[$sid]['charges'][]=$charge;
				}
			}
		}



	foreach($Students as $sid => $Student){
		$Account=(array)fetchAccount($Student['payee']['id']);
		//trigger_error('ACCOUNT: '.$sid.' :'.$Account['id_db'],E_USER_WARNING);
		if($Account['id_db']!=-1){
			$invoice=(array)create_invoice($Account['id_db'],$remid);
			foreach($Student['charges'] as $charge){
				set_charge_payment($charge['id'],'2',$invoice['id']);
				}
			}
		}

	if(isset($_SESSION['remittancestotals'])){
		$Remittance=fetchRemittance($remid);
		unset($_SESSION['remittancestotals'][$Remittance['Year']['value']]);
		}
	}
else{
	$result[]=get_string('noactiontaken',$book);
	$action=$cancel;
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
