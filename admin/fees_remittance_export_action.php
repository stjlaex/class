<?php
/**								   fees_remittance_export_action.php
 *
 */

$action='fees_remittance_list.php';

/* CFG->feeslib needs to be set depending on your provider */
require_once($CFG->dirroot.'/lib/'.$CFG->feeslib);

$remid=$_POST['remid'];
if(isset($_POST['charids'])){$charids=(array)$_POST['charids'];}else{$charids=array();}


include('scripts/sub_action.php');


if(sizeof($charids)==0){
		$result[]=get_string('youneedtoselectsomething');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

if($_POST['payment0']=='yes'){

	$todate=date('Y-m-d');

  	$file='/tmp/class_export'.$ftype;

  	
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{

		$rown=1;
		$Students=array();
		foreach($charids as $charid){
			$okay=false;
			$charge=get_charge($charid);
			$sid=$charge['student_id'];
			/* Do certain things once only for a student... */
			if(!array_key_exists($sid,$Students)){
				$Student=(array)fetchStudent_short($sid);
				$guardians=(array)list_student_payees($sid);
				if(sizeof($guardians)>0 and $guardians[0]['paymenttype']=='1' and $guardians[0]['accountsno']>0){
					/* Only add the student record if their is valid payee account. */
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


		foreach($Students as $sid => $Student){

			}


		/*send the workbook with the spreadsheet and close it*/ 
		$workbook->close();
		$result[]='exportedtofile';
?>
		<script>openFileExport('<?php print $ftype;?>');</script>
<?php
		}
	}
else{
	$result[]=get_string('noactiontaken',$book);
	}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
