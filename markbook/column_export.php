<?php 
/** 									column_export.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];

if(!isset($_POST['checkmid'])){
	$error[]='Choose one or more columns to export.';
	}
else{
	require_once 'Spreadsheet/Excel/Writer.php';
  	//$file=fopen('/tmp/class_export.xls', 'w');
	$file='/tmp/class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_bold =& $workbook->addFormat();
	$format_bold =& $workbook->addFormat(array('Size' => 11,
											   'Align' => 'center',
											   'Color' => 'white',
											   'Pattern' => 1,
											   'Bold' => 1,
											   'FgColor' => 'gray'));
	$format =& $workbook->addFormat(array('Size' => 10,
										  'Align' => 'center',
										  'Bold' => 1
										  ));
	$worksheet =& $workbook->addWorksheet('ClaSS Export');

	if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{
		$checkmids=(array)$_POST['checkmid'];

		/*first do the column headers*/
		$csv=array();
		$csv[]='Enrolment No.';
		$csv[]='Surname';
		$csv[]='Forename';
		for($c=0;$c<sizeof($checkmids);$c++){
			$col_mid=$checkmids[$c];
			for($col=0;$col<sizeof($umns);$col++){
				if($col_mid==$umns[$col]['id']){
					$csv[]=$umns[$col]['topic'] .' '. $umns[$col]['entrydate'].
						' '.$umns[$col]['component'].' '. $umns[$col]['marktype'];
					$col=sizeof($umns);
					}
				}
			}

		/* The column headers */
		for($col=0; $col<sizeof($csv); $col++) {
			$worksheet->write(0, $col, $csv[$col], $format_bold);
			}

		/*cycle through the student rows*/
		$i=1;
		for($c2=0;$c2<sizeof($viewtable);$c2++){
			$csv=array();
			$csv[]=$viewtable[$c2]['sid'];
			$csv[]=$viewtable[$c2]['surname'];
			$csv[]=$viewtable[$c2]['forename'];
			for($c=0;$c<sizeof($checkmids);$c++){
    			$col_mid=$checkmids[$c];
				$csv[]=$viewtable[$c2]["$col_mid"];
				}

			for($col=0;$col<sizeof($csv);$col++){				
				if($col<=2){
					$worksheet->write($i, $col, $csv[$col], $format);
					} 
				else{					
					$worksheet->write($i, $col, $csv[$col]);
					}
				}
			$i++;
			}


		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
		$result[]='Exported table in current view to file.';
?>
		<script>openFileExport('xls');</script>
<?php
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
