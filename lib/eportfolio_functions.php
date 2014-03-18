<?php
/**											lib/eportfolio_functions.php
 *
 * Currently only appropriate to integration with elgg.
 *
 * The eportfolio_db prefix is set to separate different schools into
 * separate eportfolio databases. The clientid can also be used as a
 * school identifier where different schools are sharing the same
 * eportfolio database - some communities will then be inter-school,
 * primarily for staff, while others for pastoral and teaching groups
 * will be prefixed by the clientid and remain intra-school only.
 */


/**
 *
 * The purpose here is to refresh (or rather clear out) all of the
 * existing 'relationships' between users and communities in the
 * database ready for them to be re-populated (refreshed) say at the
 * start of the year. So, it empties completely the friends and
 * group_membership tables, deletes all community users from the users
 * table and all of their associated content (blogs, files all get
 * wiped!). All users' accounts and their content are left intact.
 *
 */
function elgg_refresh(){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	if(isset($CFG->clientid) and $CFG->clientid!=''){$school=$CFG->clientid;}
	else{$school='all';}

	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_friends=$CFG->eportfolio_db_prefix.'friends';
	$table_groups=$CFG->eportfolio_db_prefix.'groups';
	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_files=$CFG->eportfolio_db_prefix.'files';
	$table_members=$CFG->eportfolio_db_prefix.'group_membership';
	$table_flags=$CFG->eportfolio_db_prefix.'user_flags';
	$table_pages=$CFG->eportfolio_db_prefix.'pages';
	$table_profile=$CFG->eportfolio_db_prefix.'profile_data';
	$table_tags=$CFG->eportfolio_db_prefix.'tags';
	$table_weblog=$CFG->eportfolio_db_prefix.'weblog';
	$table_hw=$CFG->eportfolio_db_prefix.'weblog_homework';
	$table_comments=$CFG->eportfolio_db_prefix.'weblog_comments';
	$table_watchlist=$CFG->eportfolio_db_prefix.'weblog_watchlist';

	$no=0;
	/*This will list all community users for this school*/
	$d_users=mysql_query("SELECT ident,username FROM $table_users 
							WHERE ident!='1' AND username LIKE
							'$school%' AND user_type='community';");
	while($oldcom=mysql_fetch_array($d_users)){
		$no++;
		$ident=$oldcom['ident'];
				mysql_query("DELETE FROM $table_users WHERE ident='$ident';");
		mysql_query("DELETE FROM $table_friends WHERE
								friend='$ident' OR owner='$ident';");
		mysql_query("DELETE FROM $table_folders WHERE owner='$ident';");
		mysql_query("DELETE FROM $table_files WHERE files_owner='$ident';");
		mysql_query("DELETE FROM $table_pages WHERE owner='$ident';");
		mysql_query("DELETE FROM $table_profile WHERE owner='$ident';");
		mysql_query("DELETE FROM $table_tags WHERE owner='$ident';");
		mysql_query("DELETE FROM $table_groups WHERE owner='$ident';");
		mysql_query("DELETE FROM $table_members WHERE user_id='$ident';");
		mysql_query("DELETE FROM $table_flags WHERE user_id='$ident';");
	 		mysql_query("DELETE FROM $table_weblog WHERE weblog='$ident';");
		mysql_query("DELETE FROM $table_hw JOIN
					$table_weblog.ident=$table_hw.weblog_post 
					WHERE $table_weblog.weblog='$ident';");
		mysql_query("DELETE FROM $table_comments JOIN
					$table_weblog.ident=$table_comments.post_id 
					WHERE $table_weblog.weblog='$ident';");
		mysql_query("DELETE FROM $table_watchlist JOIN
					$table_weblog.ident=$table_watchlist.weblog_post
					WHERE $table_weblog.weblog='$ident';");
		}
	}


/**
 *
 * This blanks all users from the elgg database of a particular role,
 * identified by their default template name. NB. This does not blank
 * their epfusernames in the ClaSS db, this needs to be done seperately.
 * 
 */
function elgg_blank($usertemplate){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$blanktables=array('friends'=>'owner',
					   'groups'=>'owner',
					   'file_folders'=>'owner',
					   'files'=>'files_owner',
					   'group_membership'=>'user_id',
					   'user_flags'=>'user_id',
					   'pages'=>'owner',
					   'profile_data'=>'owner',
					   'tags'=>'owner',
					   'weblog'=>'weblog',
					   'weblog_homework'=>'owner',
					   'weblog_comments'=>'owner',
					   'weblog_watchlist'=>'owner'
					   );
	while(list($table,$field)=each($blanktables)){
		$table=$CFG->eportfolio_db_prefix.$table;
		mysql_query("DELETE FROM $table JOIN $table_users ON
						$table_users.ident=$table.$field 
						WHERE template_name='$usertemplate';");
		}

	$table=$CFG->eportfolio_db_prefix.'friends';
	mysql_query("DELETE FROM $table JOIN $table_users ON
						$table_users.ident=$table.friend 
						WHERE template_name='$usertemplate';");

	mysql_query("DELETE FROM $table_users WHERE template_name='$usertemplate';");

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}



/**
 * Generates a new user account in elgg for the User xml-array.
 * Properties are decided by one of three possible roles: staff,
 * student or guardian. Returns the new epfuid for the User or -1 if
 * it fails.
 *
 */
function elgg_newUser($User,$role){

	$epfuid=-1;
	global $CFG;

	$table=$CFG->eportfolio_db_prefix.'users';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	/* Only use first part of a name. */
	$name=$User['Forename']['value'].' '.$User['Surname']['value'];
	$active='yes';
	setlocale(LC_CTYPE,'en_GB');

	$epfusername=$User['EPFUsername']['value'];
	$surname=str_replace(' ','',$User['Surname']['value']);
	$surname=str_replace("'",'',$surname);
	$surname=str_replace('-','',$surname);

	if($role=='student'){
		$email=$User['EmailAddress']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Student';
		$epftemplate=2;
		$dob=(array)explode('-',$User['DOB']['value']);
		$passwstart=utf8_to_ascii($surname);
		$password=good_strtolower($passwstart[0]).$dob[0];
		$assword0=md5($password);
		}
	elseif($role=='guardian'){
		$email=$User['EmailAddress']['value'];
		$name=$User['Title']['value'].' '.$User['Surname']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Guardian';
		$epftemplate=3;
		$assword0=$User['Password']['value'];
		}
	elseif($role=='staff'){
		$email=$User['EmailAddress']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Staff';
		$epftemplate=1;
		$assword0=$User['Password']['value'];
		}

	if(isset($dbepf)){
		/* Doublecheck its a unique username and reject if not. */
		$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername';");
		if(mysql_num_rows($d_user)>0){
			trigger_error('EPFUsername duplicate: '.$epfusername.' already exists.',E_USER_WARNING);
			}
		else{
			$name=str_replace("'",'',$name);
			mysql_query("INSERT INTO $table (username, password, name, 
					email, active, user_type,icon,template_id,template_name) VALUES 
					('$epfusername', '$assword0', '$name',
					'$email', '$active','$epfusertype','-$epftemplate',
					'$epftemplate','$epftemplate_name');");
			$epfuid=mysql_insert_id();
			}

		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $epfuid;
	}



/**
 * Updates and existingw user account in elgg for the User xml-array.
 * Returns the epfuid for the User or -1 if it fails.
 *
 */
function elgg_updateUser($epfuid,$User,$role='guardian'){

	global $CFG;

	$table=$CFG->eportfolio_db_prefix.'users';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$active='yes';
	setlocale(LC_CTYPE,'en_GB');

	$epfusername=$User['EPFUsername']['value'];

	if($role=='student'){
		$email=$User['EmailAddress']['value'];
		$name=$User['Forename']['value'].' '.$User['Surname']['value'];
		}
	elseif($role=='guardian'){
		$email=$User['EmailAddress']['value'];
		$name=$User['Title']['value'].' '.$User['Surname']['value'];
		$pwdno=$User['Password']['index'];
		$assword=$User['Password']['value'];
		}
	elseif($role=='staff'){
		$email=$User['EmailAddress']['value'];
		}

	if(isset($dbepf)){
		mysql_query("UPDATE $table SET email='$email', name='$name'
					WHERE ident='$epfuid' AND username='$epfusername';");
		if(isset($assword) and $pwdno==0){
			mysql_query("UPDATE $table SET password='$assword'
					WHERE ident='$epfuid' AND username='$epfusername';");
			}
		elseif(isset($assword) and $pwdno<4){
			mysql_query("UPDATE $table SET password$pwdno='$assword'
					WHERE ident='$epfuid' AND username='$epfusername';");
			}

		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $epfuid;
	}


/*
 * Creates an html image tag for a document which can be displayed in Classic
 */
function epf_photo_display($file){
	global $CFG;
	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}
	$filedisplay_url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/epf_file_display.php';

	if(!isset($file['id']) or $file['id']==''){$fileid=$file['name'];}
	else{$fileid=$file['id'];}
	$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'&filename='.$file['name'];
	$path=$filedisplay_url.$fileparam_list;

	$img="<img src='".$filedisplay_url.$fileparam_list."'>";
	return $img;

	}


/*
 * Appends html to a comment in Classic
 */
function epf_append_to_comment($html,$epfusername,$commentid){

	$d_c=mysql_query("SELECT detail,subject_id FROM comments WHERE id='$commentid';");
	$comment=mysql_result($d_c,0,'detail');
	$subject_id=mysql_result($d_c,0,'subject_id');
	if($subject_id=="form"){$subject="Subject: form";}
	else{
		$d_s=mysql_query("SELECT name FROM subject WHERE id='$subject_id';");
		$subject="Subject: ".mysql_result($d_s,0,'name');
		}

	global $CFG;
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	$table=$CFG->eportfolio_db_prefix.'weblog_posts';

	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}

	$epfuid=elgg_get_epfuid($epfusername,'person');

	$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
	$epfgroupid=elgg_update_group($group,array('owner'=>'','name'=>'','access'=>''),false);
	$access='group'.$epfgroupid;

	$d_p=mysql_query("SELECT ident,body FROM $table WHERE weblog='$epfuid' AND access='$access' 
						AND body LIKE '<p>$comment</p>%' AND title='$subject' ORDER BY ident DESC;");
	$post_id=mysql_result($d_p,0,'ident');

	$body=mysql_result($d_p,0,'body');
	$newbody=addslashes($body).'<p>'.addslashes($html).'</p>';


	mysql_query("UPDATE $table SET body='$newbody' WHERE ident=$post_id;");

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}


/**
 *
 * Checks for a community and either updates or creates a new one.
 * Expects an array with at least type and name set, can optionally
 * have displayname as well.
 *
 */
function elgg_update_community($community,$communityfresh=array('type'=>'','name'=>''),$epfuidowner=''){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';
	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(isset($community['type']) and isset($community['name']) 
	   and $community['type']!='' and $community['name']!=''){
		$type=$community['type'];
		$name=$community['name'];
		/* Be careful when the yid (ie. name for a year com) is negative */
		if($type=='year' and $name<0){$name='0'.$name;}
		$typefresh=$communityfresh['type'];
		$namefresh=$communityfresh['name'];
		/*Use the displayname if set or fallback to the yid or fid etc.*/
		if(isset($community['displayname'])){$epffullname=$community['displayname'];}
		else{$epffullname=$name;}
		/* Make sure username is still unique across all user types.*/
		$epfusername=str_replace(' ','',$name);
		$epfusername=str_replace('-','',$epfusername);
		/*all communities are named according to their school and type*/
		$epfusername=$school. $type . $epfusername;
		$d_community=mysql_query("SELECT ident FROM $table WHERE username='$epfusername'");
		if(mysql_num_rows($d_community)==0){
			if($type=='class'){
				$epftemplate_name='Default_Class';
				$epftemplate=4;
				}
			elseif($type=='year'){
				$epftemplate_name='Default_Year';
				$epftemplate=5;
				}
			elseif($type=='form'){
				$epftemplate_name='Default_Form';
				$epftemplate=6;
				}
			else{
				$epftemplate_name='Default_Template';
				$epftemplate=1;
				}
			mysql_query("INSERT INTO $table (username, name, 
				    active, moderation, owner,
					user_type,icon,icon_quota, template_id,template_name) 
					VALUES ('$epfusername', '$epffullname',
					'yes', 'yes', '1', 'community','-$epftemplate','1',
					'$epftemplate','$epftemplate_name')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				/*TODO update all references to epfusername, if
					allowed by elgg?*/
				//$epfusername=$school. $typefresh . $namefresh;
				//mysql_query("UPDATE $table SET name='$epfname'
				//		   WHERE ident='$comid'");
				}
			}
		}
	if(isset($comid) and $epfuidowner!=''){
		mysql_query("UPDATE $table SET owner='$epfuidowner' WHERE ident='$comid'");
		}
	if(!isset($comid)){$comid=-1;}

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");

	return $comid;
	}


/**
 *
 */
function elgg_join_community($epfuid,$community){
	global $CFG;
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_friends=$CFG->eportfolio_db_prefix.'friends';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(isset($community['type'])){$type=$community['type'];}else{$type='';}

	if(!isset($community['epfcomid'])){$community['epfcomid']='';}
	if($community['epfcomid']!=''){$epfcomid=$community['epfcomid'];}
	else{$epfcomid=elgg_update_community($community);}
	mysql_query("INSERT INTO $table_friends SET owner='$epfuid',
							friend='$epfcomid', status='perm'");
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}


/**
 *
 */
function elgg_fix_homework($epfuid,$epfcomid){
	global $CFG;
	$table_weblog=$CFG->eportfolio_db_prefix.'weblog_posts';
	$table_homework=$CFG->eportfolio_db_prefix.'weblog_homework';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$time=time()-(80*24*60*60);
	mysql_query("INSERT INTO $table_homework (owner,weblog_post) 
			SELECT '$epfuid',$table_weblog.ident
			FROM $table_weblog WHERE $table_weblog.weblog='$epfcomid' 
			AND $table_weblog.posted>'$time';");

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");

	}

/**
 *
 */
function elgg_update_group($group,$groupfresh=array('owner'=>'','name'=>'','access'=>''),$dbc=true){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'groups';

	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$name=$group['name'];
	$owner=$group['owner'];
	if(isset($group['access'])){$access=$group['access'];}
	else{$access='';}
	$namefresh=$groupfresh['name'];
	if($owner!='' and $name!=''){
		$d_group=mysql_query("SELECT ident FROM $table WHERE
				owner='$owner' AND name='$name';");
		if(mysql_num_rows($d_group)==0){
			mysql_query("INSERT INTO $table (owner, name, access) 
					VALUES ('$owner', '$name','$access');");
			$epfgroupid=mysql_insert_id();
			}
		else{
			$epfgroupid=mysql_result($d_group,0);
			if($namefresh!=''){
				/*TODO update all references to epfusername, if
					allowed by elgg?*/
				mysql_query("UPDATE $table SET name='$namefresh', 
						   WHERE ident='$epfgroupid';");
				}
			}
		}

	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $epfgroupid;
	}

/**
 *
 */
function elgg_join_group($epfuid,$group){
	global $CFG;
	$table_group=$CFG->eportfolio_db_prefix.'groups';
	$table_member=$CFG->eportfolio_db_prefix.'group_membership';

	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(!isset($group['epfgroupid'])){$group['epfgroupid']='';}
	if($group['epfgroupid']!=''){$epfgroupid=$group['epfgroupid'];}
	else{
		$name=$group['name'];
		$owner=$group['owner'];
		$d_elgg=mysql_query("SELECT ident FROM $table_group WHERE
						name='$name' AND owner='$owner'");
		if(mysql_num_rows($d_elgg)>0){$epfgroupid=mysql_result($d_elgg,0);}
		//else{trigger_error($epfuid.' '.$name.' '.$owner,E_USER_WARNING);}
		}
	if(isset($epfgroupid)){
		mysql_query("INSERT INTO $table_member SET user_id='$epfuid',
							group_id='$epfgroupid'");
		}

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");

	}

/**
 *
 * If the folder already exists then just returns the folder_id
 *
 * If folder doesn't exist and the @access is set then creates new
 * folder and returns its folder_id.
 *
 * This can only create folders in the user's root folder because
 * parent=-1 always.
 *
 *
 *
 */
function elgg_new_folder($owner,$name,$access,$dbc=true){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'file_folders';

	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$d_folder=mysql_query("SELECT ident FROM $table WHERE owner='$owner' AND name='$name';");
	if(mysql_num_rows($d_folder)>0){
		$folder_id=mysql_result($d_folder,0);
		}
	elseif($owner!='' and $name!='' and $access!=''){
		$d_f=mysql_query("INSERT INTO $table SET owner='$owner', files_owner='$owner',
					 name='$name', access='$access', parent='-1';");
		$folder_id=mysql_insert_id();
		}
	else{
		$folder_id=-1;
		}

	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $folder_id;
	}



/** 
 *
 * Returns the epfuid - usually called from other elgg_ functions but
 * set dbc=true if its to be called elsewhere.
 * The owner is the epfusername and type is the elgg user_type 
 * currently only recognised as either 'person' or 'community'.
 *
 */
function elgg_get_epfuid($owner,$type,$dbc=false){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';

	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$d_u=mysql_query("SELECT ident FROM $table WHERE username='$owner'
									AND user_type='$type';");
	if(mysql_num_rows($d_u)==1){
		$uid=mysql_result($d_u,0);
		}
	else{
		$uid=-1;
		}

	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $uid;
	}

/** 
 *
 * Returns an array of file urls and descriptions for the given $filetype and $owner.
 * If not called from other elgg_ functions set dbc=true.
 * The owner is the epfusername.
 *
 * @params string $epfusername of the owner
 * @params string $filetype
 * @params logical $dbc
 * @return array $files
 */
function elgg_list_files($epfun,$filetype,$dbc=false){
	global $CFG;
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_icons=$CFG->eportfolio_db_prefix.'icons';
	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_files=$CFG->eportfolio_db_prefix.'files';
	$files=array();

	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$epfuid=elgg_get_epfuid($epfun,'person');

	if($filetype=='icon'){
		/* TODO: make all icons (ie.photos), with exception of current
		 *		 icon, part of normal file structure??? 
		 */
		/*
		$d_u=mysql_query("SELECT icon FROM $userstable WHERE username='$owner';");
		if(mysql_num_rows($d_u)==1){
			$iconid=mysql_result($d_u,0);
			$fileurl=$CFG->eportfoliosite.'/_icon/user/'.$iconid.'/h/135/w/100';
			}
		*/
		}
	elseif($filetype=='work'){
		$folder_name='Portfolio Work';
		}
	elseif($filetype=='report'){
		$folder_name='Reports';
		}
	else{
		/* Just defaults to their parent folder. */
		$folder_name='root';
		$folder_id=-1;
		}

	if($epfuid>0){
		if($folder_name!='root'){
			$folder_id=elgg_new_folder($epfuid,$folder_name,'',false);
			}
		$d_f=mysql_query("SELECT ident, title, description, location, originalname FROM $table_files 
						WHERE files_owner='$epfuid' AND folder='$folder_id' ORDER BY time_uploaded DESC;");
		while($file=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			$file['name']=$file['originalname'];
			$file['path']=$CFG->eportfolio_dataroot.'/'.$file['location'];
			$file['url']=$CFG->eportfoliosite.'/'.$epfun.'/files/'.$folder_id.'/'.$file['ident'].'/'.$file['originalname'];
			$files[]=$file;
			}
		}

	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $files;
	}

/**
 *
 */
function elgg_new_homework($tid,$classname,$bid,$pid,$title,$body,$dateset){

	list($year,$month,$day)=explode('-',$dateset);
	$posted=mktime(0,0,0,$month,$day,$year);
	global $CFG;

	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}

	$epfcid=str_replace('/','',$classname);
	$epfcid=str_replace('-','',$epfcid);
	$epfblogname=$school. 'class'. $epfcid;
	$epfuidweblog=elgg_get_epfuid($epfblogname,'community');
	$epfuidowner=elgg_get_epfuid($school. $tid,'person');
	/*Homework access is restricted to the class its set for.*/
	$access='community'.$epfuidweblog;

	if($epfuidowner!='' and $title!='' and $body!=''){
		$table=$CFG->eportfolio_db_prefix.'weblog_posts';
		mysql_query("INSERT INTO $table SET owner='$epfuidowner',weblog='$epfuidweblog',
			   	posted='$posted',title='$title',body='$body',access='$access';");
		$epfuidpost=mysql_insert_id();


		$table=$CFG->eportfolio_db_prefix.'tags';
		if($pid!=''){
			mysql_query("INSERT INTO $table SET tag='$pid',tagtype='weblog',
			   	ref='$epfuidpost',owner='$epfuidowner',access='$access';");
			}
		mysql_query("INSERT INTO $table SET tag='$bid',tagtype='weblog',
			   	ref='$epfuidpost',owner='$epfuidowner',access='$access';");
		mysql_query("INSERT INTO $table SET tag='homework',tagtype='weblog',
			   	ref='$epfuidpost',owner='$epfuidowner',access='$access';");

		$table=$CFG->eportfolio_db_prefix.'friends';
		$d_f=mysql_query("SELECT owner FROM $table WHERE friend='$epfuidweblog';");
		while($friend=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			$epfuidmember=$friend['owner'];
			$table=$CFG->eportfolio_db_prefix.'weblog_homework';
			mysql_query("INSERT INTO $table SET 
							owner='$epfuidmember',weblog_post='$epfuidpost';");
			}

		}

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");

	}





/**
 *
 */
function elgg_new_comment($epfu,$dateset,$message,$title,$tid){

	list($year,$month,$day)=explode('-',$dateset);
	$posted=mktime(0,0,0,$month,$day,$year);
	global $CFG;

	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}

	/* Identify the student - the comment is being posted to the personal weblog for this student. */
	$epfuid=elgg_get_epfuid($epfu,'person');
	$epfuidowner=elgg_get_epfuid($school. $tid,'person');

	/* This is the family access group */
	$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
	$epfgroupid=elgg_update_group($group,array('owner'=>'','name'=>'','access'=>''),false);
	$access='group'.$epfgroupid;

	if($epfuid!='' and $message!=''){
		/* Post the comment to the weblog. */
		$table=$CFG->eportfolio_db_prefix.'weblog_posts';
		mysql_query("INSERT INTO $table SET owner='$epfuidowner',weblog='$epfuid',
			   	posted='$posted',title='$title',body='$message',access='$access';");
		$epfuidpost=mysql_insert_id();

		$recipients=array();
		$table=$CFG->eportfolio_db_prefix.'friends';
		$d_f=mysql_query("SELECT owner FROM $table WHERE friend='$epfuid';");
		while($friend=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			$epfuidmember=$friend['owner'];
			/* Update the watchlist for this parent. */
			$table=$CFG->eportfolio_db_prefix.'weblog_comment';
			mysql_query("INSERT INTO $table SET owner='$epfuidmember',weblog_post='$epfuidpost';");

			/* Notify the parent by email. */
			$recipient=array();
			$table=$CFG->eportfolio_db_prefix.'users';
			$d_u=mysql_query("SELECT name FROM $table WHERE ident='$epfuid';");
			$recipient['studentname']=mysql_result($d_u,0);
			$recipient['sid']=$epfuid;
			$d_u=mysql_query("SELECT email FROM $table WHERE ident='$epfuidmember';");
			$recipient['emailaddress']=trim(mysql_result($d_u,0));
			$recipient['gid']=$epfuidmember;
			$recipients[]=$recipient;
			}
		elgg_send_email($recipients,"comment");

		}

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");

	}


/**
 * Adds a message to classic's message_event table to be sent to parents
 *
 * @param $emailaddresses	array()
 * @param $emailtype		string (example: comment,report,homework)
 *
 */
function elgg_send_email($recipients,$emailtype,$template='classicemail'){
	global $CFG;
	$success=false;

	$sends=array();//use to avoid mulitple notification meassages to the same address
	foreach($recipients as $recipient){
		if($recipient['emailaddress']!='' and !in_array($recipient['emailaddress'],$sends)){
			$messagetxt=strip_tags(html_entity_decode($messagehtml, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
			$messagehtml.='<br><hr><p>'. $footer.'<p>';
			$emailaddress=strtolower($recipient['emailaddress']);
			/*Add template if exists for Classic email named classicemail*/
			$dbt=db_connect(true,'class');
			$templates=getTemplates('tmp',$template);
			if(count($templates)>0){
				$type['{{type}}']=$emailtype;
				$tags=getTags(true,'default',$uid=array('student_id'=>$sid,'guardian_id'=>$gid));
				$tags=array_merge($tags,$type);
				$messagehtml=getMessage($tags,'',$template);
				$messagetxt=strip_tags(html_entity_decode($messagehtml, ENT_QUOTES, 'UTF-8'));
				}

			/*Add email to message_event table*/
			$dbn=db_connect(false,$CFG->eportfolio_db);
			$table=$CFG->eportfolio_db_prefix.'message_event';
			if(send_email_to($emailaddress,'',$title,$messagetxt,$messagehtml,'','',$dbn,$table)){$success=true;}
			else{trigger_error('Couldn\'t send email to : '.$recipient['emailaddress'],E_USER_NOTICE);}
			}
		}
		return $success;
	}

/**
 *
 * Uploads files to a student's epf file space.  The file properties
 * are given by $file=array($name,$description,$title,$foldertype,$batchfiles)
 *
 * Will upload as many files as batchfiles identifies with epfusername
 * and filename, all with the same porperties defined by file.
 *
 * $foldertype is report or work or null for the root folder.
 *
 * @return true|false Returns true on success
 *
 * NB. We don't want to include these file sizes for the user quota when
 * they are posted by ClaSS.
 *
 */
function elgg_upload_files($filedata,$dbc=true){
	global $CFG;
	$success=false;

	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_files=$CFG->eportfolio_db_prefix.'files';
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_icons=$CFG->eportfolio_db_prefix.'icons';
	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$file_title=$filedata['title'];
	$file_description=$filedata['description'];
	$file_time=time();

	/* Identify the folder to be linked with this file. Note this is a
	 * virtual flolder in elgg and does not affect the physical directory
	 * the file is being stored in. 
	 */
	if($filedata['foldertype']=='report'){
		$folder_name='Reports';
		$dir_name='files';
		}
	elseif($filedata['foldertype']=='work'){
		$folder_name='Portfolio Work';
		$dir_name='files';
		}
	elseif($filedata['foldertype']=='icon'){
		$folder_name='root';
		$dir_name='icons';
		}
	else{
		/* Just defaults to their parent folder. */
		$folder_name='root';
		$dir_name='files';
		$folder_id=-1;
		}

	$batchfiles=$filedata['batchfiles'];
	foreach($batchfiles as $batchfile){
		$epfusername=$batchfile['epfusername'];
		$file_name=$batchfile['filename'];
		$epfuid=elgg_get_epfuid($epfusername,'person');

		/* This is the family access group */
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'Family','access'=>'');
		$epfgroupid=elgg_update_group($group,array('owner'=>'','name'=>'','access'=>''),false);
		$file_access='group'.$epfgroupid;
		if($folder_name!='root'){
			/* Create the virtual folder if it doesn't exist. */
			$folder_id=elgg_new_folder($epfuid,$folder_name,'group'.$epfgroupid,false);
			}

		$dir=$dir_name . '/' . substr($epfusername,0,1) . '/' . $epfusername; 
		/* Create the physical folder if it doesn't exist. */
		if(!make_portfolio_directory($dir)){
			trigger_error('Could not create eportfolio directory: '.$dir,E_USER_WARNING);
			}
		else{
			$file_fullpath=$CFG->eportfolio_dataroot . '/' . $dir. '/'. $file_name;
			$file_location=$dir . '/'. $file_name;
			$file_originalname=$file_name;
			if($filedata['foldertype']=='report'){
				$file_originalpath=$CFG->eportfolio_dataroot.'/cache/reports/'. $file_name;
				}
			elseif($filedata['foldertype']=='icon'){
				$file_originalpath=$CFG->eportfolio_dataroot.'/cache/images/'. $file_name;
				}
			else{
				$file_originalpath=$batchfile['tmpname'];
				}

			if($filedata['foldertype']=='icon'){

				mysql_query("INSERT INTO $table_icons SET owner='$epfuid',
					filename='$file_name', description='$file_description';");
				mysql_query("UPDATE $table_users SET icon=LAST_INSERT_ID() WHERE ident='$epfuid';");

				}
			else{
				$d_f=mysql_query("SELECT ident FROM $table_files WHERE originalname='$file_originalname' 
								AND files_owner='$epfuid';");
				if(mysql_num_rows($d_f)==0){
					$d_f=mysql_query("INSERT INTO $table_files 
		   			 (owner, files_owner, folder, title, originalname,
						description, location, access, time_uploaded) VALUES 
		   			 ('1', '$epfuid','$folder_id','$file_title','$file_originalname',
		   			  '$file_description','$file_location','$file_access','$file_time');");
					}
				else{
					$file_ident=mysql_result($d_f,0);
					$d_f=mysql_query("UPDATE $table_files SET (originalname='$file_originalname') 
		   				WHERE ident='$file_ident';");
					}
				}

			if($filedata['foldertype']!='icon'){
				if(rename($file_originalpath,$file_fullpath)){
					trigger_error('Uploaded file to: '.$dir,E_USER_NOTICE);
					// chmod($file_fullpath, $CFG->filepermissions);
					$success=true;

					/*Send an email to guardians if a report has been uploaded*/
					if($filedata['foldertype']=='report'){
						$recipients=array();
						$table=$CFG->eportfolio_db_prefix.'friends';
						$d_f=mysql_query("SELECT owner FROM $table WHERE friend='$epfuid';");
						while($friend=mysql_fetch_array($d_f,MYSQL_ASSOC)){
							/* Notify the parent by email. */
							$recipient=array();
							$epfuidmember=$friend['owner'];
							$table=$CFG->eportfolio_db_prefix.'users';
							$d_u=mysql_query("SELECT name FROM $table WHERE ident='$epfuid';");
							$recipient['studentname']=mysql_result($d_u,0);
							$recipient['sid']=$epfuid;
							$d_u=mysql_query("SELECT email FROM $table WHERE ident='$epfuidmember';");
							$recipient['emailaddress']=trim(mysql_result($d_u,0));
							$recipient['gid']=$epfuidmember;
							$recipients[]=$recipient;
							}
						if(!elgg_send_email($recipients,"report")){trigger_error('Couldn\'t send eportfolio email: '.$recipient['studentname'],E_USER_WARNING);}
						}
					}

				else{trigger_error('Could not move file to eportfolio: '.$file_fullpath,E_USER_WARNING);}
				}
			}

		}

	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $success;
	}



/**
 *
 *
 */
function elgg_delete_files($filedata,$dbc=true){
	global $CFG;
	$success=false;

	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_files=$CFG->eportfolio_db_prefix.'files';
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_icons=$CFG->eportfolio_db_prefix.'icons';
	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect(true,$CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}


	/* Identify the folder to be linked with this file. Note this is a
	 * virtual flolder in elgg and does not affect the physical directory
	 * the file is being stored in. 
	 */
	if($filedata['foldertype']=='report'){
		$folder_name='Reports';
		$dir_name='files';
		}
	elseif($filedata['foldertype']=='work'){
		$folder_name='Portfolio Work';
		$dir_name='files';
		}
	elseif($filedata['foldertype']=='icon'){
		$folder_name='root';
		$dir_name='icons';
		}
	else{
		/* Just defaults to their parent folder. */
		$folder_name='root';
		$dir_name='files';
		$folder_id=-1;
		}

	$batchfiles=$filedata['batchfiles'];
	foreach($batchfiles as $batchfile){
		$epfusername=$batchfile['epfusername'];
		$file_name=$batchfile['filename'];
		$epfuid=elgg_get_epfuid($epfusername,'person');
		$dir=$dir_name . '/' . substr($epfusername,0,1) . '/' . $epfusername; 
		$file_fullpath=$CFG->eportfolio_dataroot . '/' . $dir. '/'. $file_name;
		$file_location=$dir . '/'. $file_name;
		$file_originalname=$file_name;
		trigger_error($epfusername.' : '.$file_name,E_USER_WARNING);	

		if($filedata['foldertype']=='icon'){
			mysql_query("DELETE FROM $table_icons WHERE owner='$epfuid' AND filename='$file_name';");
			}
		else{
			$d_f=mysql_query("SELECT ident FROM $table_files WHERE originalname='$file_originalname' 
								AND files_owner='$epfuid';");
			if(mysql_num_rows($d_f)>0){
				$file_ident=mysql_result($d_f,0);
				$d_f=mysql_query("DELETE FROM $table_files WHERE ident='$file_ident';");
				}
			}
		
		if(unlink($file_fullpath)){
			$success=true;
			}
		else{trigger_error('Could not remove file from eportfolio: '.$file_fullpath,E_USER_WARNING);}
		}
	if($dbc==true){
		$db=db_connect();
		mysql_query("SET NAMES 'utf8'");
		}

	return $success;
	}



/**
 * Create a directory in the eportfolio_dataroot.
 *
 * @uses $CFG
 * @param string $directory  a string of directory names under
 * $CFG->dataroot eg stuff/assignment/1
 * param boolean $shownotices If true then notification messages will be printed out on error.
 * @return string|false Returns full path to directory if successful, false if not
 *
 */
function make_portfolio_directory($directory,$shownotices=false){
    global $CFG;
	/* File and directory permissions in the $CFG->eportfolio_dataroot */
	if(!isset($CFG->directorypermissions)){
		//$CFG->directorypermissions=0777;
		$CFG->directorypermissions=0755;
		}
	if(!isset($CFG->filepermissions)){
		//$CFG->filepermissions=0666;
		$CFG->filepermissions=0655;
		}

    $currdir=$CFG->eportfolio_dataroot;
    umask(0000);

    $dirarray=explode('/', $directory);

    /* Remove any trailing slash */
	$currdir=rtrim($currdir, '/');
    
    foreach($dirarray as $dir){
        $currdir=$currdir .'/'. $dir;
        if(!file_exists($currdir)){
            if(!mkdir($currdir,$CFG->directorypermissions)){
                if($shownotices){
                    trigger_error('ERROR: Could not find or create a directory ('. $currdir .')',E_USER_WARNING);
					}
                return false;
				}
            //@chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
			}
		}

    return $currdir;
	}



/**
 *
 * Uploads files to a student's epf file space.  The file properties
 * are given by $file=array($name,$description,$title,$foldertype,$batchfiles)
 *
 * Will upload as many files as batchfiles identifies with epfusername
 * and filename, all with the same porperties defined by file.
 *
 * $foldertype is report or work or null for the root folder.
 *
 * @return true|false Returns true on success
 *
 *
 */
function upload_files($filedata){
	global $CFG;
	$success=false;

	$file_title=$filedata['title'];
	$file_time=time();
	/* TODO: set these values... */
	$file_access='';
	$file_size=0;

	/* Identify the folder to be linked with this file. Note this is a
	 * virtual flolder does not affect the physical directory the file
	 * is stored in.
	 */
	if(isset($filedata['foldertype']) and $filedata['foldertype']=='icon'){
		$folder_name='root';
		$dir_name='icons';
		}
	elseif(isset($filedata['foldertype']) and $filedata['foldertype']=='staff'){
		$folder_usertype='u';
		$folder_name='staff';
		$dir_name='files';
		}
	else{
		/* Just defaults to their parent folder. */
		if(!isset($filedata['foldertype'])){
			$folder_name='root';
			$folder_id=-1;
			}
		else{$folder_name=$filedata['foldertype'];}
		$dir_name='files';
		}
	
	if(!isset($folder_usertype)){$folder_usertype='s';}

	$batchfiles=$filedata['batchfiles'];
	foreach($batchfiles as $batchfile){
		$epfusername=$batchfile['epfusername'];
		$file_name=$batchfile['filename'];
		$file_description=$batchfile['description'];
		$file_originalname=$batchfile['originalname'];
		$file_linkedid=0;
		$uid=get_epfuid($epfusername,$folder_usertype);

		if($folder_name!='root'){
			/* Create the virtual folder if it doesn't exist. */
			$folder_id=new_folder($uid,$folder_name);
			}

		$dir=$dir_name . '/' . substr($epfusername,0,1) . '/' . $epfusername; 
		/* Create the physical folder if it doesn't exist. */
		if(!make_portfolio_directory($dir)){
			trigger_error('Could not create eportfolio directory: '.$dir,E_USER_WARNING);
			}
		else{
			$file_fullpath=$CFG->eportfolio_dataroot . '/' . $dir. '/'. $file_name;
			$file_location=$dir . '/'. $file_name;
			$sql_linked='';

			if($filedata['foldertype']=='report'){
				$file_tmppath=$CFG->eportfolio_dataroot.'/cache/reports/'. $file_name;
				}
			elseif($filedata['foldertype']=='icon'){
				$file_tmppath=$CFG->eportfolio_dataroot.'/cache/images/'. $file_name;
				}
			else{
				$file_tmppath=$batchfile['tmpname'];
				if(isset($batchfile['linkedid']) and $batchfile['linkedid']>0){
					$file_linkedid=$batchfile['linkedid'];
		trigger_error($file_linkedid,E_USER_WARNING);
					}
				}

			if($filedata['foldertype']=='icon'){

				/* Keep a copy of the old icon */
				if(file_exists($file_fullpath)){
					$year=get_curriculumyear()-1;
					$file_old=$CFG->eportfolio_dataroot . '/'. $dir .'/'. $epfusername. '-'.$year. '.jpeg';
					rename($file_fullpath,$file_old);
					}

				/* No db record is required for icons. */

				}
			else{

				$sql_linked="AND other_id='$file_linkedid'";

				$d_f=mysql_query("SELECT id FROM file WHERE originalname='$file_originalname' 
											$sql_linked AND owner='$folder_usertype' AND owner_id='$uid';");
				if(mysql_num_rows($d_f)==0){
					$d_f=mysql_query("INSERT INTO file (owner, owner_id, folder_id, title, originalname,
										description, location, access, size, other_id) VALUES 
										('$folder_usertype', '$uid','$folder_id','$file_title','$file_originalname',
										'$file_description','$file_location','$file_access','$file_size','$file_linkedid');");
					}
				else{
					$file_id=mysql_result($d_f,0);
					$d_f=mysql_query("UPDATE file SET (originalname='$file_originalname', 
										title='$file_title', description='$file_description', other_id='$file_linkedid') WHERE id='$file_id';");
					}
				}

			if(rename($file_tmppath,$file_fullpath)){
				trigger_error('Uploaded file to: '.$dir,E_USER_NOTICE);
				// chmod($file_fullpath, $CFG->filepermissions);
				$success=true;
				}
			else{
				trigger_error('Could not move file to eportfolio: '.$file_fullpath,E_USER_WARNING);
				}
			}

		}


	return $success;
	}



/**
 *
 *
 */
function delete_file($filedata){

	$success=false;

	global $CFG;

	$file_id=$filedata['id'];
	
	if($filedata['context']=='icon'){
		//mysql_query("DELETE FROM $table_icons WHERE owner='$epfuid' AND filename='$file_name';");
		$owner=$filedata['owner'];
		$fname=$filedata['fname'];
		$file=$CFG->eportfolio_dataroot.'/icons/' . substr($owner,0,1) . '/' . $owner.'/'.$fname;
		if(unlink($file)) $success=true;
		}
	else{
		mysql_query("DELETE FROM file WHERE id='$file_id';");
		}

	$flocation=$filedata['flocation'];
	$d_f=mysql_query("SELECT * FROM file WHERE location='$flocation';");
	if(mysql_num_rows($d_f)==1){
		if(unlink($filedata['path'])){
			$success=true;
			}
		else{trigger_error('Could not remove file from eportfolio: '.$filedata['path'],E_USER_WARNING);}
		}
 

	return $success;
	}


/**
 *
 * If the folder already exists then just returns the folder_id
 *
 * If folder doesn't exist and the @access is set then creates new
 * folder and returns its folder_id.
 *
 * This can only create folders in the user's root folder because
 * parent=-1 always.
 *
 *
 *
 */
function new_folder($owner,$name,$access=''){

	if($name=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$d_folder=mysql_query("SELECT id FROM file_folder WHERE owner='$folder_usertype' AND owner_id='$owner' AND name='$name';");
	if(mysql_num_rows($d_folder)>0){
		$folder_id=mysql_result($d_folder,0);
		}
	elseif($owner!='' and $name!=''){
		$d_f=mysql_query("INSERT INTO file_folder SET  owner='$folder_usertype', owner_id='$owner',
					 name='$name', access='$access', parent_folder_id='-1';");
		$folder_id=mysql_insert_id();
		}
	else{
		$folder_id=-1;
		}

	return $folder_id;
	}


/** 
 *
 * Returns an array of file urls and descriptions for the given $filetype and $owner.
 * If not called from other elgg_ functions set dbc=true.
 * The owner is the epfusername.
 *
 * @params string $epfusername of the owner
 * @params string $filetype
 * @params string $linked_id
 *
 */
function list_files($epfun,$foldertype,$linkedid='-1',$bid=''){
	global $CFG;

	$files=array();

	if($foldertype=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$epfuid=get_epfuid($epfun,$folder_usertype);
	if(strlen($epfuid)<1){$epfuid='-999999';}

	if($foldertype=='icon'){
		/* Just involves listing the directory contents for icons. */
		$foldername='icons';
		$file_extensions=array('jpeg','jpg');
		$directory=$foldername.'/' . substr($epfun,0,1) . '/' . $epfun;
		foreach($file_extensions as $file_extension){
			$dir_files=(array)list_directory_files($CFG->eportfolio_dataroot.'/'.$directory,$file_extension);
			foreach($dir_files as $file){
				$files[]=array('id'=>'',
							   'description'=>$file,
							   'name'=>$file.'.'.$file_extension,
							   'originalname'=>$file.'.'.$file_extension,
							   'path'=>$CFG->eportfolio_dataroot.'/'.$directory.'/'.$file.'.'.$file_extension,
							   'location'=>$directory.'/'.$file.'.'.$file_extension);
				}
			}
		}
	else{
		/* Could be passing both an id and some description from a linked comment. */
		if(is_array($linkedid)){
			$linked_description=$linkedid['detail'];
			$linkedid=$linkedid['id'];
			}

		if($linkedid>0){
			/* Looking only at the files attached to a single entry. */
			$attachment="file.other_id='$linkedid' AND ";
			}
		else{
			/* Looking only at all files dropped in this context. */
			$attachment='';
			}

		$d_f=mysql_query("SELECT file.id, title, description, location, originalname, other_id FROM file 
						JOIN file_folder ON file_folder.id=file.folder_id
						WHERE $attachment file.owner_id='$epfuid' AND file.owner='$folder_usertype' 
						AND file_folder.name='$foldertype';");
		while($file=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			if($foldertype=='assessment'){
				/*
				 * The other_id is a comment_id and will have a descriptoin from there.
				 */
				$file['description']=$linked_description;
				}
			else{
				$file['description']=$file['description'];
				}
			$file['name']=$file['originalname'];
			$file['path']=$CFG->eportfolio_dataroot.'/'.$file['location'];
			$files[]=$file;
			}
		}

	return $files;
	}



/** 
 *
 *
 * @params string $file_id
 *
 */
function get_filedata($file_id){
	global $CFG;

	$d_f=mysql_query("SELECT file.id, file.title, file.description, file.location, file.originalname, file_folder.owner_id, file_folder.owner, 
						file.folder_id, file_folder.name AS foldertype FROM file 
						JOIN file_folder ON file_folder.id=file.folder_id WHERE file.id='$file_id';");
	$filedata=mysql_fetch_array($d_f,MYSQL_ASSOC);

	$filedata['path']=$CFG->eportfolio_dataroot.'/'.$filedata['location'];

	return $filedata;
	}


/** 
 * Associates a file to another resource identified by the linkedid.
 *
 * @params string $epfun of the owner
 * @params string $foldertype
 * @params string $linkedid
 */
function link_files($epfun,$foldertype,$linkedid){
	global $CFG;

	if($foldertype=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$epfuid=get_epfuid($epfun,$folder_usertype);

	$folder_id=new_folder($epfuid,$foldertype,$access='');

	mysql_query("UPDATE file SET other_id='$linkedid' 
						WHERE owner_id='$epfuid' AND owner='$folder_usertype' AND file.other_id='0';");

	}


/** 
 *
 * Returns the uid - sid, gid or uid
 * The owner is identified by their epfusername end the type is 's', 'g' or 'u'.
 *
 */
function get_epfuid($epfun,$user_type){

	if($user_type=='s'){
		/* student */
		$d_u=mysql_query("SELECT student_id FROM info WHERE epfusername='$epfun';");
		}
	elseif($user_type=='g'){
		/* guardian */
		$d_u=mysql_query("SELECT id FROM guardian WHERE epfusername='$epfun';");
		}
	elseif($user_type=='u'){
		/* user */
		$d_u=mysql_query("SELECT uid FROM users WHERE epfusername='$epfun';");
		}

	if(isset($d_u) and mysql_num_rows($d_u)==1){
		$uid=mysql_result($d_u,0);
		}
	elseif(substr($epfun,0,7)=='section'){
		/* special situation for a school section which is within the
		   context of staff and owner will be administrator */
		$uid=1;
		}
	else{
		$uid=-1;
		}

	return $uid;
	}

?>
