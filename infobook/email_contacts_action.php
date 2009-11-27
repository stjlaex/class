<?php
/**									email_contacts_action.php
 *
 */

$action='student_list.php';

$messagebody=clean_text($_POST['messagebody']);
if(isset($_POST['messageto'])){$messto=$_POST['messageto'];}
if(isset($_POST['messagebcc'])){$messagebcc=clean_text($_POST['messagebcc']);}else{$messagebcc='';}
if(isset($_POST['messagesubject'])){$messagesubject=clean_text($_POST['messagesubject']);}else{$messagesubject='';}
if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}
if(isset($_POST['messageoption'])){$messop=$_POST['messageoption'];}else{$messop='';}

if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}

// Get the uploaded file information
if(isset($_FILES['messageattach']) and $_FILES['messageattach']['name']!=''){
	$file_name=basename($_FILES['messageattach']['name']);
	$file_type=substr($file_name,strrpos($file_name, '.')+1);
	$file_size=$_FILES['messageattach']['size']/1024;
	if($file_type!='pdf'){
		/* Only allow pdf attachments. */
		$error[]='Only PDF files are permitted for attachments.';
		}
	if($file_size>150){
		/* Limit to 150KB. */
		$error[]='The size of the file attachment is '.$file_size.'KB, it should be less than 150KB.';
		}
	}

include('scripts/sub_action.php');


$fromaddress=$CFG->schoolname;


if($recipients and sizeof($recipients)>0 and !isset($error)){
	$sentno=0;
	$failno=0;
	if($CFG->emailoff!='yes' and $messop=='email'){

		$footer='--'. "\r\n" . get_string('guardianemailfooterdisclaimer');
		$messagebody.="\r\n". $footer;
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
			
			$message="\r\n". $recipient['explanation']. "\r\n";
			$message.="\r\n".$messagebody;		
			$message=utf8_to_ascii($message);

			$email_result=send_email_to($recipient['email'],$fromaddress,$messagesubject,$message,'',$attachments);

			//trigger_error('TO: '.$recipient['email'].' SUBJECT:'.$messagesubject.' BODY:'.$message,E_USER_WARNING);
			if($email_result){$sentno++;}
			else{$failno++;}

			}
		$result[]=get_string('emailsentto',$book).' '. $sentno.' '.get_string('contacts',$book);
		}
	elseif(isset($CFG->smsoff) and $CFG->smsoff=='no' and $messop=='sms'){

		foreach($recipients as $key => $recipient){
			
			$message=$recipient['explanation']. ":";
			$message.="\r\n".$messagebody;
			$message=utf8_to_ascii($message);

			$sms_result=send_sms_to($recipient['mobile'],$message);

			//trigger_error('TO: '.$recipient['mobile'].' BODY:'.$message,E_USER_WARNING);

			if($sms_result){$sentno++;}
			else{$failno++;}
			}

		$result[]=get_string('smssentto',$book).' '.$sentno.' '.get_string('contacts',$book);
		}

	if($failno>0){$result[]=get_string('failedtosend',$book).' '.$failno;}

	}
elseif(!isset($error)){
	$result[]=get_string('nocontacts',$book);
	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
