<?php
/**						httpscripts/contact_labels_print.php
 *
 * Allows for a list of sids to select the recipients (coming from the
 * Admin book probably). Or an already selected list of recipients set as a
 * session var within the InfoBook.
 */

require_once('../../scripts/http_head_options.php');
$book='infobook';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];unset($_SESSION[$book.'recipients']);}
elseif(isset($_GET['sids'])){$sids=(array)$_GET['sids'];unset($_SESSION[$book.'recipients']);}
elseif(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
//elseif(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}
if(isset($_POST['template'])){$template=$_POST['template'];}
elseif(isset($_GET['template'])){$template=$_GET['template'];}
else{$template='address_labels2x7';}
if(isset($_POST['explanation'])){$explanation=$_POST['explanation'];}
elseif(isset($_GET['explanation'])){$explanation=$_GET['explanation'];}
else{$explanation='blank';}

$Students['transform']=$template;
$Students['paper']='portrait';
$Students['homecountry']=strtoupper($CFG->sitecountry);
$Students['explanation']=$explanation;


if(isset($recipients) and sizeof($recipients)>0){
	$Students['recipients']=$recipients;
	$returnXML=$Students;
	$rootName='Students';
	}
/* this is de precated - was used for labels button in admin -> enrolment_list.php*/
/*
elseif(isset($sids) and sizeof($sids)>0){
	$Recipients=array();
	$Recipients['Recipient']=array();
	foreach($sids as $sid){
		$Student=fetchStudent_short($sid);
		$Contacts=(array)fetchContacts($sid);
		$sid_recipient_no=0;
		foreach($Contacts as $Contact){
			$Recipient=array();
			if($Contact['ReceivesMailing']['value']=='1' and $sid_recipient_no==0){
				if(sizeof($Contact['Addresses'])>0){
					$Recipient['Address']=$Contact['Addresses'];
					$Recipient['DisplayFullName']=$Contact['DisplayFullName'];

					if($explanation=='studentname'){
						$Recipient['explanation']=$Student['DisplayFullName'];
						}
					elseif($explanation=='enrolmentno'){
						$Recipient['explanation']=$Student['EnrolmentNumber'];
						}
					else{$Recipient['explanation']='';}
					trigger_error($explanation,E_USER_WARNING);
					$Recipients['Recipient'][]=$Recipient;
					$sid_recipient_no++;
					}
				}
			}
		}
	$Students['recipients']=$Recipients;
	$returnXML=$Students;
	$rootName='Students';
	}

*/
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>