<?php
/**				   				eportfolio_accounts_action.php
 *
 */

$choice='';
$cancel='eportfolio_accounts.php';
require_once('lib/eportfolio_functions.php');

include('scripts/sub_action.php');

$refresh=$_POST['refresh0'];
$staffcheck=$_POST['staffcheck0'];
$studentblank=$_POST['studentblank0'];
$photocheck=$_POST['photocheck0'];
$contactcheck=$_POST['contactcheck0'];
$contactblank=$_POST['contactblank0'];

if($refresh=='yes'){
	/* This empties content and relationships but leaves accounts as
	 *  they are.
	 */
	elgg_refresh();
	}


if($studentblank=='yes'){
	/* Clear out all students ready to regenerate them. 
	 * Everything to do with each account is lost.
	 */
	elgg_blank('Default_Student');
	$result[]='Students\' accounts blanked.';
	}

if($contactblank=='yes'){
	/* Clear out all contacts ready to regenerate them. 
	 * Everything to do with each account is lost.
	 */
	elgg_blank('Default_Guardian');
	$result[]='Contacts\' accounts blanked.';
	}


/**
 * TODO:
 * Temporary to set default icon photos for all students.
 * The photos still have to be placed in the elggdata/icons folders by some
 * other means.
 *
if($photocheck=='yes'){

	$yearcoms=(array)list_communities('year');
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$students=listin_community($com);
		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Student=fetchStudent_singlefield($sid,'EPFUsername');
			$epfuid=elgg_get_epfuid($Student['EPFUsername']['value'],'person',true);
			if($epfuid!=-1){elgg_set_student_photo($epfuid,$yid);}
			}
		}

	}
 */



include('scripts/results.php');
include('scripts/redirect.php');

						/* TODO: Make optional to grab their new epfusername and
						 * send them an and email with the details. 
						 */
						/*
						  $Contact=fetchContact(array('guardian_id'=>$gid));
						  $emailaddress=strtolower($Contact['EmailAddress']['value']);
						  if($CFG->emailoff!='yes' and $emailaddress!=''){
						  $fromaddress=$CFG->schoolname;
						  $subject=get_string('eportfolioemailsubject',$book);
						  $message=get_string('eportfolioguardianemail1',$book);
						  $message.= "\r\n". 'Your user-name is: ' 
						  .$Contact['EPFUsername']['value']. "\r\n";
						  
						// NOT sending out passwords!!!!!!!!!!!!!
						//$message.= "\r\n". 'Your password is: ' 
						//					.$firstchild. "\r\n";
						$message.=get_string('eportfolioguardianemail2',$book);
						$message.=get_string('eportfolioguardianemail3',$book);
						$message.= "\r\n".'Su nombre de usuario es: '.$Contact['EPFUsername']['value']. "\r\n";
						$message.=get_string('eportfolioguardianemail4',$book);
						$footer='--'. "\r\n" .get_string('guardianemailfooterdisclaimer');
						$message.="\r\n". $footer;
						
						// NOT sending anything!!!!!!!!!!!!!!!!!
						//send_email_to($emailaddress,$fromaddress,$subject,$message);
						}
						*/
?>
