<?php
/*												post.php
 */

$action='about.php';
$choice='';

$summary=clean_text($_POST{'summary'});
if(isset($_POST{'book'})){$book=$_POST{'book'};}else{$book='';}
$detail=clean_text($_POST{'detail'});
$entrydate=$_POST['date0'];
$subject=clean_text($_POST['subject']); 
$address=$_POST['address'];
$queue=$_POST['queue'];

include('scripts/sub_action.php');

	$footer='';
	$message=$subject."\r\n".'Date:
	'.$entrydate."\r\n".$CFG->version."\r\n". $book. "\r\n". 
				$detail. "\r\n" .'Posted by '.$tid. "\r\n" .$footer;
	$recipients[]=array('email'=> $queue.'@'.$CFG->support,
					'username'=>'ClaSS Support');
	if(isset($CFG->contact) and $queue=='support'){
		$recipients[]=array('email'=> $CFG->contact,'username'=>'ClaSS Contact');
		}
	$headers='From: ClaSS@'.$CFG->siteaddress . "\r\n" . 
				'Reply-To: noreply@'.$CFG->siteaddress . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();

   	foreach($recipients as $key => $recipient){
   		if(mail($recipient['email'],$summary,$message,$headers)){
	 		$result[]='Email sent to '.$recipient['username'];
			}
		else{$error[]='No mail sent!'.$recipient['username'].$recipient['email'];}
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
