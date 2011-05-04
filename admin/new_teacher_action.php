<?php
/**                    new_teacher_action.php
 */

$action='new_teacher.php';

include('scripts/sub_action.php');

$result=array();
if($sub=='Submit'){
	/*Load the teachers' details from a file*/
	$importfile=$_POST['importfile'];
	$result[]=get_string('loadingfile').$importfile;
    include('scripts/file_import_csv.php');
	while(list($index,$d)=each($inrows)){
			$user=array();
			$user['username']=$d[0];
			$user['surname']=$d[1];
			$user['forename']=$d[2];
			$user['title']=$d[3];
			$user['email']=$d[4];
			$user['role']=$d[5];
			$user['personalcode']=$d[6];
			$user['street']=$d[7];
			$user['postcode']=$d[8];
			$user['homephone']=$d[9];
			$user['mobilephone']=$d[10];
			$user['dob']=$d[11];
			$user['contractdate']=$d[12];
			$result[]=update_user($user,'no',$CFG->shortkeyword);
			}
	}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
