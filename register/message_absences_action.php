<?php
/**									message_absences_action.php
 *
 */

$action='absence_list.php';


$recipients=array();

include('scripts/sub_action.php');

if($_POST['all0']=='yes'){
	$recipients=$_SESSION[$book.'unauthrecipients'];
	$recipients=$_SESSION[$book.'authrecipients'];
	}
elseif($_POST['unauth0']=='yes'){
	$recipients=$_SESSION[$book.'unauthrecipients'];
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



$fromaddress=$CFG->schoolname;

if($recipients and sizeof($recipients)>0){
	$sentno=0;
	$failno=0;
	if($CFG->emailoff!='yes'){

		$footer='--'. "\r\n" . get_string('guardianemailfooterdisclaimer');
		$messagesubject=$CFG->schoolname.': '.get_string('attendance',$book);

		foreach($recipients as $key => $recipient){

			$message="\r\n". get_string(displayEnum($recipient['relationship'],'relationship'),'infobook'). ' to '. $recipient['studentname']. "\r\n";
			$message.="\r\n". 'At 10:00AM this morning '. $recipient['studentname']. ' had not registered in school. Please could you contact the school to inform us of the reason for your child\'s absence.'."\r\n";
			$message.="\r\n".'A las 10 de la manana de hoy '. $recipient['studentname']. ' no se ha registrado en el colegio. Podria, por favor, contactar con el colegio e informarnos de los motivos por los que su hijo/a ha estado ausente?'."\r\n";
			$message.="\r\n". $footer;

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

unset($_SESSION[$book.'unauthrecipients']);
unset($_SESSION[$book.'authrecipients']);
include('scripts/results.php');	
include('scripts/redirect.php');	
?>
