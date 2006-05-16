<?php
/**                    new_teacher_action.php
 */

$action='passwords.php';

include('scripts/sub_action.php');

$result=array();
if($sub=='Submit'){

	$users=getAllStaff();

	if($_POST['newpasswords']=='regenerate'){
		$chars=9;
		$length=3;
		foreach($users as $uid => $user){
			if($user['username']!='administrator'){
				unset($nums);
				unset($code);
				while(count($nums)<$chars){$nums[rand(0,9)]=null;}
				while(strlen($code)<$length){$code.=array_rand($nums);}
				$user['userno']=$code;
				$user['passwd']='';
				$usernolist[]=$user['surname']. ','.$user['forename']. 
						'   '.$user['username'].' : '.$user['userno'];
				$result[]=updateUser($user,'yes',$CFG->shortkeyword);

				if($_POST['emailstaff']=='reminders'){
					//		$email=$user['email'];
					$email='stj@laex.org';
					if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $email)){
						$headers=emailHeader();
						$footer='--'. "\r\n" .get_string('emailfooterdisclaimer');
						$message=get_string('emailnewloginuserno',$book)."\r\n";
						$message=$message .get_string('username').':'.$user['username']."\r\n";
						$message=$message .get_string('keynumber',$book).':'.$code."\r\n";
						$message=$message ."\r\n".$footer;
						$subject=get_string('emailnewloginsubject',$book);
						if(mail($email,$subject,$message,$headers)){
							$result[]='Email sent to '.$user['username'];
							}
						}
					}
				}
			else{$admin=$user;}
			}

		unset($message);
		$email=$admin['email'];
		$email='stj@laex.org';
		foreach($usernolist as $index => $line){
			$message=$message . $line."\r\n";
			}
		if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $email)){
			$headers=emailHeader();
			$footer='--'. "\r\n".get_string('emailfooterdisclaimer');
			$message=$message .$footer;
			$subject=get_string('emailusernolistsubject',$book);
			if(mail($email,$subject,$message,$headers)){
				$result[]='List of user numbers sent to administrator'."\r\n";
				}
			}
		}
	}
include('scripts/results.php');
include('scripts/redirect.php');	
?>



















