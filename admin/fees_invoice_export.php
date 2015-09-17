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

function checksumDigits($string,$country="ES"){
	$letters=array("A"=>"10","B"=>"11","C"=>"12","D"=>"13","E"=>"14","F"=>"15",
				"G"=>"16","H"=>"17","I"=>"18","J"=>"19","K"=>"20","L"=>"21",
				"M"=>"22","N"=>"23","O"=>"24","P"=>"25","Q"=>"26","R"=>"27",
				"S"=>"28","T"=>"29","U"=>"30","V"=>"31","W"=>"32","X"=>"33",
				"Y"=>"34","Z"=>"35"
				);
	$code=$string.$country."00";
	$ncode=strtr($code,$letters);
	$rest=bcmod($ncode,97);
	$digits=98-$rest;
	if($digits<10){$digits=str_pad($digits,2,"0",STR_PAD_LEFT);}
	return $digits;
	}

$Students=array();
$Remittance=fetchRemittance($remid);
$invoices=(array)list_remittance_invoices($remid,$paymenttype);



	require_once 'Spreadsheet/Excel/Writer.php';

	$file=$CFG->eportfolio_dataroot. '/cache/files/';
  	$file.='class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$workbook->setVersion(8);
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
		$worksheet->write(1, 2, '', $format);
		$worksheet->write(1, 3, '', $format);
		$worksheet->write(1, 4, '', $format_head);
		$worksheet->write(1, 5, get_string('remittance',$book), $format_head);
		$worksheet->write(1, 6, get_string('date',$book), $format_head);
		$worksheet->write(1, 7, get_string('total',$book), $format_head);
		$worksheet->write(2, 0, '', $format);
		$worksheet->write(2, 1, '', $format);
		$worksheet->write(2, 2, '', $format);
		$worksheet->write(2, 3, '', $format);
		$worksheet->write(2, 4, '', $format);
		$worksheet->write(2, 5, $Remittance['Name']['value'], $format);
		$worksheet->write(2, 6, display_date($Remittance['IssueDate']['value']), $format);

		$worksheet->write(4, 0, '', $format_head);
		$worksheet->write(4, 1, get_string('invoice',$book), $format_head);
		$worksheet->write(4, 2, get_string('student',$book), $format_head);
		$worksheet->write(4, 3, get_string('formgroup',$book), $format_head);
		$worksheet->write(4, 4, get_string('payment',$book), $format_head);
		$worksheet->write(4, 5, get_string('amount',$book), $format_head);
		$worksheet->write(4, 6, get_string('payee',$book), $format_head);
		$worksheet->write(4, 7, get_string('account',$book), $format_head);


		$total=0;
		$rowno=4;
		foreach($invoices as $invoice){
			$rowno++;
			$Invoice=(array)fetchFeesInvoice($invoice);
			$sid=$Invoice['student_id_db'];
			$accountid=$Invoice['account_id_db'];
			$Account=(array)fetchAccount($accountid,'id');
			if($Account['Iban']['value']!='' and checkIBAN($Account['Iban']['value'])){$IBAN=$Account['Iban']['value'];}
			else{
				$accountno=$Account['BankCode']['value'].$Account['Branch']['value'].$Account['Control']['value'].$Account['Number']['value'];
				$ibandigits=checksumDigits($accountno);
				$IBAN="ES".$ibandigits.$accountno;
				}

			if(!array_key_exists($sid,$Students)){
				/* Do this once only for a student... */
				$Student=(array)fetchStudent_short($sid);
				$Students[$sid]=$Student;
				}
			else{
				$Student=$Students[$sid];
				}
			
			$worksheet->write($rowno, 0, $rowno-4, $format);
			$worksheet->write($rowno, 1, $Invoice['Reference']['value'], $format);
			$worksheet->write($rowno, 2, iconv('UTF-8','ISO-8859-1',$Student['DisplayFullSurname']['value']), $format);
			$worksheet->write($rowno, 3, iconv('UTF-8','ISO-8859-1',$Student['RegistrationGroup']['value']), $format);
			$worksheet->write($rowno, 4, iconv('UTF-8','ISO-8859-1',get_string(displayEnum($Invoice['PaymentType']['value'],'paymenttype'),$book)), $format);
			$worksheet->write($rowno, 5, iconv('UTF-8','ISO-8859-1',display_money($Invoice['TotalAmount']['value'])), $format);
			$worksheet->write($rowno, 6, iconv('UTF-8','ISO-8859-1',$Invoice['AccountName']['value']), $format);
			$worksheet->write($rowno, 7, iconv('UTF-8','ISO-8859-1',$IBAN), $format);
			$total+=$Invoice['TotalAmount']['value'];
			}

		/* The final Total */
		$worksheet->write(2, 7, display_money($total), $format);

		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
?>
		<input type="hidden" name="openexport" id="openexport" value="xls">
<?php
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
