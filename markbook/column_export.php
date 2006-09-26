<?php 
/** 									column_export.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];
$file='class_export.csv';

if(!isset($_POST['checkmid'])){
	$error[]='Choose one or more columns to export.';
	}
else{
  	$file=fopen('/tmp/class_export.csv', 'w');
	if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{
		$checkmids=$_POST['checkmid'];

		/*first do the column headers*/
		$csv=array();
		$csv[]=' ';
		for($c=0;$c<sizeof($checkmids);$c++){
			$col_mid=$checkmids[$c];
			for($col=0;$col<sizeof($umns);$col++){
				if($col_mid==$umns[$col]['id']){
					$csv[]=$umns[$col]['topic'] .' '. $umns[$col]['entrydate']
						' '.$umns[$col]['component'].' '. $umns[$col]['marktype'];
					$col=sizeof($umns);
					}
				}
			}
		fputcsv($file,$csv);

		/*cycle through the student rows*/
		for($c2=0;$c2<sizeof($viewtable);$c2++){
			$csv=array();
			$csv[]=$viewtable[$c2]['sid'];
			$csv[]=$viewtable[$c2]['surname'];
			$csv[]=$viewtable[$c2]['forename'];
			for($c=0;$c<sizeof($checkmids);$c++){
    			$col_mid=$checkmids[$c];
				$csv[]=$viewtable[$c2]["$col_mid"];
				}
		   	fputcsv($file,$csv);
			}
	   	fclose($file);
		$result[]='Exported table in current view to file.';
?>
		<script>openFileExport();</script>
<?php
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>

