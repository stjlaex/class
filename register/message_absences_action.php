<?php
/**									message_absences_action.php
 *
 */

$action='absence_list.php';


if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}


include('scripts/sub_action.php');
include('scripts/answer_action.php');


$fromaddress=$CFG->schoolname;


if($recipients and sizeof($recipients)>0){
	$sentno=0;
	$failno=0;
	if($CFG->emailoff!='yes'){

		$footer='--'. "\r\n" . get_string('guardianemailfooterdisclaimer');
		$messagebody.="\r\n". $footer;

		foreach($recipients as $key => $recipient){
			
			$message="\r\n". $recipient['explanation']. "\r\n";
			$message.="\r\n".$recipient['messagebody'];		
			$message=utf8_to_ascii($message);

			$email_result=send_email_to($recipient['email'],$fromaddress,$messagesubject,$message);

			trigger_error('TO: '.$recipient['email'].' SUBJECT:'.$messagesubject.' BODY:'.$message,E_USER_WARNING);
			if($email_result){$sentno++;}
			else{$failno++;}

			}

		$result[]=get_string('emailsentto',$book).' '. $sentno.' '.get_string('contacts',$book);
		}

	if($failno>0){$result[]=get_string('failedtosend',$book).' '.$failno;}

	}
else{
	$result[]=get_string('nocontacts',$book);
	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
