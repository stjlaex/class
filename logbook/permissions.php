<?php	
/**						   			permissions.php
 *
 */


/**
 *
 * Given a sid and a bid this will return a numerical array which
 * lists the responsibles (both pastoral and academic) who have been
 * flagged to receive emails.
 *
 */
function list_sid_responsible_users($sid, $bid){

    $gids=array();
	$recipients=array();
	$yid=get_student_yeargroup($sid);
	$year=get_curriculumyear();

	/* First find all communities (yeargroup, form, house etc.) with a permissions group. */
	$coms=list_member_communities($sid,array('id'=>'','type'=>'','name'=>''));
	foreach($coms as $com){
		if(isset($com['groups']) and sizeof($com['groups'][$yid])>0){
			$gids[]=$com['groups'][$yid];
			}
		}

	/* Then academic permission groups by course and subject. */
	if($bid!='' and $bid!='%' and $bid!='General' and $bid!='G'){
	  	$d_class=mysql_query("SELECT course_id FROM cohort WHERE cohort.year='$year' AND 
						cohort.id=ANY(SELECT class.cohort_id FROM class JOIN cidsid
						ON cidsid.class_id=class.id WHERE class.subject_id='$bid' AND cidsid.student_id='$sid');");
		if(mysql_num_rows($d_class)>0){
			$crid=mysql_result($d_class,0);
			$d_group=mysql_query("SELECT gid FROM groups WHERE course_id='$crid' AND subject_id='%';");
			$group=mysql_fetch_array($d_group);
			$gids[]=$group['gid'];

			$d_group=mysql_query("SELECT gid FROM groups WHERE course_id='%' AND subject_id='$bid';"); 
			$group=mysql_fetch_array($d_group);
			$gids[]=$group['gid'];

			$d_group=mysql_query("SELECT gid FROM groups WHERE course_id='$crid' AND subject_id='$bid';"); 
			$group=mysql_fetch_array($d_group);
			$gids[]=$group['gid'];
			}
		}
	foreach($gids as $gid){
		$d_users=mysql_query("SELECT users.uid, users.username, users.email FROM users 
								JOIN perms ON users.uid=perms.uid WHERE perms.gid='$gid' AND perms.e='1' AND users.nologin!='1';");
		while($user=mysql_fetch_array($d_users)){
			if(check_email_valid($user['email'])){ 
				$recipients[$user['uid']]=array('username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}


	/*checks for boarders*/
	$Student=fetchStudent_singlefield($sid,'Boarder');
	if($Student['Boarder']['value']!='N'){
		$d_u=mysql_query("SELECT uid FROM perms JOIN groups ON perms.gid=groups.gid WHERE
				groups.type='p' AND groups.community_id=ANY(SELECT id FROM community WHERE type='accomodation');");
		//trigger_error($u['uid'].mysql_error(),E_USER_WARNING);
		while($u=mysql_fetch_array($d_u)){
			$user=get_user($u['uid'],'uid');
			//trigger_error($u['uid'],E_USER_WARNING);
			if(check_email_valid($user['email'])){ 
				$recipients[$user['uid']]=array('username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}


	/* Checks for special needs */
	/* TODO: make this optional at the user level or restrict within sections? */
	$Student=fetchStudent_singlefield($sid,'SENFlag');
	if($Student['SENFlag']['value']!='N'){
		$d_u=mysql_query("SELECT uid FROM users WHERE (role='sen') AND users.nologin!='1';");
		while($u=mysql_fetch_array($d_u)){
			$user=get_user($u['uid'],'uid');
			if(check_email_valid($user['email'])){ 
				$recipients[$user['uid']]=array('username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}

	return $recipients;
	}




/**
 *  Will return all details of users of interest based on the
 *	teaching staff for the curriculum area identified in the current
 *	selected respons in an array with the uid as the key.
 */
function list_responsible_users($tid,$respons,$r=0){
   	$users=array();
	$year=get_curriculumyear();

	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$d_cids=mysql_query("SELECT DISTINCT class.id FROM class JOIN cohort ON class.cohort_id=cohort.id WHERE
						  class. subject_id LIKE '$rbid' AND cohort.year='$year' AND cohort.course_id LIKE '$rcrid' ORDER BY id");
		while($cid=mysql_fetch_row($d_cids)){
			$d_users=mysql_query("SELECT DISTINCT uid,
			   	username, passwd, forename, surname, email, emailuser, nologin,
				firstbookpref, role, senrole, medrole, epfusername FROM users JOIN tidcid ON 
				users.username=tidcid.teacher_id WHERE
				tidcid.class_id='$cid[0]' AND users.nologin!='1' ORDER BY username");
		  while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
			$uid=$user['uid'];
			if(!array_key_exists($uid,$users)){
				$users[$uid]=$user;
				}
			}
		  }
		}
	return $users;
	}


/**
 *
 * Will return all details of users of interest based on the current
 * selected yeargroup in an array with the uid as the key
 * eg. a head of year would be 111
 *
 */
function list_pastoral_users($ryid,$perms){
   	$users=array();

	$r=$perms['r'];
	$w=$perms['w'];
	$x=$perms['x'];
	$d_g=mysql_query("SELECT gid FROM groups WHERE
			course_id='' AND subject_id='' AND community_id='0' AND yeargroup_id LIKE '$ryid' AND type='p';");

	if(mysql_num_rows($d_g)==0){
		mysql_query("INSERT INTO groups (yeargroup_id,type) VALUES ('$ryid','p');");
		}
	else{
		$gid=mysql_result($d_g,0);
		$d_users=mysql_query("SELECT DISTINCT users.uid,
			   	username, passwd, forename, surname, email, emailuser, nologin,
				firstbookpref, role, senrole, medrole, epfusername FROM users JOIN perms ON 
				users.uid=perms.uid WHERE perms.gid='$gid' AND perms.r='$r' 
				AND perms.w='$w' AND perms.x='$x'");
		while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
			$uid=$user['uid'];
			$users[$uid]=$user;
			}
		}

	return $users;
	}


/**
 * Will return all users and their perms with access to the given gid.
 *
 */
function list_group_users_perms($gid,$nologin='0'){
   	$users_perms=array();
	$d_users=mysql_query("SELECT DISTINCT users.uid, users.username, users.surname, 
				perms.r, perms.w, perms.x, perms.e, role FROM users JOIN perms ON 
				users.uid=perms.uid WHERE perms.gid='$gid' AND users.nologin LIKE '$nologin';");
	while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
		$uid=$user['uid'];
		$users_perms[$uid]=$user;
		}
	return $users_perms;
	}


/**
 * Will return perms for the given gid and uid.
 *
 *
 */
function get_group_perm($gid,$uid){
	$d_p=mysql_query("SELECT r, w, x, e FROM perms WHERE gid='$gid' AND uid='$uid';");
	if(mysql_num_rows($d_p)>0){
		$perm=mysql_fetch_array($d_u,MYSQL_ASSOC);
		}
	else{
		$perm=array('r'=>0,'w'=>0,'x'=>0,'e'=>0);
		}
	return $perm;
	}


/**
 * Singles out a special group of users with admin permissions
 * $type is b=budget, a=academic, p=pastoral, u=users, s=special access to restricted
 *
 * The return perm is either true or false.
 *
 */
function get_admin_perm($type,$uid){

	$d_p=mysql_query("SELECT r FROM perms JOIN groups ON perms.gid=groups.gid WHERE
					  perms.uid='$uid' AND groups.type='$type' AND groups.yeargroup_id='-9999';");
	if(mysql_num_rows($d_p)>0){
		$perm=1;
		}
	else{
		$perm=0;
		}
	return $perm;
	}


/**
 * Return an array of gids to which this user has at least 'r' perms,
 * restricted to one particular type of group.
 *
 * types are s=section, a=academic, b=budgets, p=pastoral, u=users 
 */
function list_user_groups($uid,$type){
	$d_p=mysql_query("SELECT perms.r, perms.gid FROM perms JOIN groups ON perms.gid=groups.gid WHERE
					  perms.uid='$uid' AND groups.type='$type' AND groups.yeargroup_id IS NULL;");
	$groups=array();
	while($p=mysql_fetch_array($d_p,MYSQL_ASSOC)){
		$groups[]=$p['gid'];
		}
	return $groups;
	}


/**
 *
 * Returns array of special groups with admin permissions identified
 * by group.yeargroup_id='-9999'.
 *
 * If they haven't already been created in the db table then calling
 * this function will take care of that too.
 *
 */
function list_admin_groups(){
	$groups=array('a'=>array('name'=>'academic'),
				  'p'=>array('name'=>'pastoral'),
				  's'=>array('name'=>'reserved'),
				  'u'=>array('name'=>'users'),
				  'b'=>array('name'=>'budgets')
				  );
	$d_g=mysql_query("SELECT gid,type FROM groups WHERE yeargroup_id='-9999';");
	if(mysql_num_rows($d_g)>=sizeof($groups)){
		while($group=mysql_fetch_array($d_g,MYSQL_ASSOC)){
			if(array_key_exists($group['type'],$groups)){$groups[$group['type']]['gid']=$group['gid'];}
			}
		}
	else{
		/* Check each individual group and create if it doesn't exist. */
		foreach($groups as $type=>$name){
			$d_g=mysql_query("SELECT gid,type FROM groups WHERE yeargroup_id='-9999' AND type='$type';");
			if(mysql_num_rows($d_g)==0){
				mysql_query("INSERT INTO groups (yeargroup_id,type) VALUES ('-9999','$type');");
				}
			}
		$groups=list_admin_groups();
		}

	return $groups;
	}



/**
 * Will return all details of all users.
 *
 */
function list_all_users($nologin='%'){
   	$users=array();
	$d_users=mysql_query("SELECT uid, username, passwd, forename,
				surname, title, email, emailuser, nologin, firstbookpref,
				role, worklevel, senrole, medrole, epfusername
				FROM users WHERE nologin LIKE '$nologin' ORDER BY username");
	while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){;
		$uid=$user['uid'];
		$users[$uid]=$user;
		}
	return $users;
	}

/**
 * Will return all teachers with active logins of a particular subject
 * area as defined by crid and bid. If no arguments given then all
 * teachers are returned.
 * 
 */
function list_teacher_users($crid='',$bid='',$nologin='0'){
	$users=array();
	$year=get_curriculumyear();
	if($crid!='' or $bid!=''){
		/*Get ids for the teachers of this subject and store in tids[]*/
		$d_teacher=mysql_query("SELECT DISTINCT teacher_id FROM tidcid JOIN
				class ON class.id=tidcid.class_id WHERE class.subject_id LIKE '$bid' 
				AND class.cohort_id=ANY(SELECT cohort.id FROM cohort WHERE cohort.course_id LIKE '$crid' AND cohort.year='$year') 
				ORDER BY teacher_id;");
		while($teacher=mysql_fetch_array($d_teacher,MYSQL_ASSOC)){
			$tid=$teacher['teacher_id'];
			$d_users=mysql_query("SELECT uid, username, passwd, forename,
					surname, title, email, emailuser, 
					nologin, firstbookpref, role, worklevel,
					senrole, medrole, epfusername FROM users WHERE
					username='$tid' AND nologin LIKE '$nologin';");
			$user=mysql_fetch_array($d_users,MYSQL_ASSOC);
			$users[$tid]=$user;
			}
		}
	else{
		/*Otherwise just return all active teaching staff ie. nologin=0*/
		$d_user=mysql_query("SELECT uid, username, passwd, forename,
				surname, title, email, emailuser,  
				nologin, firstbookpref, role, worklevel, senrole, 
				medrole, epfusername FROM users WHERE
				(role='teacher' or role='admin') AND nologin LIKE '$nologin' AND
				username!='administrator' ORDER BY username");
		while($user=mysql_fetch_array($d_user,MYSQL_ASSOC)){
			$tid=$user['username'];
			$users[$tid]=$user;	
			}
		}
	return $users;
	}

/**
 * Will return all users with active logins of a particular section
 * as defined by section. If no arguments given then all
 * medical users are returned.
 * 
 */
function list_medical_users($sectionid='-1'){
	$d_u=mysql_query("SELECT uid FROM users WHERE (role='medical' OR medrole='1') AND users.nologin!='1';");
	while($u=mysql_fetch_array($d_u)){
		$user=get_user($u['uid'],'uid');
		if(check_email_valid($user['email'])){
			if($section!='-1'){
				$sections=(array)list_sections(false);
				$access_groups=(array)list_user_groups($user['uid'],'s');
				foreach($sections as $section){
					if((in_array($section['gid'],$access_groups) and $section['id']==$sectionid) or count($access_groups)==0){
						$recipients[$user['uid']]=array('uid'=>$user['uid'],'username'=>$user['username'], 'email'=>$user['email']);
						}
					}
				}
			else{
				$recipients[$user['uid']]=array('uid'=>$user['uid'],'username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}
	return $recipients;
	}

/**
 * 
 */
function get_uid($tid){
	$d_users=mysql_query("SELECT uid FROM users WHERE username='$tid'");
	$uid=mysql_result($d_users,0);
	return $uid;
	}

/**
 * 
 */
function get_staff_epfusername($tid){
	$d_users=mysql_query("SELECT epfusername FROM users WHERE username='$tid'");
	$epfu=mysql_result($d_users,0);
	return $epfu;
	}

/**
 * Returns the full user record or -1 if none found. 
 * The key field can be either username (set fieldname to distinguish) or uid.
 *
 */
function get_user($id,$fieldname='username'){
	if($fieldname=='username'){
		$d_users=mysql_query("SELECT * FROM users WHERE username='$id'");
		}
	elseif($fieldname=='uid'){
		$d_users=mysql_query("SELECT * FROM users WHERE uid='$id'");
		}
	if(isset($d_users) and mysql_num_rows($d_users)==1){
		$user=mysql_fetch_array($d_users,MYSQL_ASSOC);
		}
	else{
		$user=-1;
		}
	return $user;
	}

/**
 * 
 */
function checkCurrentRespon($r,$respons,$required='subject'){
	$error=array();
	if($r>-1){
		$bid=$respons[$r]['subject_id'];
		$crid=$respons[$r]['course_id'];
		if($bid==''){$bid='%';}
		}
	if($required=='subject' and $bid==''){
		$error[]=get_string('selectresponsibility');
		}
	elseif($required=='course' and ($crid=='' or $crid=='%')){
		$error[]=get_string('selectcourseresponsibility');
		}
	return array($crid,$bid,$error);
	}

/**
 *  
 * Return perm for yeargroup
 *
 */
function getYearPerm($yid){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	foreach($_SESSION['prespons'] as $respon){
		if($respon['yeargroup_id']==$yid and $respon['community_id']==0){
			$perm['r']=$respon['r'];
			$perm['w']=$respon['w'];
			$perm['x']=$respon['x'];
			}
		}

	$aperm=get_admin_perm('p',$_SESSION['uid']);
	if($_SESSION['role']=='admin' or $aperm==1){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	elseif($_SESSION['role']=='office'){$perm['r']=1; $perm['w']=1; $perm['x']=0;}
	elseif($_SESSION['role']=='district'){$perm['r']=1; $perm['w']=0; $perm['x']=0;}
	return $perm;
	}

/**
 * 
 */
function getSENPerm($yid){
	/*return perm for sen in this yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$perm=getYearPerm($yid);
	if($_SESSION['senrole']=='1' or $_SESSION['role']=='sen'){$perm['r']=1;$perm['w']=1;}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}
	return $perm;
	}

/**
 * 
 */
function getMedicalPerm($yid){
	/*return perm for med in this yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$perm=getYearPerm($yid);
	if($_SESSION['role']=='medical' or $_SESSION['medrole']=='1'){$perm['r']=1;$perm['w']=1;}
	return $perm;
	}


/**
 * 
 */
function get_residence_perm(){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($_SESSION['prespons']);$c++){
		$resp=$_SESSION['prespons'][$c];
		if($resp['comtype']=='accomodation'){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=0;}
	return $perm;
	}


/**
 * 
 */
function getFormPerm($fid){
	/*return perm for form group*/
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;

	$comid=update_community(array('id'=>'','type'=>'form','name'=>$fid));

	$formyid=get_form_yeargroup($fid);

	$perm=get_community_perm($comid,$formyid);

	return $perm;
	}


/**
 * 
 */
function get_community_perm($comid,$comyid=''){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;

	foreach($_SESSION['prespons'] as $respon){
		if($respon['community_id']==$comid){
			/* Are they directly assigned to this form? */
			$perm['r']=$respon['r'];
			$perm['w']=$respon['w'];
			$perm['x']=$respon['x'];
			}
		elseif($comyid!='' and $respon['community_id']==0 and $respon['yeargroup_id']==$comyid ){
			/* Are they assigned to a year group to which this group belongs. */
			$perm['r']=$respon['r'];
			$perm['w']=$respon['w'];
			$perm['x']=$respon['x'];
			}
		}

	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	elseif($_SESSION['role']=='office'){$perm['r']=1; $perm['w']=1; $perm['x']=0;}
	elseif($_SESSION['role']=='district'){$perm['r']=1; $perm['w']=0; $perm['x']=0;}
	return $perm;
	}


/**
 * 
 */
function get_section_perm($secid){
	$perm=array('r'=>0,'w'=>0,'x'=>0);

	if(sizeof($_SESSION['srespons'])>0){
		foreach($_SESSION['srespons'] as $respon){
			if($respon['id']==$secid){
				$perm['r']=1;
				}
			}
		}
	else{
		$perm['r']=1;
		}

	$aperm=get_admin_perm('p',$_SESSION['uid']);
	if($_SESSION['role']=='admin' or $aperm==1){$perm['r']=1; $perm['w']=1; $perm['x']=1;}
	elseif($_SESSION['role']=='district'){$perm['r']=1;}

	return $perm;
	}


/**
 * 
 */
function getMarkPerm($mid,$respons){
	$d_class=mysql_query("SELECT class_id FROM midcid WHERE mark_id='$mid' LIMIT 1;");
	$class=get_this_class(mysql_result($d_class,0));
	/*this will only takes the first crid/bid, would be a prolem if
				the mark is defined across more than one type of class*/
	$bid=$class['bid'];
	$crid=$class['crid'];

	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if(($resp['subject_id']==$bid or $resp['subject_id']=='%') and
				($resp['course_id']==$crid or $resp['course_id']=='%')){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	return $perm;
	}

/**
 * 
 */
function getSubjectPerm($bid,$respons){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$crids=array();
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['subject_id']==$bid and $resp['course_id']=='%'){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		elseif($resp['course_id']!='%' and
			   ($resp['subject_id']=='%' or $resp['subject_id']==$bid)){
			$crids[$resp['course_id']]=$resp['course_id'];
			}
		}

	$courses=list_courses($bid);
	if(sizeof($crids)>=sizeof($courses) or $_SESSION['role']=='admin'){
		$perm['r']=1;
		$perm['w']=1;
		$perm['x']=1;
		}

	return $perm;
	}

/**
 * 
 */
function getCoursePerm($course,$respons){
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['subject_id']=='%' and $resp['course_id']==$course){
			$perm['r']=$resp['r'];
			$perm['w']=$resp['w'];
			$perm['x']=$resp['x'];
			}
		}
	if($_SESSION['role']=='admin'){$perm['r']=1; $perm['w']=1; $perm['x']=1;}		
	return $perm;
	}

/**
 * 
 * Returns arrays of fids, yids and houses ie. pastoral responisbilites for a 
 * given $respons array.
 *
 */
function list_pastoral_respon(){
	$rforms=array();
	$rhouses=array();
	$rregs=array();
	$ryears=array();
	$aperm=get_admin_perm('p',$_SESSION['uid']);
	$resperm=get_residence_perm();

	/* For a normal user split yeargroups, houses and forms into separate arrays. */
	foreach($_SESSION['prespons'] as $respon){
		if($respon['comtype']=='form'){
			$rforms[]=$respon;
			}
		elseif($respon['comtype']=='house'){
			$rhouses[]=$respon;
			}
		elseif($respon['comtype']=='reg'){
			$rregs[]=$respon;
			}
		elseif($respon['yeargroup_id']!='' and $respon['community_id']=='0'){
			/* All academic respons must have null yeargroup_id for this to work!*/
			$ryears[$respon['yeargroup_id']]=$respon['yeargroup_id'];
			}
		}

	/**
	 * In addition if the user has admin access to pastoral then
	 * return all year groups to ensure access to everything.
	 */
	if($aperm==1 or $resperm['x']==1){
		$ryears=array();
		$d_groups=mysql_query("SELECT DISTINCT yeargroup_id FROM groups WHERE type='p' 
								AND yeargroup_id IS NOT NULL ORDER BY yeargroup_id;");
		while($group=mysql_fetch_array($d_groups, MYSQL_ASSOC)){
			$ryears[]=$group['yeargroup_id'];
			}
		}


	return array('forms'=>$rforms,'years'=>$ryears,'houses'=>$rhouses,'regs'=>$rregs);
	}


/**
 *
 * Optional $update='yes' will amend an existing record.
 *
 * The value of $short should always really be $CFG->shortkeyword for
 * generating a password.
 * 
 */
function update_user($user,$update='no',$short='class'){
	global $CFG;
	$result='';
	$username=$user['username'];
	$surname=$user['surname'];
	$forename=$user['forename'];
	$title=$user['title'];
	$role=$user['role'];
	$homephone=$user['homephone'];
	$mobilephone=$user['mobilephone'];
	$personalcode=$user['personalcode'];
	$personalemail=$user['personalemail'];
	$jobtitle=$user['jobtitle'];
	$dob=$user['dob'];
	$contractdate=$user['contractdate'];
	$education2=$user['education2'];
	$education=$user['education'];
	if(isset($user['worklevel'])){
		$worklevel=$user['worklevel'];
		}
	else{
		if($role=='office'){$worklevel='-1';}
		elseif($role=='admin'){$worklevel='3';}
		elseif($role=='teacher'){$worklevel='0';}
		elseif($role=='support'){$worklevel='-1';}
		else{$worklevel='0';}
		}
	if(isset($user['firstbookpref'])){
		$firstbookpref=$user['firstbookpref'];
		}
	else{
		if($role=='office'){$firstbookpref='infobook';}
		elseif($role=='admin'){$firstbookpref='admin';}
		elseif($role=='teacher'){$firstbookpref='markbook';}
		elseif($role=='district'){$firstbookpref='admin';}
		elseif($role=='medical'){$firstbookpref='medbook';}
		elseif($role=='sen'){$firstbookpref='seneeds';}
		else{$firstbookpref='infobook';}
		}
    if(isset($user['email'])){
		if(check_email_valid($user['email'])){ 
			$user['email']=strtolower($user['email']);
			$email=$user['email'];
			if(isset($user['emailuser'])){$emailuser=$user['emailuser'];}
			else{$emailuser='';}
	   		}
		}
	else{
		$email='';
		$emailuser='';
		}
    if(isset($user['nologin'])){$nologin=$user['nologin'];}else{$nologin='0';}
    if(isset($user['senrole'])){$senrole=$user['senrole'];}else{$senrole='0';}
    if(isset($user['medrole'])){$medrole=$user['medrole'];}else{$medrole='0';}


	$d_user=mysql_query("SELECT username, surname, forename 
							FROM users WHERE username='$username'");
	if($olduser=mysql_fetch_array($d_user)){
		  if($update!='yes'){
				$result=$result.'Username '.$username.' already exists
							for a member of staff:
						'.$olduser['surname'].', '.$olduser['forename'];
				}
		  else{
			mysql_query("UPDATE users SET
				  surname='$surname', forename='$forename', title='$title',
					email='$email', emailuser='$emailuser', 
					personalemail='$personalemail', jobtitle='$jobtitle',  
					role='$role', senrole='$senrole', medrole='$medrole', worklevel='$worklevel', nologin='$nologin',
					firstbookpref='$firstbookpref', homephone='$homephone', mobilephone='$mobilephone', 
					personalcode='$personalcode', dob='$dob', education='$education', education2='$education2',
					contractdate='$contractdate' WHERE username='$username';");
			$result=$result.'Updated details for user '.$username;
			}
		}
	else{
		mysql_query("INSERT INTO users (username, forename,
					surname, title, email, emailuser, role, nologin, worklevel,
					senrole, medrole, firstbookpref, homephone, mobilephone, 
					 personalcode, dob, contractdate, education, education2) 
					VALUES ('$username', '$forename',
					 '$surname', '$title', '$email', '$emailuser', 
						'$role', '$nologin', '$worklevel',
					   '$senrole', '$medrole', '$firstbookpref', '$homephone', '$mobilephone', 
						 '$personalcode', '$dob', '$contractdate', '$education', '$education2');");
		$result=$result.'Username '.$username.' added.';
		}

 	/** 
	 * All users get a passwd based on the shortkeyword (set in $CFG) and a
	 * userno, this formula could be personalised to meet your needs.
	 */
	if(isset($user['userno']) and $user['userno']!=''){
		$assword=$short. $user['userno'];
		$d_user=mysql_query("UPDATE users SET passwd=md5('".$assword."') WHERE username='$username';");
		if($CFG->emailoff=='no' and !empty($user['userno'])){
			if(check_email_valid($user['email'])){ 
				//$headers=emailHeader();
				  $footer='--'. "\r\n" .get_string('emailfooterdisclaimer');
				  $message=get_string('emailnewloginuserno','admin')."\r\n";
				  $message=$message ."\r\n".get_string('username').': '.$username."\r\n";
				  $message=$message .get_string('keynumber','admin').': '.$user['userno']."\r\n";
				  $message=$message ."\r\n".$footer;
				  $subject=get_string('emailnewloginsubject','admin');
				  $fromaddress='ClaSS';
				  /* TODO: decide if update_user needs to send email? */
   				  //send_email_to($email,$fromaddress,$subject,$message);
				}
			}
		}

	return $result;
	}

/**
 * 
 * Needs a uid and a gid and will update or insert the supplied
 * permissions.
 *
 */
function update_staff_perms($uid,$gid,$newperms){
	$r=$newperms['r'];
	$w=$newperms['w'];
	$x=$newperms['x'];
	if(isset($newperms['e'])){$e=$newperms['e'];}else{$e='0';}

	if($r==0 and $w==0 and $x==0){
		mysql_query("DELETE FROM perms WHERE uid='$uid' AND gid='$gid' LIMIT 1");
		$result=get_string('removedresponsibility','admin');
		}
	else{
		if(mysql_query("INSERT perms (uid, gid, r, w, x, e) VALUES
				('$uid', '$gid', '$r', '$w', '$x', '$e')")){
			$result=get_string('assignednewresponsibilities','admin');
			}
		else{mysql_query("UPDATE perms SET r='$r', w='$w', x='$x',
				e='$e' WHERE uid='$uid' AND gid='$gid'"); 
			$result=get_string('updatedresponsibilities','admin');
			}
		}

	return $result;
	}


/**
 * Taken from Moodle (lib/moodlelib.php) for ClaSS without ammendment
 * Based on a class by Mukul Sabharwal [mukulsabharwal @ yahoo.com]
 *
 * @param string $pwd ?
 * @param string $data ?
 * @param string $case 'en' or 'de'
 * @return string
 */
function endecrypt($pwd, $data, $case='en'){

    if($case=='de'){
        $data=urldecode($data);
		}

    $key[]='';
    $box[]='';
    $temp_swap='';
    $pwd_length=0;

    $pwd_length=strlen($pwd);

    for($i=0; $i<=255; $i++){
        $key[$i]=ord(substr($pwd,($i % $pwd_length),1));
        $box[$i]=$i;
		}

    $x=0;

    for($i=0; $i<=255; $i++) {
        $x=($x + $box[$i] + $key[$i]) % 256;
        $temp_swap=$box[$i];
        $box[$i]=$box[$x];
        $box[$x]=$temp_swap;
		}

    $temp='';
    $k='';

    $cipherby='';
    $cipher='';

    $a=0;
    $j=0;

    for($i=0; $i<strlen($data); $i++) {
        $a=($a + 1) % 256;
        $j=($j + $box[$a]) % 256;
        $temp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$temp;
        $k=$box[(($box[$a] + $box[$j]) % 256)];
        $cipherby=ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
		}

    if($case=='de') {
        $cipher=urldecode(urlencode($cipher));
		} 
	else{
        $cipher=urlencode($cipher);
		}

    return $cipher;
	}



function fetchUser($uid='-1'){
	global $CFG;

   	$d_u=mysql_query("SELECT * FROM users WHERE uid='$uid';");
	$user=mysql_fetch_array($d_u,MYSQL_ASSOC);

	$User=array();
	$User['id_db']=$uid;
	$User['Username']=array('label' => 'username', 
							'field_db' => 'username',
							'type_db' => 'varchar(14)', 
							'value' => ''.$user['username']);
	$User['Surname']=array('label' => 'surname', 
						   'inputtype'=> 'required',
						   'table_db' => 'user', 
						   'field_db' => 'surname',
						   'type_db' => 'varchar(50)', 
						   'value' => ''.$user['surname']);
	$User['Forename']=array('label' => 'forename', 
						   'inputtype'=> 'required',
						   'table_db' => 'user', 
						   'field_db' => 'forename',
						   'type_db' => 'varchar(50)', 
						   'value' => ''.$user['forename']);
	$User['Title']=array('label' => 'title', 
						 'table_db' => 'user', 
						 'field_db' => 'title',
						 'type_db' => 'enum', 
						 'value' => ''.$user['title']);
	$User['EmailAddress']=array('label' => 'email', 
						 'table_db' => 'user', 
						 'field_db' => 'email',
						 'type_db' => 'varchar(200)', 
						 'value' => ''.$user['email']);
	$User['PersonalEmailAddress']=array('label' => 'personalemail', 
						 'table_db' => 'user', 
						 'field_db' => 'personalemail',
						 'type_db' => 'varchar(200)', 
						 'value' => ''.$user['personalemail']);
	$User['SENRole']=array('label' => 'senrole', 
						   'inputtype'=> 'required',
						   //'table_db' => 'user', 
						   'field_db' => 'senrole',
						   'type_db' => 'enum', 
						   'value' => ''.$user['senrole']);
	$User['MedRole']=array('label' => 'medrole', 
						   'inputtype'=> 'required',
						   //'table_db' => 'user', 
						   'field_db' => 'medrole',
						   'type_db' => 'enum', 
						   'value' => ''.$user['medrole']);
	$User['Role']=array('label' => 'role', 
						'inputtype'=> 'required',
						//'table_db' => 'user', 
						'field_db' => 'role',
						'type_db' => 'enum', 
						'value' => ''.$user['role']);
	$User['Language']=array('label' => 'language',
							'inputtype'=> 'required',
							//'table_db' => 'user',
							'field_db' => 'language',
							'type_db' => 'enum', 
							'value' => ''.$user['language']);
	$User['Worklevel']=array('label' => 'worklevel', 
							 'inputtype'=> 'required',
							 //'table_db' => 'user', 
							 'field_db' => 'worklevel',
							 'type_db' => 'enum', 
							 'value' => ''.$user['worklevel']);
	$User['FirstBook']=array('label' => 'firstbookpref', 
							 'inputtype'=> 'required',
							 //'table_db' => 'user', 
							 'field_db' => 'firstbookpref',
							 'type_db' => 'enum', 
							 'value' => ''.$user['firstbookpref']);
	$User['NoLogin']=array('label' => 'nologin', 
						   //'table_db' => 'user', 
						   'field_db' => 'nologin',
						   'type_db' => 'flag', 
						   'value' => ''.$user['nologin']);
	$User['JobTitle']=array('label' => 'jobtitle', 
							'table_db' => 'user', 
							'field_db' => 'jobtitle',
							'type_db' => 'varchar(240)', 
							'value' => ''.$user['jobtitle']);
	$User['HomePhone']=array('label' => 'homephone', 
							 'table_db' => 'user', 
							 'field_db' => 'homephone',
							 'type_db' => 'varchar(22)', 
							 'value' => ''.$user['homephone']);
	$User['MobilePhone']=array('label' => 'mobilephone', 
							 'table_db' => 'user', 
							 'field_db' => 'mobilephone',
							 'type_db' => 'varchar(22)', 
							 'value' => ''.$user['mobilephone']);
	$User['PersonalCode']=array('label' => 'code', 
								'table_db' => 'user', 
								'field_db' => 'personalcode',
								'type_db' => 'varchar(120)', 
								'value' => ''.$user['personalcode']);
	$User['DOB']=array('label' => 'dateofbirth', 
					   'table_db' => 'user', 
					   'field_db' => 'dob',
					   'type_db' => 'date', 
					   'value' => ''.$user['dob']);
	$User['ContractDate']=array('label' => 'contractdate', 
								'table_db' => 'user', 
								'field_db' => 'contractdate',
								'type_db' => 'date', 
								'value' => ''.$user['contractdate']);
	$User['Qualification']=array('label' => 'qualification', 
								 'table_db' => 'user', 
								 'field_db' => 'education',
								 'type_db' => 'varchar(240)', 
								 'value' => ''.$user['education']);
	$User['University']=array('label' => 'university', 
							  'table_db' => 'user', 
							  'field_db' => 'education2',
							  'type_db' => 'varchar(240)', 
							  'value' => ''.$user['education2']);
	if($user['epfusername']==''){
		/* If we can then set the epfusername now. */
		$user['epfusername']=new_epfusername($User,'staff');
		}
	$User['EPFUsername']=array('label' => 'epfusername', 
							   'field_db' => 'epfusername',
							   'type_db' => 'varchar(128)', 
							   'value' => ''.$user['epfusername']);


	if($user['address_id']>0){$addid=$user['address_id'];}
	else{$addid=-1;}
	$User['Address']=(array)fetchAddress(array('address_id'=>$addid,'addresstype'=>''));

	$d_ie=mysql_query("SELECT id,name,comment,othertype FROM categorydef WHERE type='inf' AND subtype='staff';");
	while($field=mysql_fetch_array($d_ie,MYSQL_ASSOC)){
		$d_v=mysql_query("SELECT value FROM info_extra WHERE catdef_id='".$field['id']."' AND user_id='$uid';");
		$value=mysql_result($d_v,0);
		$User['ExtraInfo'][$field['name']]=array('label' => $field['name'], 
							   'label_not_translate' => true, 
							   'field_db' => 'extra_'.$field['id'],
							   'table_db' => 'info_extra',
							   'type_db' => 'varchar(150)', 
							   'value' => ''.$value);
		}

	return $User;
	}

/**
 * Takes a user record and returns an xml array for it.
 *
 */
function fetchUser_short($user){
	$User=array();
	$User['id_db']=$user['uid'];
	$User['Surname']['value']=$user['surname'];
	if($user['title']!=''){
		$title=displayEnum($user['title'],'title');
		$User['Forename']['value']=get_string($title,'infobook');
		$User['Forename']['value']=$user['forename'];
		}
	else{
		$User['Forename']['value']=$user['forename'];
		}
	$User['EmailAddress']['value']=$user['email'];
	$User['Username']['value']=strtolower($user['username']);
	$User['EPFUsername']['value']=$user['epfusername'];
	$User['Password']['value']=$user['passwd'];

	return $User;
	}
?>
