<?php
/**									completion_reminder_action.php
 *
 */

$action='completion_list.php';


$recipients=array();

include('scripts/sub_action.php');

if($_POST['all0']=='yes'){
	$recipients=$_SESSION[$book.'recipients'];
	}
else{
	$current='';
	$action='';
	$choice='';

 	$result[]=get_string('noactiontaken');

	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}



if($recipients and sizeof($recipients)>0){
	if(!empty($CFG->emailregisternoreply)){
		$replyto=$CFG->emailregisternoreply;
		}
	else{
		if(is_array($CFG->emailnoreply)){
			$replyto=$CFG->emailnoreply[0];
			}
		else{
			$replyto=$CFG->emailnoreply;
			}
		}

	$footer=get_string('pastoralemailfooterdisclaimer');
	$messagesubject=$CFG->schoolname.': '.get_string('register',$book);

	foreach($recipients as $recipient){
		$message='<p>'.get_string('completeregisterreminder',$book,$recipient['explanation']).'</p>';
		$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
		$message.='<br /><hr><p>'. $footer.'<p>';
		$email_result=send_email_to($recipient['email'],'',$messagesubject,$messagetxt,$message,'',$replyto);
		if($email_result){
			$result[]=get_string('emailsentto','infobook').' '.get_teachername($recipient['username']);
			}
		
		}
	}
else{
	$result[]=get_string('no','infobook').' '.get_string('tutors','infobook');
	}

unset($_SESSION[$book.'recipients']);
include('scripts/results.php');	
include('scripts/redirect.php');	
?>
