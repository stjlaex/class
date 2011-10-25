<?php
/*												post.php
 */

$action='about.php';
$choice='';

$summary=clean_text($_POST['summary']);
if(isset($_POST['book'])){$book=$_POST['book'];}else{$book='';}
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['date0'];
$subject=clean_text($_POST['subject']); 
$address=$_POST['address'];
$queue=$_POST['queue'];
$recipients=array();

include('scripts/sub_action.php');

	$message=$subject."\r\n".
		'Date:	'.$entrydate."\r\n".$CFG->version."\r\n". $book. "\r\n". $detail. "\r\n" .
		'Posted by '.$_SESSION['username']. "\r\n".
		'School: '.$CFG->schoolname. "\r\n".
		'Client: '.$CFG->clientid. "\r\n";

	if(isset($CFG->clientid)){
		$clientid=$CFG->clientid;
		if($queue=='class-bug'){$queue='class_bug';}
		if($queue=='class-feature'){$queue='class_feature';}
		}

	$recipients[]=array('email'=> $queue.'@'.$CFG->support,
						'username'=>'ClaSS Support');
	if(isset($CFG->contact) and $queue=='support'){
		$recipients[]=array('email'=> $CFG->contact,
							'username'=>'ClaSS Contact');
		}

	$fromaddress=$CFG->schoolname.'<ClaSS@'.$CFG->siteaddress.'>';
	$headers='List-Id: '.$clientid . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();

	if(sizeof($recipients)>0 and $CFG->emailoff!='yes'){
		foreach($recipients as $key => $recipient){
			send_email_to($recipient['email'],$fromaddress,$summary,$message);
			$result[]='Email sent to '.$recipient['username'];
			}
		}
	else{$error[]='No mail sent!';}

include('scripts/results.php');
include('scripts/redirect.php');
?>
