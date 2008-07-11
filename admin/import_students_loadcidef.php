<?php 
/**			   			   				import_students_loadcidef.php
 *
 *	Handle saved file definitions.	
 */

$action='import_students_action1.php';
$cancel=$action;
$ferror=0;
	

include('scripts/sub_action.php');

$nofields=$_SESSION['nofields'];

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

include('scripts/results.php');
include('scripts/redirect.php');
?>
