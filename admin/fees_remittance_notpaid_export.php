<?php
/**									fees_remittance_notpaid_export.php
 */

$action='fees_remittance_notpaid_list.php';
$choice='fees.php';

if(isset($_POST['ids'])){$ids=$_POST['ids'];}else{$ids=array();}

require_once('Spreadsheet/Excel/Writer.php');

if(sizeof($ids)==0){
	$result[]=get_string('youneedtoselectstudents');
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$file=$CFG->eportfolio_dataroot. '/cache/files/';
$file.='class_export.xls';
$workbook=new Spreadsheet_Excel_Writer($file);
$workbook->setVersion(8);
$format_hdr_bold=&$workbook->addFormat(
	array(
		'Size' => 11,
		'Color' => 'white',
		'Pattern' => 1,
		'Bold' => 1,
		'FgColor' => 'gray'
		));
$format_line_bold=&$workbook->addFormat(
	array(
		'Size' => 10,
		'Bold' => 1
		));
$format_line_normal=&$workbook->addFormat(
	array(
		'Size' => 10,
		'Bold' => 0
		));
$worksheet=&$workbook->addWorksheet('Export_Not_Paid_Fees');

if(!$file){
	$error[]='unabletoopenfileforwriting';
	}
else{
	$worksheet->setInputEncoding('UTF-8');
	$worksheet->write(0, 0, 'Classis Id.', $format_hdr_bold);
	$worksheet->write(0, 1, 'Enrolment No.', $format_hdr_bold);
	$worksheet->write(0, 2, 'Surname', $format_hdr_bold);
	$worksheet->write(0, 3, 'Forename', $format_hdr_bold);
	$worksheet->write(0, 4, 'Remittance', $format_hdr_bold);
	$worksheet->write(0, 5, 'Tarif', $format_hdr_bold);
	$worksheet->write(0, 6, 'Payment type', $format_hdr_bold);
	$worksheet->write(0, 7, 'Amount', $format_hdr_bold);

	$paymenttypes=getEnumArray('paymenttype');

	$rown=1;
	foreach($ids as $id){
		$idsparts=explode("-",$id);
		$sid=$idsparts[0];
		$chargeid=$idsparts[1];
		$Student=(array)fetchStudent_short($sid);
		$EnrolNumber=(array)fetchStudent_singlefield($sid,'EnrolNumber');
		$Charge=get_charge($chargeid);
		$tarifid=$Charge['tarif_id'];
		$remittanceid=$Charge['remittance_id'];
		$d_t=mysql_query("SELECT * FROM fees_tarif WHERE id='$tarifid';");
		$tarifname=mysql_result($d_t,0,'name');
		$d_r=mysql_query("SELECT * FROM fees_remittance WHERE id='$remittanceid';");
		$remittancename=mysql_result($d_r,0,'name');

		$worksheet->write($rown, 0, $sid, $format_line_bold);
		$worksheet->write($rown, 1, $EnrolNumber['EnrolNumber']['value'], $format_line_bold);
		$worksheet->write($rown, 2, $Student['Surname']['value'], $format_line_bold);
		$worksheet->write($rown, 3, $Student['Forename']['value'], $format_line_bold);
		$worksheet->write($rown, 4, $remittancename, $format_line_normal);
		$worksheet->write($rown, 5, $tarifname, $format_line_normal);
		$worksheet->write($rown, 6, get_string($paymenttypes[$Charge['paymenttype']],$book), $format_line_normal);
		$worksheet->write($rown, 7, $Charge['amount'], $format_line_normal);

		$rown++;
		}

	$workbook->close();
?>
	<input type="hidden" name="openexport" id="openexport" value="xls">
<?php
	}

include('scripts/redirect.php');
?>
