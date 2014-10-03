<?php
/**									message_action.php
 *
 */

$action='staff_list.php';

if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}
if(isset($_POST['messageop'])){$messageop=$_POST['messageop'];}

if(isset($_POST['messagebody'])){$messageb=clean_text($_POST['messagebody'],false);}
if(isset($_POST['messagebcc'])){$messagebcc=clean_text($_POST['messagebcc']);}else{$messagebcc='';}
if(isset($_POST['replyto'])){$replyto=$_POST['replyto'];}else{$replyto='';}
if(isset($_POST['messagesubject'])){$messagesubject=clean_text($_POST['messagesubject']);}else{$messagesubject='';}
if(isset($_POST['messageformat'])){$messageformat=$_POST['messageformat'];}else{$messageformat='';}
if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}

if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}

include('scripts/sub_action.php');

if($sub=='' and (isset($messageto) or isset($messageop))){
	$action_post_vars=array('messageto','messageop','share');
	$action='message.php';
	$cancel=$action;
	include('scripts/redirect.php');
	exit;
	}

// Get the uploaded file information
if($sub=='Submit' and isset($_FILES['messageattach']) and $_FILES['messageattach']['name']!=''){
	$file_name=basename($_FILES['messageattach']['name']);
	$file_type=substr($file_name,strrpos($file_name, '.')+1);
	$file_size=round($_FILES['messageattach']['size']/1024);
	if($file_type!='pdf'){
		/* Only allow pdf attachments. */
		$error[]='Only PDF files are permitted for attachments.';
		}
	if($file_size>490){
		/* Limit to 500KB. */
		$error[]='The size of the file attachment is '.$file_size.'KB, it must be less than 400KB. No messages have been sent.';
		}
	}

if($sub=='Submit' and $recipients and sizeof($recipients)>0 and !isset($error)){
	$sentno=0;
	$failno=0;

	/* Sending emails.... */
	if($CFG->emailoff!='yes' and $messageop=='email'){
		if(isset($replyto) and isset($CFG->emailnoreplyname[$replyto])){
			$from=array('name'=>$CFG->emailnoreplyname[$replyto],'email'=>$replyto);
			}
		elseif(isset($replyto) and !isset($CFG->emailnoreplyname[$replyto])){
			$from=array('name'=>$CFG->schoolname,'email'=>$replyto);
			}
		else{
			$from='';
			}


		$footer=get_string('emailfooterdisclaimer');

		$attachments=array();
		if(isset($file_name)){
			//copy the temp. uploaded file to uploads folder
			$upload_path='/tmp/';
			$upload_file=$upload_path. $file_name;
			$tmp_path=$_FILES['messageattach']['tmp_name'];
			if(is_uploaded_file($tmp_path)){
				if(!copy($tmp_path,$upload_file)){
					$error[]='Failed to upload file attachment!';
					}
				else{
					$attachments[]=array('filepath'=>$upload_file,'filename'=>$file_name);
					/*DOING!!!!!!!!!!!*/

					/*DOING!!!!!!!!!!!!*/
					}
				}
			}

		foreach($recipients as $key => $recipient){
			$messagebody=$messageb;
			/* This is the parent contact details sheet for verification. */
			/*
			$preset='';
			if($messageformat!=''){
				$Content=array();
				if($messageformat=='message_contact_update' and isset($recipient['sid'])){
					$Content['Student']=$recipient['Student'];
					$Content['Siblings']=$recipient['Siblings'];
					}
				$preset=html_message_transform($Content,$messageformat);
				}
			*/
			/* Need both plain text and html versions of body.*/
			$tags=getTags(true,'staff',array('user_id'=>$recipient['uid']));
			$messagebody=getMessage($tags,$messagebody,'false');
			$messagebodytxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));

			$messagehtml='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>'.'<p>'.$recipient['explanation'].'</p>';

			$messagehtml.=$messagebody;
			//if($preset){$messagehtml.=$preset;}

			//$messagehtml.='<br /><hr><p>'. $footer.'</p></body></html>';
			$messagehtml.='</body></html>';

			$messagetxt='';
			$messagetxt.=$messagebodytxt;
			//$messagetxt.="\r\n". '--'. "\r\n" . $footer;

			/*DOING!!!!!!!!!!!!*/

			/*DOING!!!!!!!!!!!!*/
			$email_result=send_email_to($recipient['email'],$from,$messagesubject,$messagetxt,$messagehtml,$attachments);
			if($email_result){$sentno++;}
			else{$failno++;}
			}
		$sentno=$sentno-sizeof($extrarecipients);
		$result[]=get_string('emailsentto',$book).' '. $sentno;
		}

	/* Sending SMS Text messages.... */
	elseif(isset($CFG->smsoff) and $CFG->smsoff=='no' and $messageop=='sms'){

		$messagebody=str_replace("'",'',$messagebody);
		$messagetxt=iconv('UTF-8','ISO-8859-1',$messagebody);

		foreach($recipients as $key => $recipient){

			$sms_result=send_sms_to($recipient['mobile'],$messagetxt);

			if($sms_result){$sentno++;}
			else{$failno++;}
			}

		$result[]=get_string('smssentto',$book).' '.$sentno;
		}

	if($failno>0){$result[]=get_string('failedtosend',$book).' '.$failno;}

	}
elseif($sub=='Submit' and !isset($error)){
	$result[]=get_string('nousers',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
