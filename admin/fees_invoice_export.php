<?php
/**                                  fees_invoice_export.php
 *
 */

$action='fees_invoice_list.php';
$choice='fees_remittance_list.php';

$action_post_vars=array('feeyear','remid','paymenttype');

if((isset($_POST['remid']) and $_POST['remid']!='')){$remid=$_POST['remid'];}else{$remid='';}
if((isset($_GET['remid']) and $_GET['remid']!='')){$remid=$_GET['remid'];}

if((isset($_POST['paymenttype']) and $_POST['paymenttype']!='')){$paymenttype=$_POST['paymenttype'];}else{$paymenttype='';}
if((isset($_GET['paymenttype']) and $_GET['paymenttype']!='')){$paymenttype=$_GET['paymenttype'];}



$Students=array();
$Remittance=fetchRemittance($remid);
$invoices=(array)list_remittance_invoices($remid,$paymenttype);



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

		/*first do the column headers*/
		$worksheet->setColumn(0,0,14);
		$worksheet->setColumn(1,2,25);
		$worksheet->setColumn(2,20,20);


		$worksheet->write(0, 0, '', $format);
		$worksheet->write(0, 1, '', $format);
		$worksheet->write(0, 2, '', $format_head);
		$worksheet->write(0, 3, get_string('remittance',$book), $format_head);
		$worksheet->write(0, 4, get_string('date',$book), $format_head);
		$worksheet->write(0, 5, get_string('total',$book), $format_head);
		$worksheet->write(1, 0, '', $format);
		$worksheet->write(1, 1, '', $format);
		$worksheet->write(1, 2, '', $format);
		$worksheet->write(1, 3, $Remittance['Name']['value'], $format);
		$worksheet->write(1, 4, display_date($Remittance['IssueDate']['value']), $format);

		$worksheet->write(3, 0, '', $format_head);
		$worksheet->write(3, 1, get_string('invoice',$book), $format_head);
		$worksheet->write(3, 2, get_string('student',$book), $format_head);
		$worksheet->write(3, 3, get_string('formgroup',$book), $format_head);
		$worksheet->write(3, 4, get_string('payment',$book), $format_head);
		$worksheet->write(3, 5, get_string('amount',$book), $format_head);


		$total=0;
		$rowno=3;
		foreach($invoices as $invoice){
			$rowno++;
			$Invoice=(array)fetchFeesInvoice($invoice);
			$sid=$Invoice['student_id_db'];
			
			if(!array_key_exists($sid,$Students)){
				/* Do this once only for a student... */
				$Student=(array)fetchStudent_short($sid);
				$Students[$sid]=$Student;
				}
			else{
				$Student=$Students[$sid];
				}
			
			$worksheet->write($rowno, 0, $rowno-3, $format);
			$worksheet->write($rowno, 1, $Invoice['Reference']['value'], $format);
			$worksheet->write($rowno, 2, iconv('UTF-8','ISO-8859-1',$Student['DisplayFullSurname']['value']), $format);
			$worksheet->write($rowno, 3, iconv('UTF-8','ISO-8859-1',$Student['RegistrationGroup']['value']), $format);
			$worksheet->write($rowno, 4, iconv('UTF-8','ISO-8859-1',get_string(displayEnum($Invoice['PaymentType']['value'],'paymenttype'),$book)), $format);
			$worksheet->write($rowno, 5, iconv('UTF-8','ISO-8859-1',display_money($Invoice['TotalAmount']['value'])), $format);
			$total+=$Invoice['TotalAmount']['value'];
			}

		/* The final Total */
		$worksheet->write(1, 5, display_money($total), $format);

		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
?>
		<script>openFileExport('xls');</script>
<?php
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>