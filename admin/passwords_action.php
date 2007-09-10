<?php
/**                    new_teacher_action.php
 */

$action='passwords.php';

include('scripts/sub_action.php');

$result=array();
if($sub=='Submit'){

	$users=list_all_users();
	$usernolist=array();

	if($_POST['passwords0']=='yes'){
		$chars=9;
		$length=3;
		foreach($users as $uid => $user){
			if($user['username']!='administrator' and $user['nologin']!='1'){
				unset($nums);
				unset($code);
				while(count($nums)<$chars){$nums[rand(0,9)]=null;}
				while(strlen($code)<$length){$code.=array_rand($nums);}
				$user['userno']=$code;
				$user['passwd']='';
				$usernolist[]=$user['username']. 
						':'.$user['userno'].', '.$user['surname']. ','.$user['forename']. 
						', '.$user['role'].', '.$user['email'];
				$result[]=update_user($user,'yes',$CFG->shortkeyword);

				}
			else{$admin=$user;}
			}
		}
	elseif($_POST['emailadmin0']=='yes'){
		/*this will be used if the passwords have not been changed*/
		foreach($users as $uid => $user){
			if($user['username']!='administrator'){
				$usernolist[]=$user['username']. 
						', '.$user['surname']. ','.$user['forename']. 
						', '.$user['role'].', '.$user['email'];
				}
			else{$admin=$user;}
			}
		}

	unset($message);
	$email=$admin['email'];
	reset($usernolist);
	foreach($usernolist as $index => $line){
		$message=$message . $line."\r\n";
		}
	if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$',$email)){
		$headers=emailHeader();
		$footer='--'. "\r\n".get_string('emailfooterdisclaimer');
		$message=$message .$footer;
		$subject=get_string('emailusernolistsubject',$book);
		if(mail($email,$subject,$message,$headers)){
			$result[]=get_string('listofuserssenttoadmin',$book);
			}
		}

	}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
