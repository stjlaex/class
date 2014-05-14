<?php

require('../../scripts/api_head_options.php');

if($action=='register'){
	if(isset($_POST['email']) and $_POST['email']!=''){$email=$_POST['email'];}else{$email='';}
	if(isset($_POST['classispath']) and $_POST['classispath']!=''){$classispath=$_POST['classispath'];}else{$classispath='';}

	$d_u=mysql_query("SELECT username, title, surname, forename FROM users WHERE email='$email' LIMIT 1;");
	$username=mysql_result($d_u,0,'username');
	$title=mysql_result($d_u,0,'title');
	$title=get_string(displayEnum($title, 'title'),'infobook');
	$surname=mysql_result($d_u,0,'surname');
	$forename=mysql_result($d_u,0,'forename');

	$registered=register($username,$device,$ip,1);
	$d_t=mysql_query("SELECT expire,token FROM api WHERE username='$username' and device='$device';");
	$refreshtoken=generateToken($username,mysql_result($d_t,0,'expire'));
	$token=mysql_result($d_t,0,'token');

	$messagesubject='Classis API Register';
	$message="<p>$title $surname, $forename, <br>Your Classis details for the API register.<br> User: $username<br> Token: $token<br> Device: $device<br> Thank you!</p>";
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
		}
	else{$errors[]='Couldn\'t register user: '.$username;}
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
elseif($action=='getclasses'){
	$d_c=mysql_query("SELECT tidcid.class_id,class.name FROM tidcid JOIN class ON tidcid.class_id=class.id JOIN cohort ON class.cohort_id=cohort.id WHERE tidcid.teacher_id='$username' AND cohort.year='$curryear';");
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
	else{$errors[]='Classes not found for user: '.$username;}
	}
else{
	$errors[]="Invalid action: $action";
	}

require('../../scripts/api_end_options.php');
?>
