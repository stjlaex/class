<?php
/**								student_transport_action.php
 *
 */

$action='student_fees.php';
require_once('lib/fetch_fees.php');

if(isset($_POST['newconceptid'])){$newconceptid=$_POST['newconceptid'];}else{$newconceptid='';}

include('scripts/sub_action.php');


if($sub=='Submit'){

	$charges=(array)list_student_charges($sid);
	$todate=date('Y-m-d');
	foreach($charges as $conid => $charge){
		foreach($charge as $index => $c){
			$charid=$c['id'];
			if(isset($_POST['tarif'.$charid]) and $_POST['tarif'.$charid]!=''){
				$tarifid=$_POST['tarif'.$charid];
				$paymenttype=$_POST['paymenttype'.$charid];
				if(isset($_POST['payment'.$charid]) and $_POST['payment'.$charid]==1){
					$payment=$_POST['payment'.$charid];
					$paymentdate=$todate;
					}
				else{
					$payment=0;
					$paymentdate='';
					}

				mysql_query("UPDATE fees_charge SET tarif_id='$tarifid', paymenttype='$paymenttype', paymentdate='$paymentdate', payment='$payment' 
								WHERE id='$charid';");
				}
			}
		}

	}


if($newconceptid!=''){

	$d_t=mysql_query("SELECT id FROM fees_tarif WHERE concept_id='$newconceptid' ORDER BY name LIMIT 1;");
	$tarifid=mysql_result($d_t,0);
	mysql_query("INSERT INTO fees_charge (student_id,tarif_id) VALUES ('$sid','$tarifid');");
	trigger_error($tarifid,E_USER_WARNING);
	}

include('scripts/redirect.php');
?>
