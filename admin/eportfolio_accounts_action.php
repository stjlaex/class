<?php
/**									eportfolio_accounts_action.php
 */

$action='eportfolio_accounts.php';
require_once('lib/eportfolio_functions.php');

include('scripts/sub_action.php');

$blank=$_POST['blank0'];
//$staffcheck=$_POST['staffcheck0'];
$studentcheck=$_POST['studentcheck0'];
$contactcheck=$_POST['contactcheck0'];
$contactblank=$_POST['contactblank0'];
$photocheck=$_POST['photocheck0'];


if($blank=='yes'){
	elgg_refresh();
	}

if($studentcheck=='yes'){

	/*get all ClaSS data first*/
	$yearcoms=(array)list_communities('year');
	$formcoms=(array)list_communities('form');
	$classes=(array)list_course_classes();
	$allteachers=(array)list_teacher_users();
	$Students=array();
	$yearusers=array();
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$yearusers[$yid]=array();
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*heads of year only*/
		$owners=(array)list_pastoral_users($yid,$yearperms);
		while(list($uid,$user)=each($owners)){
			if($user['role']!='office' and $user['role']!='admin'){
				$yearusers[$yid][]=strtolower($user['username']);
				}
			}
		$students=listin_community($com);
		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Students[$sid]=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
			$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
			}
		}

	$formusers=array();
	while(list($index,$com)=each($formcoms)){
		$fid=$com['name'];
		$d_form=mysql_query("SELECT teacher_id FROM form WHERE id='$fid'");
		$formusers[]=strtolower(mysql_result($d_form,0));
		}
	reset($formcoms);


	/* Now insert into elgg*/

	$staff=array();
	$com=array('epfcomid'=>'','type'=>'staff','name'=>'all','displayname'=>'Staff');
	$epfcomid=elgg_update_community($com);
	$com['epfcomid']=$epfcomid;
	while(list($index,$user)=each($allteachers)){
		$Newuser['id_db']=$user['uid'];
		$Newuser['Surname']['value']=$user['surname'];
		if($user['title']!=''){
			$Newuser['Forename']['value']=get_string(displayEnum($user['title'],'title'),'infobook');
			}
		else{
			$Newuser['Forename']['value']=$user['forename'];
			}
		$Newuser['EmailAddress']['value']=$user['email'];
		$Newuser['Username']['value']=strtolower($user['username']);
		$Newuser['Password']['value']=$user['passwd'];
		/* Don't want to create an epf user if they already have an account. */
		$epfuid=-1;
		if(isset($user['epfusername']) and $user['epfusername']!=''){
			$epfuid=elgg_get_epfuid($user['epfusername'],'person',true);
			}
		if($epfuid==-1){
			$epfuid=elgg_newUser($Newuser,'staff');
			}
		$staff[$Newuser['Username']['value']]=$epfuid;
		elgg_join_community($epfuid,$com);
		}

	reset($yearcoms);
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$yearepfcomids[$yid]=$epfcomid;
		$comowners=$yearusers[$yid];
		while(list($index,$tid)=each($comowners)){
			$epfuid=$staff[$tid];
			elgg_join_community($epfuid,$com);
			}
		/*Only one can be the owner and this makes it the last in the list.*/
		elgg_update_community($com,$com,$epfuid);
		}

	reset($formcoms);
	while(list($formindex,$com)=each($formcoms)){
		$fid=$com['name'];
		$tid=$formusers[$formindex];
		$epfuid=$staff[$tid];
		$epfcomid=elgg_update_community($com,$com,$epfuid);
		$com['epfcomid']=$epfcomid;
		$formepfcomids[$fid]=$epfcomid;
		elgg_join_community($epfuid,$com);
		}

	reset($Students);
	while(list($sid,$Student)=each($Students)){
		$epfuid=-1;
		unset($sepfu);
		$field=fetchStudent_singlefield($sid,'EPFUsername');
		$Student=array_merge($Student,$field);
		/* Don't want to create a epf user if they already have an account. */
		if($Student['EPFUsername']['value']!=''){
			$epfuid=elgg_get_epfuid($Student['EPFUsername']['value'],'person',true);
			}
		if($epfuid==-1){
			$epfuid=elgg_newUser($Student,'student');
			}
		$Students[$sid]['epfuid']=$epfuid;

		/* Join the student to pastoral groups*/
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
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
		$epfgroupid=elgg_update_group($group);
		elgg_new_folder($epfuid,'Reports','group'.$epfgroupid);
		elgg_new_folder($epfuid,'Portfolio Work','group'.$epfgroupid);
		$Students[$sid]['epfgroupid']=$epfgroupid;
		}


	/* Now do teaching groups */
	while(list($index,$class)=each($classes)){
		$cid=$class['id'];
		$epfcid=str_replace('/','-',$cid);
		$com=array('epfcomid'=>'','type'=>'class','name'=>$epfcid);
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$d_t=mysql_query("SELECT teacher_id FROM tidcid WHERE class_id='$cid';");
		while($t=mysql_fetch_array($d_t, MYSQL_ASSOC)){
			elgg_join_community($staff[strtolower($t['teacher_id'])],$com);
			}
		$d_student=mysql_query("SELECT b.id FROM cidsid a, student b 
				WHERE a.class_id='$cid' AND b.id=a.student_id ORDER BY b.surname");
		while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student['id'];
			if(isset($Students[$sid])){elgg_join_community($Students[$sid]['epfuid'],$com);}
			}
		}
	}

/**
 *
 * Temporary to set default icon photos for all students.
 * The photos still have to be placed in the elggdata/icons folders by some
 * other means.
 */
if($photocheck=='yes'){

	$yearcoms=(array)list_communities('year');
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$students=listin_community($com);
		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Student=fetchStudent_singlefield($sid,'EPFUsername');
			$epfuid=elgg_get_epfuid($Student['EPFUsername']['value'],'person',true);
			if($epfuid!='-1'){elgg_set_student_photo($epfuid,$yid);}
			}
		}
	}

if($contactcheck=='yes'){

	$yid=8;
	if($contactblank=='yes'){
		/* Clear out all contacts ready to regenerate them. 
		 * Everything to do with each account is lost and a new username
		 * is generated.
		 */
		elgg_blank('Default_Guardian');
		mysql_query("UPDATE guardian SET epfusername='';");
		}

	/* Want all contacts who may recieve any sort of mailing to be
			given an account. */
	$d_c=mysql_query("SELECT DISTINCT guardian_id FROM gidsid JOIN
						student ON gidsid.student_id=student.id 
						WHERE student.yeargroup_id LIKE '$yid' AND gidsid.mailing!='0';");
	while($contact=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$epfuid_contact=-1;
		$gid=$contact['guardian_id'];
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$d_i=mysql_query("SELECT info.student_id, formerupn,
					epfusername FROM info JOIN gidsid ON
					gidsid.student_id=info.student_id WHERE
					info.epfusername!='' AND  info.formerupn!='' AND gidsid.mailing!='0' AND
					gidsid.guardian_id='$gid' ORDER BY info.formerupn ASC;");
		while($info=mysql_fetch_array($d_i,MYSQL_ASSOC)){
			$sid=$info['student_id'];
			$epfuid_student=elgg_get_epfuid($info['epfusername'],'person',true);
			if($epfuid_contact==-1){
				if($yid=='%'){
					/* Need formerupn to use as part of their password.
					 * This will be for their youngest child in the school. 
					 */
					$firstchild=$info['formerupn'];
					}
				else{
					/* If only doing one yeargroup then only want to
					 * use the fomerupn of their child in this yeargroup.
					 */
					$d_s=mysql_query("SELECT id FROM student
						JOIN gidsid ON student.id=gidsid.student_id WHERE
						student.yeargroup_id='$yid' AND gidsid.guardian_id='$gid'
						ORDER BY dob ASC LIMIT 0,1;");
					$firstsid=mysql_result($d_s,0);
					$d_s=mysql_query("SELECT formerupn FROM info 
								WHERE student_id='$firstsid';");
					$firstchild=mysql_result($d_s,0);
					}

				if($Contact['Title']['value']!=''){
					$Contact['Title']['value']=get_string(displayEnum($Contact['Title']['value'],'title'),'infobook');
					}
				/* Don't want to create a new epf user if they already
					have an account. If you want to force a new
					account on a one-by-one basis then blank the
					epfusername in ClaSS for that contact.
				*/
				if($Contact['EPFUsername']['value']!=''){
					$epfuid_contact=elgg_get_epfuid($Contact['EPFUsername']['value'],'person',true);
					}
				if($epfuid_contact==-1){
					$Contact['firstchild']=$firstchild;
					$epfuid_contact=elgg_newUser($Contact,'guardian');
					/* Grab their new epfusername*/
					$Contact=fetchContact(array('guardian_id'=>$gid));
					$emailaddress=strtolower($Contact['EmailAddress']['value']);
					if($CFG->emailoff!='yes' and $emailaddress!=''){
						/* Email them the details. */
						$fromaddress=$CFG->schoolname;
						$subject=get_string('eportfolioemailsubject',$book);
						$message=get_string('eportfolioguardianemail1',$book);
						$message.= "\r\n". 'Your user-name is: ' 
											.$Contact['EPFUsername']['value']. "\r\n";
						//$message.= "\r\n". 'Your password is: ' 
						//					.$firstchild. "\r\n";
						$message.=get_string('eportfolioguardianemail2',$book);
						/*translation*/
						$message.=get_string('eportfolioguardianemail3',$book);
						$message.= "\r\n".'Su nombre de usuario es: '.$Contact['EPFUsername']['value']. "\r\n";
						$message.=get_string('eportfolioguardianemail4',$book);
						/*disclaimer*/
						$footer='--'. "\r\n" .get_string('guardianemailfooterdisclaimer');
						$message.="\r\n". $footer;
						send_email_to($emailaddress,$fromaddress,$subject,$message);
						}
					}
				}

			/*
			 * Joining a family community involves simply an entry in
			 * friends and an access group, a family does not have a community of
			 * its own.
			 */
			$epfgroupid=elgg_update_group(array('owner'=>$epfuid_student,'name'=>'Family'));
			elgg_join_community($epfuid_contact,array('epfcomid'=>$epfuid_student));
			elgg_join_group($epfuid_contact,array('epfgroupid'=>$epfgroupid,'name'=>'family','owner'=>$epfuid_student,'access'=>''));
			}
		}
	}


include('scripts/redirect.php');
?>
