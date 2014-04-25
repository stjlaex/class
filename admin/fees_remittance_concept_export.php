<?php
/**                                  fees_remittance_concept_export.php
 *
 */

$action='fees_remittance_view.php';
$choice='fees_remittance_list.php';

$action_post_vars=array('feeyear','remid','paymenttype','conid','payment');


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



		$total=0;
		$rowno=4;
		foreach($Remittance['Concepts'] as $Concept){
		  $Tarifs=array();

		  foreach($Concept['Tarifs'] as $Tarif){
		    $Tarifs[$Tarif['id_db']]=$Tarif;
		  }
		  $charges=(array)list_remittance_charges($remid,$Concept['id_db']);

		  $subtotal=0;
		  $rowno++;
		  $rowno++;
		  $worksheet->write($rowno, 0, '', $format_head);
		  $worksheet->write($rowno, 1, iconv('UTF-8','ISO-8859-1',$Concept['Name']['value']), $format_head);
		  $rowno++;
		  $worksheet->write($rowno, 0, '', $format_head);
		  $worksheet->write($rowno, 1, get_string('enrolmentnumber','infobook'), $format_head);
		  $worksheet->write($rowno, 2, get_string('student',$book), $format_head);
		  $worksheet->write($rowno, 3, get_string('tarif',$book), $format_head);
		  $worksheet->write($rowno, 4, get_string('amount',$book), $format_head);
  
		  $chargeno=0;
		  foreach($charges as $charge){

		    $rowno++;
		    $chargeno++;
		    $sid=$charge['student_id'];
		    if(!array_key_exists($sid,$Students)){
		      $Student=(array)fetchStudent_short($sid);
		      $Students[$sid]=$Student;
		      $first=1;
		    }
		    else{
		      $Student=$Students[$sid];
		      $first++;
		    }

		    $worksheet->write($rowno, 0, $chargeno, $format);
		    $worksheet->write($rowno, 1, $Student['EnrolNumber']['value'], $format);
		    $worksheet->write($rowno, 2, iconv('UTF-8','ISO-8859-1',$Student['DisplayFullSurname']['value']), $format);
		    $worksheet->write($rowno, 3, iconv('UTF-8','ISO-8859-1',$Tarifs[$charge['tarif_id']]['Name']['value']), $format);
		    $worksheet->write($rowno, 4, display_money($charge['amount']), $format);
		    $subtotal+=$charge['amount'];
		    trigger_error($Tarifs[$charge['tarif_id']]['Name']['value'],E_USER_WARNING);
		  }
		$rowno++;

		$worksheet->write($rowno, 0, '', $format_head);
		$worksheet->write($rowno, 1, get_string('total',$book), $format_head);
		$worksheet->write($rowno, 2, '', $format_head);
		$worksheet->write($rowno, 3, '', $format_head);
		$worksheet->write($rowno, 4, display_money($subtotal), $format_head);
		$total+=$subtotal;
		}
		/* The final Total */
		$worksheet->write(2, 6, display_money($total), $format);
		$worksheet->write($rowno+2, 6, get_string('total',$book), $format_head);
		$worksheet->write($rowno+3, 6, display_money($total), $format);


		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
?>
		<input type="hidden" name="openexport" id="openexport" value="xls">
<?php
						  }

include('scripts/results.php');
include('scripts/redirect.php');
?>
