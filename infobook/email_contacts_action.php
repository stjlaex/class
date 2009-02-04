<?php
/**									email_contacts_action.php
 *
 */

$action='student_list.php';

$messagebody=clean_text($_POST['messagebody']);
if(isset($_POST['messagesubject'])){$messagesubject=clean_text($_POST['messagesubject']);}else{$messagesubject='';}
if(isset($_POST['messagebcc'])){$messagebcc=clean_text($_POST['messagebcc']);}else{$messagebcc='';}
if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}
if(isset($_POST['messageoption'])){$messop=$_POST['messageoption'];}else{$messop='';}

if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}

//TODO: file attacments

include('scripts/sub_action.php');


$fromaddress=$CFG->schoolname;


if($recipients and sizeof($recipients)>0){
	$sentno=0;
	if($CFG->emailoff!='yes' and $messop=='emailcontacts'){

		$footer='--'. "\r\n" . get_string('guardianemailfooterdisclaimer');
		$messagebody.="\r\n". $footer;

		foreach($recipients as $key => $recipient){
			
			$message="\r\n". $recipient['explanation']. "\r\n";
			$message.="\r\n".$messagebody;		
			$message=utf8_to_ascii($message);

			send_email_to($recipient['email'],$fromaddress,$messagesubject,$message);

			//trigger_error('TO: '.$recipient['email'].' SUBJECT:'.$messagesubject.' BODY:'.$message,E_USER_WARNING);
			$sentno++;
			}
		$result[]=get_string('emailsentto',$book).' '. $sentno.' '.get_string('contacts',$book);
		}
	elseif(isset($CFG->smsoff) and $CFG->smsoff=='no' and $messop=='smscontacts'){

		foreach($recipients as $key => $recipient){
			
			$message="\r\n". $recipient['explanation']. "\r\n";
			$message.="\r\n".$messagebody;
			$message=utf8_to_ascii($message);

			//send_email_to($recipient['email'],$fromaddress,$messagesubject,$message);

			//trigger_error('TO: '.$recipient['mobile'].' SUBJECT:'.$messagesubject.' BODY:'.$message,E_USER_WARNING);
			$sentno++;
			}
		$result[]=get_string('smssentto',$book).' '.$sentno.' '.get_string('contacts',$book);
		}

	}
else{
	$result[]=get_string('nocontacts',$book);
	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
