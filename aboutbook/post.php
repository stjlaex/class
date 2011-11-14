<?php
/*												post.php
 */

$action='about.php';
$choice='';

$summary=clean_text($_POST['summary']);
if(isset($_POST['book'])){$book=$_POST['book'];}else{$book='';}
$detail=clean_text($_POST['detail']);
$subject=clean_text($_POST['subject']); 
$address=$_POST['address'];
$queue=$_POST['queue'];
$recipients=array();
$entrydate=date('Y-m-d');

include('scripts/sub_action.php');

$message.='<p>'.$subject.'</p>';
$message.='<ul><li>Date: '.$entrydate.'</li><li>Version: '.$CFG->version.'</li><li>Book: '. $book.'</li>';
$message.='<li>Posted by: '.$_SESSION['username']. '</li>'.
	'<li>School: '.$CFG->schoolname. '</li>'.
	'<li>Client: '.$CFG->clientid. '</li></ul>';
$message.='<p>'.$detail.'</p>';

	if(isset($CFG->clientid)){
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
	$messagetxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));

	if(sizeof($recipients)>0 and $CFG->emailoff!='yes'){
		foreach($recipients as $key => $recipient){
			send_email_to($recipient['email'],$fromaddress,$summary,$messagetxt,$message);
			$result[]='Email sent to '.$recipient['username'];
			}
		}
	else{$error[]='No mail sent!';}

include('scripts/results.php');
include('scripts/redirect.php');
?>
