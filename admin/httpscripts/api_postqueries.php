<?php

require('../../scripts/api_head_options.php');

$postdata=(array)json_decode(stripslashes($_POST['data']),true);

if($action=='poststatementphoto'){
	$students=$postdata['students'];
	$skillid=$postdata['skillid'];
	$photos=$postdata['photos'];
	$comment=$postdata['comment'];

	if(count($students)==0 or count($photos)==0 or $skillid==''){
		$errors[]='Invalid parameters';
		}
	else{

		$result['success']=true;
		$result['action']=$action;

		foreach($photos as $photo){
			$image=$photo['photo'];
			list($type, $image)=explode(';', $image);
			list($type, $filetype)=explode(':', $type);
			list($encode, $image)=explode(',', $image);
			if($filetype==='image/jpeg'){$extension='.jpg';}
			elseif($filetype==='image/png'){$extension='.png';}
			elseif($filetype==='image/gif'){$extension='.gif';}
			$image=str_replace(' ', '+', $image);
			$data=base64_decode($image);
			$filename=$photo['name'].$extension;
			$uniquename=uniqid().$extension;

			foreach($students as $sid){
				$d_e=mysql_query("SELECT epfusername FROM info WHERE student_id='$sid';");
				$epfu=mysql_result($d_e,0,'epfusername');
				$dir=$CFG->eportfolio_dataroot.'/cache';
				$file=$dir.'/'.$uniquename;
				if(file_put_contents($file, $data)){$success=true;}
				else{$success=false;}
				$result['photos'][]=array(
					'success'=>$success,
					'sid'=>$sid,
					'filepath'=>$file,
					'filename'=>$filename
					);

				$publishdata['foldertype']='assessment';
				$publishdata['title']='';
				$publishdata['batchfiles'][]=array('epfusername'=>$epfu,
												   'filename'=>$uniquename,
												   'originalname'=>$filename,
												   'linkedid'=>$skillid,
												   'description'=>'',
												   'tmpname'=>$file
												   );
				upload_files($publishdata);
				
				if($comment!=''){
					$yid=get_student_yeargroup($sid);
					$d_c=mysql_query("SELECT id FROM comments WHERE student_id='$sid' AND yeargroup_id='$yid' AND detail='$comment' AND entrydate='$today' AND teacher_id='$username';");
					if(mysql_num_rows($d_c)==0){
						mysql_query("INSERT INTO comments SET student_id='$sid',
									detail='$comment', entrydate='$today', yeargroup_id='$yid',
									subject_id='', category='', teacher_id='$username';");
						$commid=mysql_insert_id();
						}
					else{
						$commid=mysql_result($d_c,0,'id');
						}

					if($commid>0){
						$result['comment'][]=array(
							'success'=>true,
							'sid'=>$sid
							);
						$publishdata['foldertype']='comment';
						$publishdata['title']='';
						$publishdata['batchfiles'][]=array('epfusername'=>$epfu,
														   'filename'=>$uniquename,
														   'originalname'=>$filename,
														   'linkedid'=>$commid,
														   'description'=>'',
														   'tmpname'=>$file
														   );
						upload_files($publishdata);

						if(isset($CFG->eportfolio_db) and $CFG->eportfolio_db!=''){
							require_once($CFG->dirroot.'/lib/eportfolio_functions.php');
							$epfu=$Student['EPFUsername']['value'];
							$title='Subject: ' .display_subjectname($bid);
							$message='<p>'.$detail.'</p>';
							if($CFG->eportfolio_db!='' and $epfu!=''){
								/* Set guardians field in comments table to 1 to indicate shared. */
								mysql_query("UPDATE comments SET guardians='1' WHERE id='$commentid';");
								elgg_new_comment($epfu,$entrydate,$message,$title,$tid,$sid);
								$result[]='Shared with parents.';
								}
							}
						}
					}
				}
			}
		}
	}
elseif($action=='postenquiryform'){
	function parse_enquiry($formdata){
		$Students=array();$Contacts=array();$Data=array();
		foreach($formdata['students'] as $sno=>$student){
			foreach($student as $field=>$inval){
				$Students[$sno][$field]=$inval;
				if($field=='dob' or $field=='dateofbirth'){
					$additional_details=get_yeargroup_by_dob($inval);
					foreach($additional_details as $key=>$detail){
						$Students[$sno][$key]=$detail;
						}
					}
				}
			}
		foreach($formdata['contacts'] as $cno=>$contact){
			foreach($contact as $field=>$inval){
				if($field=='dob' or $field=='dateofbirth'){
					$inval=get_date_ymd($inval);
					}
				$Contacts[$cno][$field]=$inval;
				}
			}
		$Data['Students']=$Students;
		$Data['Contacts']=$Contacts;
		return $Data;
		}

	function get_date_ymd($dob){
		$dob_bits=explode('/',$dob);
		$dob=$dob_bits[2] .'-'.$dob_bits[1].'-'.$dob_bits[0];
		return $dob;
		}

	function get_yeargroup_by_dob($dob){
		$student=array();
		$student['dob']=get_date_ymd($dob);
		$dob_bits=explode('-',$student['dob']);

		$enrolyear=get_curriculumyear()+1;
		$lookup_index=$enrolyear - $dob_bits[0] - 1;
		while($lookup_index<2){
			$enrolyear++;
			$lookup_index=$enrolyear - $dob_bits[0] - 1;
			}
		$yid_lookups=array('2'=>'-2'
						,'3'=>'-1'
						,'4'=>'0'
						,'5'=>'1'
						,'6'=>'2'
						,'7'=>'3'
						,'8'=>'4'
						,'9'=>'5'
						,'10'=>'6'
						,'11'=>'7'
						,'12'=>'8'
						,'13'=>'9'
						,'14'=>'10'
						,'15'=>'11'
						,'16'=>'12'
						,'17'=>'13'
						);
		$student['yeargroup_id']=$yid_lookups[$lookup_index];
		$student['enrolyear']=$enrolyear;
		return $student;
		}

	$formdata=parse_enquiry($postdata);
	$Contacts=$formdata['Contacts'];
	foreach($Contacts as $Contact){
		$email=$Contact['email'];
		$surname=$Contact['surname'];
		$forename=$Contact['forename'];
		$d_g=mysql_query("SELECT id FROM guardian WHERE email='$email';");
		if(mysql_num_rows($d_g)>0){
			$gid=mysql_result($d_g,0);
			}
		else{
			mysql_query("INSERT INTO guardian (surname, forename, email) 
								VALUES ('$surname', '$forename', '$email');");
			$gid=mysql_insert_id();
			foreach($Contact['phones'] as $phone){
				$phoneno=$phone['number'];
				mysql_query("INSERT INTO phone (some_id, number) 
									VALUES ('$gid', '$phoneno');");
				}
			}
		$gids[]=$gid;
		}
	if(count($gids)>0){
		$Students=$formdata['Students'];
		foreach($Students as $Student){
			$dob=$Student['dob'];
			$surname=$Student['surname'];
			$forename=$Student['forename'];
			$note=$Student['message'];
			mysql_query("INSERT INTO student (surname, forename, dob) 
							VALUES ('$surname', '$forename', '$dob');");
			$sid=mysql_insert_id();
			mysql_query("INSERT INTO info SET student_id='$sid', appnotes='$note';");
			foreach($gids as $gid){
				mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");
				}
			$enrolstatus='EN';
			$comtype='enquired';
			$comname=$enrolstatus.':'.$Student['yeargroup_id'];
			$community=array('id'=>'','type'=>$comtype,'name'=>$comname,'year'=>$Student['enrolyear']);
			join_community($sid,$community);
			$sids[]=$sid;
			}
		if(count($sids)>0){
			$result['success']=true;
			$result['enquiry']=array(
				'sids'=>$sids,
				'gids'=>$gids,
				'results'=>print_r($postdata,true)
				);
			}
		else{$errors[]="Couldn't add students to ClassIS.";}
		}
	else{$errors[]="Couldn't add contacts to ClassIS.";}
	}
elseif($action=='postinfobookcomment'){
	$result['success']=false;
	$result['action']=$action;
	/*
	$sid=170;
	$postdata['students'][$sid]['sid']=$sid;
	$postdata['students'][$sid]['category']='5-1';
	$postdata['students'][$sid]['subjectid']='';
	$postdata['students'][$sid]['comment']='New Comment'
	$postdata['students'][$sid]['share']=true;
	mysql_query("INSERT INTO comments SET student_id='$sid',
				detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
				subject_id='$bid', category='$category', teacher_id='$tid';");
	$commentid=mysql_insert_id();
	$result['comments'][]=array(
		'success'=>$success,
		'sid'=>$sid,
		'commentid'=>$commentid
		);
	*/
	}
else{
	$errors[]="Invalid action: $action";
	}

require('../../scripts/api_end_options.php');
?>
