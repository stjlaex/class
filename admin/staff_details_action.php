<?php
/**                    staff_details_action.php
 */

$action='staff_details.php';

$seluid=$_POST['newuid'];

include('scripts/sub_action.php');

if($sub=='Submit'){
   	$user=array();
   	$user['username']=$_POST{'username'};
   	$user['surname']=$_POST{'surname'};
   	$user['forename']=$_POST{'forename'};
   	$user['email']=$_POST{'email'};
   	$user['role']=$_POST{'role'};
   	$user['firstbookpref']=$_POST{'firstbookpref'};
   	$user['nologin']=$_POST{'nologin'};
	if(isset($_POST['password1'])){
	  if($_POST['password1']==$_POST['password2']){
	   	$user['passwd']=$_POST{'password1'};
		}
      else{
		$error[]=get_text('mistakematchingpasswords',$book);
		}
	  }
   	$result[]=updateUser($user,'yes');
	include('scripts/results.php');
   	}

include('scripts/redirect.php');	
?>
