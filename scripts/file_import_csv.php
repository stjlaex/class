<?php
/**						file_import_csv.php
 *
 *generic file import for csv
 *returns the contents as $inrows, and the $nofields for a row
 *aborts to the originating form page on failure
 */

$fname=$_FILES{'importfile'}{'tmp_name'};
$fuser=$_FILES{'importfile'}{'name'};
$ferror=$_FILES{'importfile'}{'error'};
$ftype=$_FILES{'importfile'}{'type'};
	
	if($ferror>0){
		$error[]='Unable to open remote file.';
		$action=$choice;
		}
	elseif($ftype!='text/x-comma-separated-values' and $ftype!='text/comma-separated-values'){
		$error[]=$ftype;
		$error[]='File is of the wrong type, it must be a csv file.';
		$action=$choice;
		}
	elseif(is_uploaded_file($fname)){
		$file=fopen("$fname", "r");
		if(!$file){
			$error[]='Failed to open uploaded file at the last.';
			}
		else{
			$inrows=array();
			$row=0;
			$nofields='';
			while(!feof($file)){
				$in=(array)fgetcsv($file,999,',');
				/*(filename, maxrowsize,delimeter,enclosure)*/

				/*if first item is null ignore whole row*/
				if($in[0][0]!='' & $in[0][0]!='#'){
					$noin=sizeof($in);
					if($nofields!='' and $nofields!=$noin and $row>0){
						$error[]="WARNING: row $row has a mismatched field
					count! ".$in[0].' ';}
					else{$nofields=$noin;}
					array_push($inrows, $in);		
					}
				$row++;	
				}
	   		$result[]='Succesfully uploaded	'.sizeof($inrows).' records.';
			}
		}
?>