<?php
/**                    new_teacher_action.php
 */

$action='new_teacher.php';

include('scripts/sub_action.php');

$result=array();
if($sub=='Load'){
	/*Load the teachers' details from a file*/
	$importfile=$_POST{'importfile'};
	$result[]=get_string('loadingfile').$importfile;
    include('scripts/file_import_csv.php');
	while(list($index,$d)=each($inrows)){
			$user=array();
			$user['userno']=$d[0];
			$user['username']=$d[1];
			$user['surname']=$d[2];
			$user['forename']=$d[3];
			$user['email']=$d[4];
			$user['role']=$d[5];
			$result[]=updateUser($user,'no',$CFG->shortkeyword);
			}
	}

/*Keyed in details for a single teacher*/
elseif($sub=='Submit'){
		$user=array();
		$user['username']=$_POST{'newtid'};
		$user['userno']=$_POST{'no'};
		$user['surname']=$_POST{'surname'};
		$user['forename']=$_POST{'forename'};
		$user['email']=$_POST{'email'};
		$user['role']=$_POST{'role'};
   		$result[]=updateUser($user,'no',$CFG->shortkeyword);
		}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
