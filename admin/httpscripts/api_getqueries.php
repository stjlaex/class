<?php

require('../../scripts/api_head_options.php');

if($action=='register'){
	if(isset($_POST['email']) and $_POST['email']!=''){$email=$_POST['email'];}else{$email='';}
	if(isset($_POST['classispath']) and $_POST['classispath']!=''){$classispath=$_POST['classispath'];}else{$classispath='';}
	if(isset($_POST['codeauth']) and $_POST['codeauth']!=''){$codeauth=$_POST['codeauth'];}else{$codeauth=false;}

	$d_u=mysql_query("SELECT username, title, surname, forename FROM users WHERE email='$email' LIMIT 1;");
	if($email!='' and mysql_num_rows($d_u)>0){
		$username=mysql_result($d_u,0,'username');
		$title=mysql_result($d_u,0,'title');
		$title=get_string(displayEnum($title, 'title'),'infobook');
		$surname=mysql_result($d_u,0,'surname');
		$forename=mysql_result($d_u,0,'forename');

		$registered=register($username,$device,$ip,1);
		$d_t=mysql_query("SELECT expire,token FROM api WHERE username='$username' and device='$device';");
		$refreshtoken=generateToken($username,mysql_result($d_t,0,'expire'));
		$token=mysql_result($d_t,0,'token');

		if($codeauth){
			$code=generateCode($token);
			$displaycode=" Code: $code<br>";
			}
		else{$displaycode='';}
		$messagesubject='Classis API/APP Register';
		$message="<p>$title $surname, $forename, <br>
				Your Classis details for the API/APP register.<br>
				 User: $username<br>
				 $displaycode
				 Token: $token<br>
				 Device: $device<br>
				Thank you!</p>";
		$messagetxt=strip_tags($message);

		if($registered and send_email_to($email,'',$messagesubject,$messagetxt,$message)){
			$result['success']=true;
			$result['action']=$action;
			$result['details'][]=array(
					'username'=>$username,
					'classispath'=>$classispath,
					'refreshtoken'=>$refreshtoken,
					'message'=>'An email will be sent with the API details. Thank you!'
				);
			if($codeauth){$resutl['details']['token']=$token;}
			}
		else{$errors[]='Couldn\'t register user: '.$username;}
		}
	else{$errors[]='Couldn\'t register user. Invalid email.';}
	}
elseif($action=='getstatements'){
	if(isset($_POST['profileid']) and $_POST['profileid']!=''){$profile=$_POST['profileid'];}else{$profile='';}
	if(isset($_POST['componentid']) and $_POST['componentid']!=''){$component=$_POST['componentid'];}else{$component='';}

	if($component!=''){$checkcomponent=" AND report_skill.component_id='$component' ";}
	else{$checkcomponent='';}
	$d_s=mysql_query("SELECT report_skill.id,report_skill.name,report_skill.component_id FROM report JOIN report_skill ON report_skill.profile_id=report.id WHERE report.title='$profile' AND year='$curryear' $checkcomponent;");
	if(mysql_num_rows($d_s)>0){
		$result['success']=true;
		$result['action']=$action;
		while($statements=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$result['statements'][]=array(
					'skillid'=>$statements['id'],
					'statement'=>$statements['name'],
					'component'=>$statements['component_id']
					);
			}
		}
	else{$errors[]='Statements not found for profile: '.$profile;}
	}
elseif($action=='getstudents'){
	if(isset($_POST['classid']) and $_POST['classid']!=''){$classid=$_POST['classid'];}else{$classid='';}

	if($classid!=''){
		$d_s=mysql_query("SELECT student.id,student.forename,student.surname,info.epfusername FROM cidsid JOIN student ON cidsid.student_id=student.id JOIN info ON student.id=info.student_id WHERE cidsid.class_id='$classid';");
		if(mysql_num_rows($d_s)>0){
			$result['success']=true;
			$result['action']=$action;
			while($students=mysql_fetch_array($d_s,MYSQL_ASSOC)){
				$epfu=$students['epfusername'];
				$sid=$students['id'];
				$forename=$students['forename'];
				$surname=$students['surname'];
				$image=get_student_photo($epfu,'','mini');
				$imagedata=base64_encode(file_get_contents($image));
				$imagesrc='data: '.mime_content_type($image).';base64,'.$imagedata;
				$result['students'][]=array(
							'sid'=>$sid,
							'forename'=>$forename,
							'surname'=>$surname,
							'profilephoto'=>$imagesrc
							);
				}
			}
		}
	else{$errors[]='Invalid parameters.';}
	}
elseif($action=='getcourses'){
	$d_c=mysql_query("SELECT id,name FROM course;");
	if(mysql_num_rows($d_c)>0){
		$result['success']=true;
		$result['action']=$action;
		while($courses=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$result['courses'][]=array(
					'coursesid'=>$courses['id'],
					'coursename'=>$courses['name']
					);
			}
		}
	else{$errors[]='Courses not found.';}
	}
elseif($action=='getclasses'){
	$teacherid=$username;
	$d_c=mysql_query("SELECT tidcid.class_id,class.name FROM tidcid JOIN class ON tidcid.class_id=class.id JOIN cohort ON class.cohort_id=cohort.id WHERE tidcid.teacher_id='$teacherid' AND cohort.year='$curryear';");
	if(mysql_num_rows($d_c)>0){
		$result['success']=true;
		$result['action']=$action;
		while($classes=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$result['classes'][]=array(
					'classid'=>$classes['class_id'],
					'classname'=>$classes['name']
					);
			}
		}
	else{$errors[]='Classes not found for user: '.$teacherid;}
	}
elseif($action=='getprofiles'){
	if(isset($_POST['courseid']) and $_POST['courseid']!=''){$courseid=$_POST['courseid'];}else{$courseid='FS';}

	$d_p=mysql_query("SELECT report.title,categorydef.name FROM categorydef JOIN assessment ON profile_name=name JOIN rideid ON assessment_id=assessment.id JOIN report ON report.id=rideid.report_id WHERE type='pro' AND categorydef.course_id='$courseid';");
	if(mysql_num_rows($d_p)>0){
		$result['success']=true;
		$result['action']=$action;
		while($profiles=mysql_fetch_array($d_p,MYSQL_ASSOC)){
			$result['profiles'][]=array(
					'profileid'=>$profiles['title'],
					'profilename'=>$profiles['name'],
					'courseid'=>$courseid
					);
			}
		}
	else{$errors[]='Profiles not found for course: '.$courseid;}
	}
elseif($action=='getcomponents'){
	if(isset($_POST['profileid']) and $_POST['profileid']!=''){$profileid=$_POST['profileid'];}else{$profileid='';}

	$d_c=mysql_query("SELECT component.id,component.subject_id,component.course_id,s1.name AS subjectname,s2.name AS componentname FROM component JOIN subject AS s1 ON component.subject_id=s1.id JOIN subject AS s2 ON component.id=s2.id WHERE subject_id='PSD' OR subject_id='CLL';");
	if(mysql_num_rows($d_c)>0){
		$result['success']=true;
		$result['action']=$action;
		while($components=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$result['components'][]=array(
					'componentid'=>$components['id'],
					'componentname'=>$components['componentname'],
					'subjectid'=>$components['subject_id'],
					'subjectname'=>$components['subjectname'],
					'courseid'=>$components['course_id'],
					'profileid'=>$profileid
					);
			}
		}
	else{$errors[]='Components not found for profile: '.$profileid;}
	}
elseif($action=='getsharedcomments'){
	if(isset($_POST['epfusername']) and $_POST['epfusername']!=''){$epfusername=$_POST['epfusername'];}else{$epfusername='';}
	$d_s=mysql_query("SELECT student_id FROM info WHERE epfusername='$epfusername';");
	$sid=mysql_result($d_s,0,'student_id');

	$Comments=fetchComments($sid,'','');
	if(count($Comments)>0){
		$result['success']=true;
		$result['action']=$action;
		$result['sid']=$sid;
		foreach($Comments['Comment'] as $Comment){
			$id=$Comment['id_db'];
			$bid=$Comment['Subject']['value'];
			$detail=$Comment['Detail']['value'];
			$title='Subject: ' .display_subjectname($bid);
			$body='<p>'.$detail.'</p>';
			if(isset($Comment['Shared']['value']) and $Comment['Shared']['value']=='1'){
				$result['comments'][]=array(
					'commentid'=>$id,
					'title'=>$title,
					'body'=>$body
					);
				}
			}
		}
	else{
		$errors[]="Comments not found for: ".$epfusername;
		}
	}
elseif($action=='getsharedcommentphotos'){
	if(isset($_POST['epfusername']) and $_POST['epfusername']!=''){$epfusername=$_POST['epfusername'];}else{$epfusername='';}
	if(isset($_POST['commentid']) and $_POST['commentid']!=''){$commentid=$_POST['commentid'];}else{$commentid='';}

	$files=list_files($epfusername,'comment',$commentid);
	$files=array_merge(list_files($epfusername,'assessment',$commentid),$files);
	if(sizeof($files)>0){
		$result['success']=true;
		$result['action']=$action;
		foreach($files as $file){
			$imagepath=$file['path'];
			$imagedata=base64_encode(file_get_contents($imagepath));
			$imagesrc='data: '.mime_content_type($imagepath).';base64,'.$imagedata;
			$result['photos'][]=array(
				'fileid'=>$file['id'],
				//'filedata'=>$imagesrc,
				'filepath'=>$imagepath
				);
			}
		}
	else{
		$errors[]='Photographs not found for the comment: '.$commentid;
		}
	}
elseif($action=='getreportphotos'){
	if(isset($_POST['epfusername']) and $_POST['epfusername']!=''){$epfusername=$_POST['epfusername'];}else{$epfusername='';}
	if(isset($_POST['reportid']) and $_POST['reportid']!=''){$reportid=$_POST['reportid'];}else{$reportid='';}

	global $CFG;
	require_once('../../../lib/eportfolio_functions.php');
	//$files=array();
	//$files=(array)list_files($epfusername,'comment',$commentid);
	$files=list_files($epfusername,'assessment',$reportid);
	$errors[]=print_r($files,true);
	if(sizeof($files)>0){
		$result['success']=true;
		$result['action']=$action;
		foreach($files as $file){
			$img_url=$file['url'];
			$b64_img=base64_encode(file_get_contents($img_url));
			$result['photos'][]=array(
				'id'=>$fid,
				'commentphoto'=>$b64_img
				);
			}
		}
	else{
		$errors[]='Photographs not found for the comment: '.$commentid;
		}
	}
else{
	$errors[]="Invalid action: $action";
	}

require('../../scripts/api_end_options.php');
?>
