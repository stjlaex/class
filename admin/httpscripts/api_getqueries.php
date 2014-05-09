<?php

require('../../scripts/api_head_options.php');

$action=$_GET['action'];

if($action=='getstatements'){
	$profile='EYFS2436';
	$component='CL:LA';
	$d_s=mysql_query("SELECT report_skill.id,report_skill.name FROM report JOIN report_skill ON report_skill.profile_id=report.id WHERE report.title='$profile' AND report_skill.component_id='$component';");
	if(mysql_num_rows($d_s)>0){
		$result['success']='true';
		$result['action']=$action;
		while($statements=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$result['statements'][]=array(
					'skillid'=>$statements['id'],
					'statement'=>$statements['name']
					);
			}
		}
	}
elseif($action=='getstudents'){
	$classid=$_GET['classid'];
	$classid='675';
	$d_s=mysql_query("SELECT student.id,student.forename,student.surname,info.epfusername FROM cidsid JOIN student ON cidsid.student_id=student.id JOIN info ON student.id=info.student_id WHERE cidsid.class_id='$classid';");
	if(mysql_num_rows($d_s)>0){
		$result['success']='true';
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
elseif($action=='getclasses'){
	$user='admin2';
	$year=2014;
	$d_c=mysql_query("SELECT tidcid.class_id,class.name FROM tidcid JOIN class ON tidcid.class_id=class.id JOIN cohort ON class.cohort_id=cohort.id WHERE tidcid.teacher_id='$user' AND cohort.year='$year';");
	if(mysql_num_rows($d_c)>0){
		$result['success']='true';
		$result['action']=$action;
		while($classes=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$result['classes'][]=array(
					'classid'=>$classes['class_id'],
					'classname'=>$classes['name']
					);
			}
		}
	}
else{
	$errors[]=print_string('invalidaction','admin').": $action";
	}

require('../../scripts/api_end_options.php');
?>
