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
elseif($action=='postinfobookcomment'){
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
