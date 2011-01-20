<?php
/**								   community_group_export.php
 *
 */

$action='community_group.php';

if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}else{$comids=array();}

require_once 'Spreadsheet/Excel/Writer.php';

include('scripts/sub_action.php');

if(sizeof($comids)==0){
		$result[]=get_string('youneedtoselectsomething');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

$todate=date('Y-m-d');

  	$file='/tmp/class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_hdr_bold =& $workbook->addFormat(array('Size' => 11,
		                                  'Align' => 'center',
		                                  'Color' => 'white',
		                                  'Pattern' => 1,
		                                  'Bold' => 1,
		                                  'FgColor' => 'gray'));
	$format_line_bold =& $workbook->addFormat(array('Size' => 10,
		                                  'Align' => 'left',
		                                  'Bold' => 1
		                                  ));
	$format_line_normal =& $workbook->addFormat(array('Size' => 10,
		                                  'Align' => 'center',
		                                  'Bold' => 0
		                                  ));
	$worksheet =& $workbook->addWorksheet('Export_Students');
  	
  	
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$worksheet->write(0, 0, 'Enrolment No.', $format_hdr_bold);
		$worksheet->write(0, 1, 'Surname', $format_hdr_bold);
		$worksheet->write(0, 2, 'Forename', $format_hdr_bold);
		$worksheet->write(0, 3, 'Type', $format_hdr_bold);
		$worksheet->write(0, 4, 'Description', $format_hdr_bold);
		$worksheet->write(0, 5, 'Price', $format_hdr_bold);
		$worksheet->write(0, 6, 'Hours_Qty', $format_hdr_bold);
		$worksheet->write(0, 7, 'Pay_Date', $format_hdr_bold);
		$worksheet->write(0, 8, 'Term', $format_hdr_bold);

		$rown=1;
		foreach($comids as $comid){
			$com=(array)get_community($comid);
			/* all students who joined the community after startdate and before enddate*/
			/* TODO: add these form values to community_group */ 
			if(isset($_POST['enroldate1'])){$startdate=$_POST['enroldate1'];}else{$startdate='2011-01-01';}
			if(isset($_POST['enroldate2'])){$enddate=$_POST['enroldate2'];}else{$enddate='';}
			//$students=(array)listin_community_new($com,$startdate,$enddate);
			$students=(array)listin_community($com);

			/*cycle through the student rows*/
			foreach($students as $student){
				$sid=$student['id'];
				$Student=fetchStudent_short($sid);
				$field=fetchStudent_singlefield($sid,'EnrolNumber');
				$Student=array_merge($Student,$field);
				if($student['special']=='' or $student['special']=='10'){$hours=1;}else{$hours=$student['special']/10;};
				$worksheet->write($rown, 0, $Student['EnrolNumber']['value'], $format_line_normal);
				$worksheet->write($rown, 1, iconv('UTF-8','ISO-8859-1',$Student['Surname']['value']), $format_line_bold);
				$worksheet->write($rown, 2, iconv('UTF-8','ISO-8859-1',$Student['Forename']['value']), $format_line_normal);
				$worksheet->write($rown, 3, 'c', $format_line_normal);
				$worksheet->write($rown, 4, $com['name'], $format_line_normal);
				$worksheet->write($rown, 5, $com['charge'], $format_line_normal);
				$worksheet->write($rown, 6, $hours, $format_line_normal);
				$worksheet->write($rown, 7, $todate, $format_line_normal);
				/* TODO: calculate the term correctly!!!! */
				$worksheet->write($rown, 8, '2', $format_line_normal);
				$rown++;
				}
			}

		/*send the workbook with the spreadsheet and close it*/ 
		$workbook->close();
		$result[]='exportedtofile';
?>
		<script>openFileExport('xls');</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
