#! /usr/bin/php -q
<?php
/**
 *												 eportfolio_sync_users.php
 *
 */ 
$book='admin';
$current='eportfolio_sync_users.php';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if (ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/eportfolio_functions.php');



/**
 * Updates the epf db with all students and staff who have an
 * epfusername in the class db (and hence ldap) but who are not
 * yet present in the eportfolio. Updates the membership of all
 * communities.
 *
 */
	$yearcoms=(array)list_communities('year');
	$formcoms=(array)list_communities('form');
	$classes=(array)list_course_classes();
	$allteachers=(array)list_teacher_users();
	$Students=array();
	$yearusers=array();
	foreach($yearcoms as $com){
		$yid=$com['name'];
		$yearusers[$yid]=array();
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*heads of year only*/
		$owners=(array)list_pastoral_users($yid,$yearperms);
		foreach($owners as $uid => $user){
			if($user['role']!='office' and $user['role']!='admin'){
				$yearusers[$yid][]=strtolower($user['username']);
				}
			}
		$students=listin_community($com);
		foreach($students as $student){
			$sid=$student['id'];
			$Students[$sid]=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
			$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
			}
		}

	$formusers=array();
	foreach($formcoms as $formindex => $com){
		$formusers[$formindex]=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1));
		}


	/**
	 * Do all staff and communities for yeargroups and formgroups.
	 */

	$staff=array();
	$com=array('epfcomid'=>'','type'=>'staff','name'=>'all','displayname'=>'Staff');
	$epfcomid=elgg_update_community($com);
	$com['epfcomid']=$epfcomid;

	foreach($allteachers as $user){
		$Newuser=(array)fetchUser($user);
		/* Ignore anyone who has not yet got an epfusername (handled by ldap). */
   		$epfuid=-1;
		if(isset($user['epfusername']) and $user['epfusername']!=''){
			$epfuid=elgg_get_epfuid($user['epfusername'],'person',true);
			if($epfuid==-1){
				/* This is a new epfusername so add to the elgg db. */
				$epfuid=elgg_newUser($Newuser,'staff');
				}
			elgg_join_community($epfuid,$com);
			}
		$staff[$Newuser['Username']['value']]=$epfuid;
		}

	foreach($yearcoms as $com){
		$yid=$com['name'];
		$com['displayname']=get_yeargroupname($yid);
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$yearepfcomids[$yid]=$epfcomid;
		$comowners=$yearusers[$yid];
		foreach($comowners as $tid){
			if($staff[$tid]!=-1){
				$epfuid=$staff[$tid];
				elgg_join_community($epfuid,$com);
				}
			}
		/* Only one can be the owner and this makes it the last in the list. */
		elgg_update_community($com,$com,$epfuid);
		}

	foreach($formcoms as $formindex => $com){
		$fid=$com['name'];
		$com['displayname']=get_string('formgroup').' '.$fid;
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$formepfcomids[$fid]=$epfcomid;
		$users=(array)$formusers[$formindex];
		foreach($users as $user){
			$tid=$user['username'];
			if($epfuid!=-1){
				$epfuid=$staff[$tid];
				elgg_join_community($epfuid,$com);
				}
			}
		/* Only one can be the owner and this makes it the last in the list. */
		elgg_update_community($com,$com,$epfuid);
		}



	/**
	 * Now do all students.
	 */
	foreach($Students as $sid => $Student){
		$field=fetchStudent_singlefield($sid,'EPFUsername');
		$Student=array_merge($Student,$field);
		/* Ignore if they don't yet have an epfusername (handled by ldap). */
		$epfuid=-1;
		$epfun=$Student['EPFUsername']['value'];
		if($epfun!=''){
			$epfuid=elgg_get_epfuid($Student['EPFUsername']['value'],'person',true);
			if($epfuid==-1){
				/* New epfusername so add to elgg db.*/
				$epfuid=elgg_newUser($Student,'student');
				}
			$Students[$sid]['epfuid']=$epfuid;
			}

		/* Ignore if something went wrong or they don't yet have an
		 * epfusername yet (assigning epfusernames is handled by
		 * ldap_sync or db_sync). 
		 */
		if($epfuid!=-1){
			/* Join the student to pastoral groups */
			$fid=$Student['RegistrationGroup']['value'];
			if(isset($formepfcomids[$fid])){
				$com=array('epfcomid'=>$formepfcomids[$fid],'type'=>'form','name'=>'');
				elgg_join_community($epfuid,$com);
				}
			$yid=$Student['YearGroup']['value'];
			if(isset($yearepfcomids[$yid])){
				$com=array('epfcomid'=>$yearepfcomids[$yid],'type'=>'year','name'=>'');
				elgg_join_community($epfuid,$com);
				}
			/* Create virtual folders for holding files. */
			$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
			$epfgroupid=elgg_update_group($group);
			elgg_new_folder($epfuid,'Reports','group'.$epfgroupid);
			elgg_new_folder($epfuid,'Portfolio Work','group'.$epfgroupid);
			$Students[$sid]['epfgroupid']=$epfgroupid;

			/* Check if a profile photo has been cached and up load as an icon. */
			$filename=$epfun .'.jpeg';
			$filepath=$CFG->eportfolio_dataroot . '/cache/images/' . $filename;
			if(file_exists($filepath)){
				$description=get_yeargroupname($yid);					
				$filedata=array();
				$file_batch=array();
				$filedata['foldertype']='icon';
				$filedata['description']=$description;
				$filedata['title']=$description;
				$file_batch[]=array('epfusername'=>$epfun,'filename'=>$filename);
				$filedata['batchfiles']=$file_batch;
				elgg_upload_files($filedata,true);
				}

			}
		}







	/* Now do teaching groups */
	foreach($classes as $class){
		$cid=$class['id'];
		$epfcid=str_replace('/','-',$cid);
		$bidname=get_subjectname($class['subject_id']);
		$com=array('epfcomid'=>'','type'=>'class','name'=>$epfcid,'displayname'=>$bidname.': '.$epfcid);
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$d_t=mysql_query("SELECT teacher_id FROM tidcid WHERE class_id='$cid';");
		while($t=mysql_fetch_array($d_t,MYSQL_ASSOC)){
			elgg_join_community($staff[strtolower($t['teacher_id'])],$com);
			}
		$d_student=mysql_query("SELECT b.id FROM cidsid a, student b 
				WHERE a.class_id='$cid' AND b.id=a.student_id ORDER BY b.surname;");
		while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student['id'];
			if(isset($Students[$sid]) and isset($Students[$sid]['epfuid'])){
				elgg_join_community($Students[$sid]['epfuid'],$com);
				elgg_fix_homework($Students[$sid]['epfuid'],$com['epfcomid']);
				}
			}
		}


/*******************************
 *
 * Now synchronise the contacts
 *
 */

	/*TODO: Allow option of updates for a single yeargroup!!!!!!*/
	$yid='%';
	/*!!!!!!*/

	/* Want all contacts who may recieve any sort of mailing to be
	 * given an account.
	 */
	$d_c=mysql_query("SELECT DISTINCT guardian_id FROM gidsid JOIN
   					student ON gidsid.student_id=student.id 
   					WHERE student.yeargroup_id LIKE '$yid' AND gidsid.mailing!='0';");
	while($contact=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$gid=$contact['guardian_id'];
		$Contact=fetchContact(array('guardian_id'=>$gid));
		if($Contact['Title']['value']!=''){
			$Contact['Title']['value']=get_string(displayEnum($Contact['Title']['value'],'title'),'infobook');
			}
		$epfuid_contact=elgg_get_epfuid($Contact['EPFUsername']['value'],'person',true);

		/* List all the students linked to this contact (ie. in this family group). */
		$d_i=mysql_query("SELECT info.student_id, formerupn,
   				epfusername FROM info JOIN gidsid ON
   				gidsid.student_id=info.student_id WHERE
   				info.epfusername!='' AND gidsid.mailing!='0' AND
   				gidsid.guardian_id='$gid' ORDER BY info.formerupn ASC;");
		$no=0;
		$pwds=array();
		while($info=mysql_fetch_array($d_i,MYSQL_ASSOC)){
			$sid=$info['student_id'];
			$epfuid_student=elgg_get_epfuid($info['epfusername'],'person',true);

			if($Contact['EPFUsername']['value']!='' and $Contact['EPFUsername']['value']!=' '){
				/* Choice of two methods for setting the password. */
				if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){
					$d_s=mysql_query("SELECT dob FROM student WHERE id='$sid';");
					$dob=(array)explode('-',mysql_result($d_s,0));
					$pwd=$dob[2].$dob[1].$dob[0];
					}
				else{
					$d_s=mysql_query("SELECT formerupn FROM info WHERE student_id='$sid';");
					$pwd=good_strtolower(mysql_result($d_s,0));
					}

				if(strlen($pwd)>3){
					$Password=array('value'=>md5($pwd),'index'=>$no);
					$Contact['Password']=(array)$Password;
					if($epfuid_contact>0){
						/* Will only update name and email and password. */
						elgg_updateUser($epfuid_contact,$Contact,'guardian');
						}
					else{
						/* New account to be created in elgg BUT only if we have valid password. */
						$epfuid_contact=elgg_newUser($Contact,'guardian');
						}
					$no++;
					}
				/*
				 * Joining a family community involves simply an entry in
				 * friends and an access group, a family does not have a community of
				 * its own.
				 */
				if($epfuid_contact>0 and $epfuid_student>0){
					$epfgroupid=elgg_update_group(array('owner'=>$epfuid_student,'name'=>'Family'));
					elgg_join_community($epfuid_contact,array('epfcomid'=>$epfuid_student));
					elgg_join_group($epfuid_contact,array('epfgroupid'=>$epfgroupid,'name'=>'family','owner'=>$epfuid_student,'access'=>''));
					}
				}
			}
		}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
