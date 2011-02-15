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
else{$template='address_labels';}


$Students['transform']=$template;
$Students['paper']='portrait';

if(isset($recipients) and sizeof($recipients)>0){
	$Students['recipients']=$recipients;
	$returnXML=$Students;
	$rootName='Students';
	}
elseif(isset($sids) and sizeof($sids)>0){
	$Recipients=array();
	$Recipients['Recipient']=array();
	while(list($sindex,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		$Contacts=(array)fetchContacts($sid);
		$sid_recipient_no=0;
		while(list($cindex,$Contact)=each($Contacts)){
			$Recipient=array();
			if($Contact['ReceivesMailing']['value']=='1' and $sid_recipient_no==0){
				/* Only contacts who are flagged to receive all mailings */
				if(sizeof($Contact['Addresses'])>0){
					$Recipient['Address']=$Contact['Addresses'];
					$Recipient['DisplayFullName']=$Contact['DisplayFullName'];
					$Recipient['explanation']=$Student['DisplayFullName'];
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
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>