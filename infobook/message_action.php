<?php
/**									message_action.php
 *
 */

$action='student_list.php';

if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}
if(isset($_POST['messageop'])){$messageop=$_POST['messageop'];}
if(isset($_POST['share0'])){$share=$_POST['share0'];}

if(isset($_POST['messagebody'])){$messagebody=clean_text($_POST['messagebody'],false);}
if(isset($_POST['messagebcc'])){$messagebcc=clean_text($_POST['messagebcc']);}else{$messagebcc='';}
if(isset($_POST['messagesubject'])){$messagesubject=clean_text($_POST['messagesubject']);}else{$messagesubject='';}
if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}

if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}

if(isset($_SESSION[$book.'tutors'])){$tutors=$_SESSION[$book.'tutors'];}
else{$tutors=array();}

/* For the BCCs to staff */
if($share=='yes'){
	foreach($tutors as $tutor){
		$recipients[]=$tutor;
		}
	}
if($messagebcc!=''){
	$tutor=array_pop($tutors);
	$extrarecipient=array('email'=>$messagebcc,'explanation'=>'',
						  'explanation'=>$tutor['explanation']);
	$recipients[]=$extrarecipient;
	}


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
	if($file_size>600){
		/* Limit to 500KB. */
		$error[]='The size of the file attachment is '.$file_size.'KB, it should be less than 600KB.';
		}
	}

if($sub=='Submit' and $recipients and sizeof($recipients)>0 and !isset($error)){
	$fromaddress=$CFG->schoolname;
	$sentno=0;
	$failno=0;
	if($CFG->emailoff!='yes' and $messageop=='email'){
		$footer='--'. "\r\n" . get_string('guardianemailfooterdisclaimer');
		$messagebody.="\r\n". $footer;
		/* TODO: needs testing and work to send in UTF-8 instead of converting? */
		//$message=utf8_to_ascii($message);
		$messagebody=iconv('UTF-8','ISO-8859-1',$messagebody);
		$messagesubject=iconv('UTF-8','ISO-8859-1',$messagesubject);

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
					}
				}
			}

		foreach($recipients as $key => $recipient){
			
			$message=iconv('UTF-8','ISO-8859-1',$recipient['explanation']);
			$message.=$messagebody;

			$email_result=send_email_to($recipient['email'],$fromaddress,$messagesubject,'',$message,$attachments);

			if($email_result){$sentno++;}
			else{$failno++;}

			}
		$result[]=get_string('emailsentto',$book).' '. $sentno;
		}
	elseif(isset($CFG->smsoff) and $CFG->smsoff=='no' and $messageop=='sms'){

		$messagebody=iconv('UTF-8','ISO-8859-1',$messagebody);
		foreach($recipients as $key => $recipient){
			
			$message=iconv('UTF-8','ISO-8859-1',$recipient['explanation']);
			$message.="\r\n".$messagebody;

			$sms_result=send_sms_to($recipient['mobile'],$message);

			//trigger_error('TO: '.$recipient['mobile'].' BODY:'.$message,E_USER_WARNING);

			if($sms_result){$sentno++;}
			else{$failno++;}
			}

		$result[]=get_string('smssentto',$book).' '.$sentno;
		}

	if($failno>0){$result[]=get_string('failedtosend',$book).' '.$failno;}

	}
elseif($sub=='Submit' and !isset($error)){
	$result[]=get_string('nocontacts',$book);
	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
