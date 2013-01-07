<?php
/**                                  fees_remittance_charge_export.php
 *
 */

$action='fees_remittance_view.php';
$choice='fees_remittance_list.php';

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



$Students=array();
$charges=array();
$invoices=array();
$Remittance=fetchRemittance($remid);
if(sizeof($charids)>0){
	foreach($charids as $charid){
		$charges[]=get_charge($charid);
		}
	}
else{
	$charges=(array)list_remittance_charges($remid,$conid,$payment);
	}

	require_once 'Spreadsheet/Excel/Writer.php';

	$file=$CFG->eportfolio_dataroot. '/cache/files/';
  	$file.='class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_head =& $workbook->addFormat();
	$format_head =& $workbook->addFormat(array('Size' => 10,
											   'Align' => 'center',
											   'Color' => 'white',
											   'Pattern' => 1,
											   'Bold' => 1,
											   'FgColor' => 'gray'));
	$format =& $workbook->addFormat(array('Size' => 10,
										  'Align' => 'left',
										  'Bold' => 1
										  ));
	$worksheet =& $workbook->addWorksheet('Invoice Export');

	if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{

		/* optional schoollogo but oly bitmap possible */
		if(file_exists('../images/schoollogo.bmp')){
			$worksheet->insertBitmap(0,0,'../images/schoollogo.bmp',0,0,0.45,0.7);
			}
		/*first do the column headers*/
		$worksheet->setColumn(0,0,14);
		$worksheet->setColumn(1,2,25);
		$worksheet->setColumn(2,20,20);


		$worksheet->write(1, 0, '', $format);
		$worksheet->write(1, 1, '', $format);
		$worksheet->write(1, 2, get_string('remittance',$book), $format_head);
		$worksheet->write(1, 3, get_string('date',$book), $format_head);
		$worksheet->write(1, 4, '', $format_head);
		$worksheet->write(1, 6, get_string('total',$book), $format_head);
		$worksheet->write(2, 0, '', $format);
		$worksheet->write(2, 1, '', $format);
		$worksheet->write(2, 2, $Remittance['Name']['value'], $format);
		$worksheet->write(2, 3, display_date($Remittance['IssueDate']['value']), $format);
		$worksheet->write(2, 4, '', $format);

		$worksheet->write(4, 0, '', $format_head);
		$worksheet->write(4, 1, get_string('enrolmentnumber','infobook'), $format_head);
		$worksheet->write(4, 2, get_string('student',$book), $format_head);
		$worksheet->write(4, 3, get_string('account',$book), $format_head);
		$worksheet->write(4, 4, get_string('invoicenumber',$book), $format_head);
		$worksheet->write(4, 5, get_string('payment',$book), $format_head);
		$worksheet->write(4, 6, get_string('amount',$book), $format_head);


		$total=0;
		$rowno=4;
		foreach($charges as $charge){
			if($charge['paymenttype']==$paymenttype or $paymenttype==''){

				$rowno++;
				$sid=$charge['student_id'];
				if(!array_key_exists($sid,$Students)){
					/* Do certain things once only for a student... */
					$Student=(array)fetchStudent_short($sid);
					$guardians=(array)list_student_payees($sid);
					if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
						$Student['payee']=$guardians[0];
						$Student['paymenttype']=$guardians[0]['paymenttype'];
						$Student['accountsno']=$guardians[0]['accountsno'];
						$Student['accountsno']=$guardians[0]['accountsno'];
						$Student['account']=fetchAccount($guardians[0]['id']);
						}
					else{
						$Student['payee']='';
						$Student['account']='';
						$Student['paymenttype']='';
						$Student['accountsno']=0;
						}
					$Students[$sid]=$Student;
					$first=1;
					}
				else{
					$Student=$Students[$sid];
					$first++;
					}

				if(!array_key_exists($charge['invoice_id'],$invoices)){
					$invoice=get_invoice($charge['invoice_id']);
					//$invoice=array('reference'=>'');
					$invoices[$charge['invoice_id']]=$invoice;
					}
				else{
					$invoice=$invoices[$charge['invoice_id']];
					}

				$worksheet->write($rowno, 0, $rowno-4, $format);
				$worksheet->write($rowno, 1, $Student['EnrolNumber']['value'], $format);
				$worksheet->write($rowno, 2, iconv('UTF-8','ISO-8859-1',$Student['DisplayFullSurname']['value']).' ('.iconv('UTF-8','ISO-8859-1',$Student['RegistrationGroup']['value']).')', $format);
				$worksheet->write($rowno, 3, iconv('UTF-8','ISO-8859-1',$Student['account']['AccountName']['value']).' '.$Student['account']['BankCode']['value'].' '.$Student['account']['Branch']['value'].' '.$Student['account']['Control']['value'].' '.$Student['account']['Number']['value'], $format);
				$worksheet->write($rowno, 4, $invoice['reference'], $format);
				$worksheet->write($rowno, 5, iconv('UTF-8','ISO-8859-1',get_string(displayEnum($charge['paymenttype'],'paymenttype'),$book)), $format);
				$worksheet->write($rowno, 6, iconv('UTF-8','ISO-8859-1',display_money($charge['amount'])), $format);
				$total+=$charge['amount'];
				}
			}

		/* The final Total */
		$worksheet->write(2, 6, display_money($total), $format);

		$worksheet->write($rowno+2, 6, get_string('total',$book), $format_head);
		$worksheet->write($rowno+3, 6, display_money($total), $format);

		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
?>
		<script>openFileExport('xls');</script>
<?php
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>