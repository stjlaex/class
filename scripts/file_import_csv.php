<?php
/**						file_import_csv.php
 *
 * generic file import for csv
 * returns the contents as $inrows, and the $nofields for a row
 * aborts to the originating form page on failure
 * if records are split across multiple lines then set $multiline>1
 */

$fname=$_FILES['importfile']['tmp_name'];
$fuser=$_FILES['importfile']['name'];
$ferror=$_FILES['importfile']['error'];
$ftype=$_FILES['importfile']['type'];
trigger_error($ftype,E_USER_WARNING);
	if($ferror>0){
		$error[]='Unable to open remote file.';
		$action=$choice;
		}
	elseif($ftype!='text/x-comma-separated-values' and 
		   $ftype!='application/vnd.ms-excel' and //needed for windows:
												  //what the fuck is
												  //this type?!!!
		   $ftype!='text/csv' and $ftype!='text/comma-separated-values'){
		$error[]=$ftype;
		$error[]='File is of the wrong type, it must be a csv file.';
		$action=$choice;
		}
	elseif(is_uploaded_file($fname)){
		$file=fopen("$fname", 'r');
		if(!$file){
			$error[]='Failed to open uploaded file at the last.';
			}
		else{
			$inrows=array();
			$record=array();
			$row=0;
			$recordline=0;
			$recordcount=0;
			if(!isset($multiline)){$multiline=1;}
			$nofields='';
			while(!feof($file)){
				$in=(array)fgetcsv($file,999,',');
				/*(filename, maxrowsize,delimeter,enclosure)*/

				/*if first item a # then ignore whole row*/
				if(sizeof($in)>1 and isset($in[0][0]) and $in[0][0]!='#'){
					$recordline++;
					//$record=$record . $in;
					$record=array_merge($record,$in);
					if($recordline==$multiline){
						$recordcount++;
						$noin=sizeof($record);
						if($nofields!='' and $nofields!=$noin and $recordcount>$multiline){
							$error[]='WARNING: record '. $recordcount. 
									' has a mismatched field count! ' 
									.$in[0].' ';}
						else{$nofields=$noin;}
						array_push($inrows, $record);
						$record=array();
						$recordline=0;
						}
					}
				}

	   		$result[]='Succesfully uploaded	'.sizeof($inrows).' records.';
			}
		}
?>