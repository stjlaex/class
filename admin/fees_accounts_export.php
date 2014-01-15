<?php
/**                                  fees_accounts_export.php
 *
 */

$action='fees.php';
$choice='fees.php';

$d_accounts=mysql_query("SELECT id FROM fees_account;");
while($accounts=mysql_fetch_array($d_accounts)){
	$Accounts[]=fetchAccount($accounts['id']);
	}

function acreedorIdDigits($string){
			$letras=array(
				"A"=>"10",
				"B"=>"11",
				"C"=>"12",
				"D"=>"13",
				"E"=>"14",
				"F"=>"15",
				"G"=>"16",
				"H"=>"17",
				"I"=>"18",
				"J"=>"19",
				"K"=>"20",
				"L"=>"21",
				"M"=>"22",
				"N"=>"23",
				"O"=>"24",
				"P"=>"25",
				"Q"=>"26",
				"R"=>"27",
				"S"=>"28",
				"T"=>"29",
				"U"=>"30",
				"V"=>"31",
				"W"=>"32",
				"X"=>"33",
				"Y"=>"34",
				"Z"=>"35"
			);
			$code=$string."ES00";
			$ncode=strtr($code,$letras);
			$rest=bcmod($ncode,97);
			$digits=98-$rest;
			if($digits<10){$digits=str_pad($digits,2,"0",STR_PAD_LEFT);}
			return $digits;
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
	$worksheet =& $workbook->addWorksheet('Accounts Export');

	if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{

		/* optional schoollogo but oly bitmap possible */
		if(file_exists('../images/schoollogo.bmp')){
			$worksheet->insertBitmap(0,0,'../images/schoollogo.bmp',0,0,0.45,0.7);
			}
		/*first do the column headers*/
		$worksheet->setColumn(0,0,25);
		$worksheet->setColumn(1,2,25);
		$worksheet->setColumn(2,20,30);


		$worksheet->write(0, 0, get_string('contact',$book), $format_head);
		$worksheet->write(0, 1, get_string('account',$book), $format_head);
		$worksheet->write(0, 2, 'IBAN', $format_head);
		$worksheet->write(0, 3, get_string('relationship','infobook'), $format_head);
		$worksheet->write(0, 4, get_string('email',$book), $format_head);
		$worksheet->write(0, 5, get_string('contactphones','infobook'), $format_head);

		$rowno=1;
		foreach($Accounts as $Account){
			$gid=$Account['guardian_id_db'];
			$Relationships=array();
			$Contact=fetchContact(array("guardian_id"=>$gid));
			$d_relationship=mysql_query("SELECT relationship,student_id FROM gidsid WHERE guardian_id='$gid';");
			while($rel=mysql_fetch_array($d_relationship,MYSQL_ASSOC)){
				$Student=fetchStudent($rel['student_id']);
				$Relationships[]=$Student['DisplayFullName']['value'].": ".get_string(displayEnum($rel['relationship'], 'relationship'),'infobook');
				}
	
			if($Account['Number']['value']!=''){
				$digits=acreedorIdDigits($Account['BankCode']['value'].$Account['Branch']['value'].$Account['Control']['value'].$Account['Number']['value']);
				$IBAN="ES".$digits.$Account['BankCode']['value'].$Account['Branch']['value'].$Account['Control']['value'].$Account['Number']['value'];
				$phones='';
				foreach($Contact['Phones'] as $Phone){
					if($Phone['Private']['value']!='Y'){
						$phones.=$Phone['PhoneType']['value'].":".$Phone['PhoneNo']['value']." ";
						}
					}
				$rels='';
				foreach($Relationships as $Relationship){
					$rels.=iconv('UTF-8','ISO-8859-1',$Relationship)."; ";
					}


				$worksheet->write($rowno, 0, iconv('UTF-8','ISO-8859-1',$Contact['DisplayFullName']['value']), $format);
				$worksheet->write($rowno, 1, $Account['BankCode']['value']."-".$Account['Branch']['value']."-".$Account['Control']['value']."-".$Account['Number']['value'], $format);
				$worksheet->write($rowno, 2, $IBAN, $format);
				$worksheet->write($rowno, 3, $rels, $format);
				$worksheet->write($rowno, 4, $Contact['EmailAddress']['value'], $format);
				$worksheet->write($rowno, 5, $phones, $format);

				$rowno++;
				}
			}



		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
?>
		<script>openFileExport('xls');</script>
<?php
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
