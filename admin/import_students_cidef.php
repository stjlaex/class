<?php 
/**			   			   				import_students_cidef.php
 *
 *	Handle saved file definitions.	
 */

$action='import_students_action1.php';
$cancel=$action;
$ferror=0;
	

include('scripts/sub_action.php');

$nofields=$_SESSION['nofields'];

trigger_error($sub,E_USER_WARNING);

if($ferror>0){
	$error[]='Unable to open remote file.';
	}
elseif($sub=='Submit'){
	$fname=$_FILES['importfile']['tmp_name'];
	$fuser=$_FILES['importfile']['name'];
	$ferror=$_FILES['importfile']['error'];
	$ftype=$_FILES['importfile']['type'];
   	$file=fopen("$fname", 'r');
   	if(!$file){
		$error[]='Failed to open uploaded file.';
		}
	else{
		$idef=array();
		while(!feof($file)){
			$in=fgetcsv($file,999,',');
			array_push($idef, $in);			
			}

		$size=sizeof($idef);
   		$result[]='Succesfully uploaded	'.$size.' lines from file definition.';
		for($c=$size+1;$c<=$nofields;$c++){
			$in=array($c, '', '', '');
			array_push($idef, $in);						
			}	
		$_SESSION['idef']=$idef;
		}
	}

elseif($sub=='Save'){
	$fname=$_POST['importfile'];
	trigger_error('file: '.$fname,E_USER_WARNING);
	$idef=$_SESSION['idef'];
	$outname=$fname.'.cidef';
	$result[]='Saving file:'.$outname;
	$file=fopen('../data/'.$outname, 'w');	
	if(!$file){
		$error[]='Unable to open remote file for writing.';
		}
	else{
		for($c=0;$c<$nofields;$c++){
			$out=$c.','.$idef[$c][1].','.$idef[$c][2].','.$idef[$c][3].'\n';
			fputs($file,  $out);
			}
		fclose ($file);
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
