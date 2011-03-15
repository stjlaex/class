<?php
/**								   export_students.php
 *
 */

$action='student_list.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}

$displayfields=array();
for($dindex=0; $dindex < ($_POST['colno']); $dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
}
require_once 'Spreadsheet/Excel/Writer.php';

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}



  	$file='/tmp/class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_hdr_bold =& $workbook->addFormat(array('Size' => 11,
		                                  'Align' => 'center',
		                                  'Color' => 'white',
		                                  'Pattern' => 1,
		                                  'Bold' => 1,
		                                  'FgColor' => 'gray'));
	$format_line_bold =& $workbook->addFormat(array('Size' => 10,
		                                  'Align' => 'center',
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
		for($colno=0;$colno<$_POST['colno'];$colno++){
			$dspfld='displayfield'.$colno;
			$worksheet->write(0, $colno+3, $_POST[$dspfld], $format_hdr_bold);
			}

		/*cycle through the student rows*/
		$rown=1;
		while(list($index,$sid)=each($sids)){
			$Student=(array)fetchStudent_short($sid);
			$EnrolNumber=(array)fetchStudent_singlefield($sid,'EnrolNumber');

			//$worksheet->write($rown, 0, $sid, $format_line_bold);
			$worksheet->write($rown, 0, $EnrolNumber['EnrolNumber']['value'], $format_line_bold);
			$worksheet->write($rown, 1, iconv('UTF-8','ISO-8859-1',$Student['Surname']['value']), $format_line_bold);
			$worksheet->write($rown, 2, iconv('UTF-8','ISO-8859-1',$Student['Forename']['value']), $format_line_bold);

			$col=3;
			reset($displayfields);
			while(list($index,$displayfield)=each($displayfields)){
				if(!array_key_exists($displayfield,$Student)){
					$field=fetchStudent_singlefield($sid,$displayfield);
					$Student=array_merge($Student,$field);
					}
				if(isset($Student[$displayfield]['type_db'])  
				   and $Student[$displayfield]['type_db']=='enum'){
					$displayout=displayEnum($Student[$displayfield]['value'],$Student[$displayfield]['field_db']);
					$displayout=get_string($displayout,$book);
					}
				elseif(isset($Student[$displayfield]['type_db'])  
					   and $Student[$displayfield]['type_db']=='date'){
					$displayout=display_date($Student[$displayfield]['value']);
					}
				else{
					$displayout=$Student[$displayfield]['value'];
					}
				$worksheet->write($rown, $col, $displayout, $format_line_normal);
				$col++;
				}
			$rown++;
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
