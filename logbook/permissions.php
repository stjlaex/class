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

	/*first find pastoral group*/
  	$d_group=mysql_query("SELECT gid FROM groups JOIN student
	  	ON student.yeargroup_id=groups.yeargroup_id WHERE
	  	student.id='$sid' AND groups.course_id='';"); 
	$group=mysql_fetch_array($d_group);
	$gids[]=$group['gid'];

	/*academic groups for course and subject*/
	if($bid!='' and $bid!='%' and $bid!='General' and $bid!='G'){
	  	$d_class=mysql_query("SELECT course_id FROM class JOIN cidsid
		  	ON cidsid.class_id=class.id WHERE
		  	class.subject_id='$bid' AND cidsid.student_id='$sid'");
		$crid=mysql_result($d_class,0);
	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='$crid' AND subject_id='%'");
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];

	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='%' AND subject_id='$bid'"); 
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];

	  	$d_group=mysql_query("SELECT gid FROM groups WHERE
		  	course_id='$crid' AND subject_id='$bid'"); 
		$group=mysql_fetch_array($d_group);
		$gids[]=$group['gid'];
		}
	foreach($gids as $key => $gid){
		$d_users=mysql_query("SELECT * FROM users JOIN perms ON users.uid=perms.uid WHERE
			perms.gid='$gid' AND perms.e='1';");
		while($user=mysql_fetch_array($d_users)){
			if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
				$recipients[]=array('username'=>$user['username'], 'email'=>$user['email']);
				}
			}
		}

	/*checks for the form tutor*/
	$d_form=mysql_query("SELECT teacher_id FROM form JOIN student ON
		  	student.form_id=form.id WHERE student.id='$sid'"); 
	$formtid=mysql_result($d_form,0);
	$user=get_user($formtid);
	if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
		$recipients[]=array('username'=>$user['username'], 'email'=>$user['email']);
		}

	/*checks for boarders*/
	$Student=fetchStudent_singlefield($sid,'Boarder');
	if($Student['Boarder']['value']!='N'){
		$d_u=mysql_query("SELECT uid FROM perms JOIN groups ON perms.gid=groups.gid WHERE
					  groups.type='p' AND groups.name='residence';");
		while($u=mysql_fetch_array($d_u)){
			$user=get_user($u['uid'],'uid');
			if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
				$recipients[]=array('username'=>$user['username'], 'email'=>$user['email']);
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

	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$d_cids=mysql_query("SELECT DISTINCT id FROM class WHERE
		subject_id LIKE '$rbid' AND course_id LIKE '$rcrid' ORDER BY id");
		while($cid=mysql_fetch_row($d_cids)){
			$d_users=mysql_query("SELECT DISTINCT uid,
			   	username, passwd, forename, surname, email, emailuser,
				emailpasswd, nologin,
				firstbookpref, role, senrole, epfusername FROM users JOIN tidcid ON 
				users.username=tidcid.teacher_id WHERE
				tidcid.class_id='$cid[0]' AND users.nologin='0' ORDER BY username");
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
 * a head of year would be 111
 */
function list_pastoral_users($ryid,$perms){
   	$users=array();

	$r=$perms['r'];
	$w=$perms['w'];
	$x=$perms['x'];
	$d_g=mysql_query("SELECT gid FROM groups WHERE
			course_id='' AND subject_id='' AND yeargroup_id LIKE '$ryid' AND type='p';");
	if(mysql_num_rows($d_g)==0){
		mysql_query("INSERT INTO groups (name,type) VALUES ('$ryid','p');");
		}
	else{
		$gid=mysql_result($d_g,0);
		$d_users=mysql_query("SELECT DISTINCT users.uid,
			   	username, passwd, forename, surname, email, emailuser,
				emailpasswd, nologin,
				firstbookpref, role, senrole, epfusername FROM users JOIN perms ON 
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
function list_group_users_perms($gid){
   	$users_perms=array();
	$d_users=mysql_query("SELECT DISTINCT users.uid, users.username, 
				perms.r, perms.w, perms.x, perms.e FROM users JOIN perms ON 
				users.uid=perms.uid WHERE perms.gid='$gid';");
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
function get_group_perms($gid,$uid){
	$d_p=mysql_query("SELECT r, w, x, e FROM perms WHERE
								gid='$gid' AND uid='$uid';");
	if(mysql_num_rows($d_p)>0){
		$perms=mysql_fetch_array($d_u,MYSQL_ASSOC);
		}
	else{
		$perms=array('r'=>0,'w'=>0,'x'=>0,'e'=>0);
		}
	return $perms;
	}

/**
 * Singles out a special group of users with admin permissions
 * $type is b=budget, a=academic, p=pastoral, s=section, u=users.
 *
 * The return perm is either true or false.
 *
 */
function get_admin_perm($type,$uid){
	$d_p=mysql_query("SELECT r FROM perms JOIN groups ON perms.gid=groups.gid WHERE
					  perms.uid='$uid' AND groups.type='$type' AND groups.name='admin';");
	if(mysql_num_rows($d_p)>0){
		$perm=1;
		}
	else{
		$perm=0;
		}
	return $perm;
	}

/**
 *
 * Returns array of special groups with admin permissions identified
 * by group.name='admin'.
 *
 * If they haven't already been created in the db table then calling
 * this function will take care of that too.
 *
 */
function list_admin_groups(){
	$groups=array('a'=>array('name'=>'academic'),
				  'p'=>array('name'=>'pastoral'),
				  'u'=>array('name'=>'users'),
				  'b'=>array('name'=>'budgets')
				  );
	$d_g=mysql_query("SELECT gid,type FROM groups WHERE name='admin';");
	if(mysql_num_rows($d_g)>=sizeof($groups)){
		while($group=mysql_fetch_array($d_g,MYSQL_ASSOC)){
			if(array_key_exists($group['type'],$groups)){$groups[$group['type']]['gid']=$group['gid'];}
			}
		}
	else{
		/* Check each individual group and create if it doesn't exist. */
		foreach($groups as $type=>$name){
			$d_g=mysql_query("SELECT gid,type FROM groups WHERE name='admin' AND type='$type';");
			if(mysql_num_rows($d_g)==0){
				mysql_query("INSERT INTO groups (name,type) VALUES ('admin','$type');");
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
				surname, title, email, emailuser, emailpasswd, nologin, firstbookpref,
				role, worklevel, senrole, epfusername
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
	if($crid!='' or $bid!=''){
		/*Get ids for the teachers of this subject and store in tids[]*/
		$d_teacher=mysql_query("SELECT DISTINCT teacher_id FROM tidcid JOIN
				class ON class.id=tidcid.class_id WHERE class.subject_id LIKE '$bid' 
				AND class.course_id LIKE '$crid' ORDER BY teacher_id;");
		while($teacher=mysql_fetch_array($d_teacher,MYSQL_ASSOC)){
			$tid=$teacher['teacher_id'];
			$d_users=mysql_query("SELECT uid, username, passwd, forename,
					surname, title, email, emailuser, emailpasswd,
					nologin, firstbookpref, role, worklevel,
					senrole, epfusername FROM users WHERE
					username='$tid' AND nologin LIKE '$nologin';");
			$user=mysql_fetch_array($d_users,MYSQL_ASSOC);
			$users[$tid]=$user;
			}
		}
	else{
		/*Otherwise just return all active teaching staff ie. nologin=0*/
		$d_user=mysql_query("SELECT uid, username, passwd, forename,
				surname, title, email, emailuser, emailpasswd, 
				nologin, firstbookpref, role, worklevel, senrole, 
				epfusername FROM users WHERE
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
		//$error[]='';
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
 */
function getYearPerm($yid,$respons){
	/*return perm for yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']!='Form'){
			if($resp['yeargroup_id']==$yid){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
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
function getSENPerm($yid,$respons){
	/*return perm for sen in this yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$perm=getYearPerm($yid,$respons);
	if($_SESSION['senrole']=='1' or $_SESSION['role']=='sen'){$perm['r']=1;$perm['w']=1;}
	return $perm;
	}

/**
 * 
 */
function getMedicalPerm($yid,$respons){
	/*return perm for med in this yeargroup*/	
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$perm=getYearPerm($yid,$respons);
	if($_SESSION['role']=='medical'){$perm['r']=1;$perm['w']=1;}
	return $perm;
	}

/**
 * 
 */
function getFormPerm($fid,$respons){
	/*return perm for form group*/
	$perm['r']=0;
	$perm['w']=0;
	$perm['x']=0;
	$d_form=mysql_query("SELECT yeargroup_id FROM form WHERE id='$fid';");
	$formyid=mysql_result($d_form,0);
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']=='Form'){
			if($resp['form_id']==$fid){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
			}
		elseif($resp['yeargroup_id']!=''){
			if($resp['yeargroup_id']==$formyid){
				$perm['r']=$resp['r'];
				$perm['w']=$resp['w'];
				$perm['x']=$resp['x'];
				}
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
function getMarkPerm($mid, $respons){
	$d_class=mysql_query("SELECT subject_id, course_id FROM class
		 JOIN midcid ON class.id=midcid.class_id WHERE midcid.mark_id='$mid'");
	$class=mysql_fetch_array($d_class,MYSQL_ASSOC);
	/*this will only takes the first crid/bid, would be a prolem if
				the mark is defined  across more than one type of class*/
	$bid=$class['subject_id'];
	$crid=$class['course_id'];

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

	$d_crid=mysql_query("SELECT DISTINCT course_id 
							FROM cridbid WHERE subject_id='$bid'");
	if(sizeof($crids)>=mysql_num_rows($d_crid)){
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
 * Returns arrays of fids and yids ie. pastoral responisbilites for a 
 * given $respons array.
 *
 */
function list_pastoral_respon($respons){
	$rfids=array();
	$ryids=array();
	for($c=0;$c<sizeof($respons);$c++){
		$resp=$respons[$c];
		if($resp['name']=='Form'){
			if($resp['form_id']!=''){$rfids[]=$resp['form_id'];}
			}
		elseif($resp['yeargroup_id']!=''){
			/*academic respons must have null yeargroup_id for this to work!*/
			$ryids[]=$resp['yeargroup_id'];
			}
		}
	return array('forms'=>$rfids,'years'=>$ryids);
	}


/**
 * 
 */
function update_user($user,$update='no',$short='class'){
	global $CFG;
	$result='';
	/* Optional $update='yes' will amend an existing record.*/
	$username=$user['username'];
	$surname=checkEntry($user['surname']);
	$forename=$user['forename'];
	$title=$user['title'];
	$role=$user['role'];
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
	   if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $user['email'])){ 
			$user['email']=strtolower($user['email']);
			$email=$user['email'];
			if(isset($user['emailuser'])){$emailuser=$user['emailuser'];}
			else{$emailuser='';}
			/*endecrypt is the rc4 encryption function from moodlelib*/
			/*the same function (and webmailshare!) needs to be used
			/*by the webmail app to decrypt the passwd if its passed encrypted*/
			/*NB. changing webmailshare will invalidate any email
			/*passwds already stored in the class db*/
			$emailpasswd=endecrypt($CFG->webmailshare,$user['emailpasswd']);
			//$emailpasswd=$user['emailpasswd'];
	   		}
		}
	else{
		$email='';
		$emailuser='';
		$emailpasswd='';
		}
    if(isset($user['nologin'])){$nologin=$user['nologin'];}else{$nologin='0';}
    if(isset($user['senrole'])){$senrole=$user['senrole'];}else{$senrole='0';}

 	/*All users get a passwd based on the shortkeyword (set in $CFG) and a
   	/*userno, this formula should be personalised to meet your needs.*/
	/*The userno should be unique to each staff login, is not stored in*/
	/*the database and should be recorded elsewhere for reference if needed.*/
	if(isset($user['passwd']) and $user['passwd']!=''){$passwd=$user['passwd'];}
	elseif(isset($user['userno'])){$passwd=$short.$user['userno'];}
	if(isset($passwd)){$assword=md5($passwd);}
	else{$assword='';}

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
							email='$email', emailuser='$emailuser', emailpasswd='$emailpasswd', 
					role='$role', senrole='$senrole', worklevel='$worklevel', nologin='$nologin',
					firstbookpref='$firstbookpref' WHERE username='$username'");
			$result=$result.'Updated details for user '.$username;
			}
		}
	else{
		mysql_query("INSERT INTO users (username, passwd, forename,
							surname, title,
					email, emailuser, emailpasswd, role, nologin, worklevel,
							senrole, firstbookpref) 
					VALUES ('$username', '$assword', '$forename',
					 '$surname', '$title', '$email', '$emailuser', 
						'$emailpasswd', '$role', '$nologin', '$worklevel',
					   '$senrole', '$firstbookpref')");
		$result=$result.'Username '.$username.' added.';
		}

	if($assword!=''){
		  $d_user=mysql_query("UPDATE users SET
					passwd='$assword' WHERE username='$username'");
		  if($CFG->emailoff=='no' and $user['userno']!=''){
			  if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $email)){
				  //$headers=emailHeader();
				  $footer='--'. "\r\n" .get_string('emailfooterdisclaimer');
				  $message=get_string('emailnewloginuserno','admin')."\r\n";
				  $message=$message ."\r\n".get_string('username').': '.$username."\r\n";
				  $message=$message .get_string('keynumber','admin').': '.$user['userno']."\r\n";
				  $message=$message ."\r\n".$footer;
				  $subject=get_string('emailnewloginsubject','admin');
				  $fromaddress='ClaSS';
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
	$e=$newperms['e'];

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
 * @param string $case ?
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

    for($i=0; $i<=255; $i++) {
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

?>
