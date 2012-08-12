<?php
/**									message_action.php
 *
 */

$action='student_list.php';

if(isset($_POST['messageto'])){$messageto=$_POST['messageto'];}
if(isset($_POST['messageop'])){$messageop=$_POST['messageop'];}
if(isset($_POST['share0'])){$share=$_POST['share0'];}else{$share='no';}

if(isset($_POST['messagebody'])){$messagebody=clean_text($_POST['messagebody'],false);}
if(isset($_POST['messagebcc'])){$messagebcc=clean_text($_POST['messagebcc']);}else{$messagebcc='';}
if(isset($_POST['replyto'])){$replyto=$_POST['replyto'];}else{$replyto='';}
if(isset($_POST['messagesubject'])){$messagesubject=clean_text($_POST['messagesubject']);}else{$messagesubject='';}
if(isset($_POST['messageformat'])){$messageformat=$_POST['messageformat'];}else{$messageformat='';}
if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}

if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}

if(isset($_SESSION[$book.'tutors'])){$tutors=$_SESSION[$book.'tutors'];}
else{$tutors=array();}
/* For the BCCs to staff */
$extrarecipients=array();
$explanation=$CFG->schoolname.': message sent to '.sizeof($recipients).' parents.';
if($share=='yes'){
	foreach($tutors as $tutor){
		$extrarecipients[]=$tutor;
		}
	}
if($messagebcc!=''){
	$extrarecipients[]=array('email'=>$messagebcc,
							 'explanation'=>$explanation);
	}
if(sizeof($CFG->emailguardianbccs)>0 and $messageop=='email'){
	foreach($CFG->emailguardianbccs as $messagebcc){
		$extrarecipients[]=array('email'=>$messagebcc,
								 'explanation'=>$explanation);
		}
	}
/* Add the BCCs on first. */
$recipients=array_merge($extrarecipients,$recipients);

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
	if($file_size>420){
		/* Limit to 400KB. */
		$error[]='The size of the file attachment is '.$file_size.'KB, it must be less than 400KB. No messages have been sent.';
		}
	}

if($sub=='Submit' and $recipients and sizeof($recipients)>0 and !isset($error)){
	$sentno=0;
	$failno=0;

	/* Sending emails.... */
	if($CFG->emailoff!='yes' and $messageop=='email'){

		if(isset($replyto)){
			$from=array('name'=>$CFG->schoolname,'email'=>$replyto);
			}
		else{
			$from='';
			}


		$footer=get_string('guardianemailfooterdisclaimer');
		/* Need both plain text and html versions of body.*/
		$messagebodytxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));


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

			$preset='';
			if($messageformat!=''){
				if($messageformat==1){
					/* This is the parent contact details sheet for verification. */
					$Phones=(array)$recipient['Contact']['Phones'];
					$Add=(array)$recipient['Contact']['Addresses'][0];
					$preset='<br />'. 
					$preset.='<p>'.$recipient['Contact']['DisplayFullName']['value'].'</p>';
					$preset.='<p>'.$recipient['Contact']['EmailAddress']['value'].'</p>';
					$preset.='<p>'.$Add['Street']['value'].'<br />'. $Add['Neighbourhood']['value'].' <br />'
						. $Add['Town']['value'].'<br /> '. $Add['Country']['value']. '<br /> '. $Add['Postcode']['value'].'</p>';
					foreach($Phones as $Phone){
						$preset.='<p>'.$Phone['No']['value'].'</p>';
						}
					}
				//$messagebodytxt.=strip_tags(html_entity_decode($preset, ENT_QUOTES, 'UTF-8'));
				}

			$messagehtml='<p>'.$recipient['explanation'].'</p>';
			$messagehtml.=$messagebody;
			$messagehtml.=$preset;
			$messagehtml.='<br /><hr><p>'. $footer.'</p>';

			$messagetxt='';
			$messagetxt.=$messagebodytxt;
			$messagetxt.="\r\n". '--'. "\r\n" . $footer;

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
	$result[]=get_string('nocontacts',$book);
	}


include('scripts/results.php');	
include('scripts/redirect.php');	
?>
