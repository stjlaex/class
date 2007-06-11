<?php
/*							    statementbank_action.php
 */

$action='statementbank.php';

include('scripts/sub_action.php');

if($sub=='Submit'){
	$importfile=$_POST['importfile'];
	$fname=$_FILES['importfile']['tmp_name'];
	$fuser=$_FILES['importfile']['name'];
	$ferror=$_FILES['importfile']['error'];
	$ftype=$_FILES['importfile']['type'];
	if($fname!=''){
	   	$result[]='Loading file '.$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
		    $in=0;
			$dbstat=connect_statementbank();
			if($dbstat==''){exit;}
			while(list($index,$d)=each($inrows)){
				$statement['crid']=$d[0];
				$statement['bid']=$d[1];
				$statement['pid']=$d[2];
				$statement['stage']=$d[3];
				$statement['area']=$d[4];
				$statement['subarea']=$d[5];
				$statement['ability']=$d[6];
				$statement['statement']=$d[7];
				if(add_statement($statement)=='yes'){$in++;}
				else{$error[]=mysql_error();}
				}
			}
		$result[]='Entered '.$in.' statements into the database.';
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>

















