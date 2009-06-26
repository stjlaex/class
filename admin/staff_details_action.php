<?php
/**                    staff_details_action.php
 */

$action='staff_details.php';
$action_post_vars=array('seluid');

if(isset($_POST['newuid0']) and $_POST['newuid0']!=''){$seluid=$_POST['newuid0'];}
if(isset($_POST['newuid1']) and $_POST['newuid1']!=''){$seluid=$_POST['newuid1'];}
if(isset($_POST['newuid2']) and $_POST['newuid2']!=''){$seluid=$_POST['newuid2'];}

include('scripts/sub_action.php');

if($sub=='Submit' and $_POST['seluid']!=''){
	$seluid=$_POST['seluid'];
   	$user=array();
   	$user['username']=clean_text($_POST['username']);
   	$user['surname']=clean_text($_POST['surname']);
   	$user['forename']=clean_text($_POST['forename']);
   	$user['title']=$_POST['title'];
   	$user['email']=($_POST['email']);
   	$user['emailpasswd']=($_POST['emailpasswd']);
   	$user['role']=$_POST['role'];
   	$user['senrole']=$_POST['senrole'];
   	$user['firstbookpref']=clean_text($_POST['book']);
   	$user['worklevel']=$_POST['worklevel'];
   	if(isset($_POST['nologin'])){$user['nologin']=$_POST['nologin'];}
	else{$user['nologin']='0';}
	if($_POST['pin1']!=''){
		if($_POST['pin1']==$_POST['pin2']){
			$user['userno']=clean_text($_POST['pin1']);
			$result[]=update_user($user,'yes',$CFG->shortkeyword);
			}
		else{
			$error[]=get_string('mistakematchingpasswords',$book);
			}
		}
	else{
		$result[]=update_user($user,'yes');
		}

	$aperm=get_admin_perm('u',$_SESSION['uid']);
	if($_SESSION['role']=='admin' or $aperm==1){

		$agroups=(array)list_admin_groups();
		foreach($agroups as $type=>$agroup){
			$agid=$agroup['gid'];
			if(isset($_POST["a$agid"]) and $_POST["a$agid"]==1){
				$newperms=array('r'=>1,'w'=>1,'x'=>1);
				}
			else{
				$newperms=array('r'=>0,'w'=>0,'x'=>0);
				}
			update_staff_perms($seluid,$agid,$newperms);
			}
		}

	include('scripts/results.php');
   	}

include('scripts/redirect.php');	
?>
