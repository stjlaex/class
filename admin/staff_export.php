<?php
/**								   export_staff.php
 *
 */

$action='staff_list.php';

if(isset($_POST['uids'])){$uids=(array)$_POST['uids'];}else{$uids=array();}


include('scripts/sub_action.php');

if(sizeof($uids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

require_once('Spreadsheet/Excel/Writer.php');

	$file=$CFG->eportfolio_dataroot. '/cache/files/';
  	$file.='class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$workbook->setVersion(8);
	$format_hdr_bold =& $workbook->addFormat(array('Size' => 11,
		                                  //'Align' => 'center',
		                                  'Color' => 'white',
		                                  'Pattern' => 1,
		                                  'Bold' => 1,
		                                  'FgColor' => 'gray'));
	$format_line_bold =& $workbook->addFormat(array('Size' => 10,
		                                  //'Align' => 'center',
		                                  'Bold' => 1
		                                  ));
	$format_line_normal =& $workbook->addFormat(array('Size' => 10,
		                                  //'Align' => 'center',
		                                  'Bold' => 0
		                                  ));
	$worksheet =& $workbook->addWorksheet('Export_Staff');
  	
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$worksheet->setInputEncoding('UTF-8');
		$worksheet->write(0, 0, 'Surname', $format_hdr_bold);
		$worksheet->write(0, 1, 'Forename', $format_hdr_bold);
		$worksheet->write(0, 2, 'Email', $format_hdr_bold);
		$worksheet->write(0, 3, 'PersonalEmail', $format_hdr_bold);
		$worksheet->write(0, 4, 'JobTitle', $format_hdr_bold);
		$worksheet->write(0, 5, 'HomePhone', $format_hdr_bold);
		$worksheet->write(0, 6, 'MobilePhone', $format_hdr_bold);
		$worksheet->write(0, 7, 'PersonalCode', $format_hdr_bold);
		$worksheet->write(0, 8, 'PostalAddress', $format_hdr_bold);

		$rown=1;
		foreach($uids as $uid){
			$User=(array)fetchUser($uid);

			$worksheet->write($rown, 0, $User['Surname']['value'], $format_line_bold);
			$worksheet->write($rown, 1, $User['Forename']['value'], $format_line_bold);
			$worksheet->write($rown, 2, $User['EmailAddress']['value'], $format_line_bold);
			$worksheet->write($rown, 3, $User['PersonalEmailAddress']['value'], $format_line_bold);
			$worksheet->write($rown, 4, $User['JobTitle']['value'], $format_line_bold);
			$worksheet->write($rown, 5, $User['HomePhone']['value'], $format_line_bold);
			$worksheet->write($rown, 6, $User['MobilePhone']['value'], $format_line_bold);
			$worksheet->write($rown, 7, $User['PersonalCode']['value'], $format_line_bold);
			$worksheet->write($rown, 8, $User['Address']['Street']['value'], $format_line_bold);
			$worksheet->write($rown, 9, $User['Address']['Neighbourhood']['value'], $format_line_bold);
			$worksheet->write($rown, 10, $User['Address']['Town']['value'], $format_line_bold);
			$worksheet->write($rown, 11, $User['Address']['Country']['value'], $format_line_bold);
			$worksheet->write($rown, 12, $User['Address']['Postcode']['value'], $format_line_bold);

			$rown++;
			}


		$workbook->close();
?>
		<script>openFileExport('xls');</script>
<?php
		}


	include('scripts/results.php');
	include('scripts/redirect.php');
?>
