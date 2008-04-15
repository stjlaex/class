<?php
/**											lib/eportfolio_functions.php
 *
 * Currently only appropriate to integration with elgg.
 *
 * The eportfolio_db prefix is set to separate different schools into
 * separate eportfolio databases. The clientid can also be used as a
 * school identify where different schools are sharing the same
 * eportfolio database - some communities will then be inter-school,
 * primarily for staff, while others for pastoral and teaching groups
 * will be prefixed by the clientid and remain intra-school only.
 */


/**
 * The purpose here is to refresh (or rather clear out) all of the
 * existing 'relationships' between users and communities in the
 * database ready for them to be re-populated (refreshed) say at the
 * start of the year. So, it empties completely the friends and
 * group_membership tables, deletes all community users from the users
 * table and all of their associated content (blogs, files all get
 * wiped!). All users' accounts and their content are left intact.
 */
function elgg_refresh(){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
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
		//trigger_error($no.': '.$oldcom['username'],E_USER_WARNING);
		}

	}


/**
 * This blanks all users from the elgg database of a particular role,
 * identified by their default template name. NB. This does not blank
 * their epfusernames in the ClaSS db, this needs to be done seperatly.
 * 
 */
function elgg_blank($usertemplate){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	$table_users=$CFG->eportfolio_db_prefix.'users';
	$blanktables=array('friends'=>'owner',
					   'friends'=>'friend',
					   'groups'=>'owner',
					   'file_folders'=>'owner',
					   'files'=>'files_owner',
					   'group_membership'=>'user_id',
					   'user_flags'=>'user_id',
					   'pages'=>'owner',
					   'profile_data'=>'owner',
					   'tags'=>'owner',
					   'weblog'=>'weblog'
					   //'weblog_homework'=>'owner',
					   //'weblog_comments'=>'owner',
					   //'weblog_watchlist'=>'owner',
					   );
	while(list($table,$field)=each($blanktables)){
		$table=$CFG->eportfolio_db_prefix.$table;
		mysql_query("DELETE FROM $table JOIN $table_users ON
						$table_users.ident=$table.$field 
						WHERE template_name='$usertemplate';");
		}

	mysql_query("DELETE FROM $table_users WHERE template_name='$usertemplate';");

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}


/**
 * Generates a new user account in elgg for hte Newuser xml-array, 
 * properties are decided by one of three possible roles: 
 * staff, student or guardian.
 */
function elgg_newUser($Newuser,$role){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	/*only use first part of a name*/
	$surname=str_replace(' ','',$Newuser['Surname']['value']);
	$surname=str_replace("'",'',$surname);
	$surname=str_replace('-','',$surname);
    $name=$Newuser['Forename']['value'].' '.$Newuser['Surname']['value'];
	$active='yes';
	setlocale(LC_CTYPE,'en_GB');

	$nums='';
	$code='';
	if($role=='student'){
		$email='';
		//$email=$Newuser['EmailAddress']['value'];
		$dob=(array)split('-',$Newuser['DOB']['value']);
		$forename=(array)split(' ',$Newuser['Forename']['value']);
		//$start=iconv('UTF-8', 'ASCII//TRANSLIT', $forename[0]);
		$start=utf8_to_ascii($forename[0]);
		$epfusertype='person';
		$epftemplate_name='Default_Student';
		$epftemplate=2;
		/* This takes the first letter of the surname and day,
	  	   month, year of dob to be password. */
		//$passwstart=iconv('UTF-8', 'ASCII//TRANSLIT', $surname);
		$passwstart=utf8_to_ascii($surname);
		$password=good_strtolower($passwstart[0]). $dob[2].$dob[1].$dob[0];
		$assword=md5($password);
		$classtable='info';
		$classfield='student_id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<2){$code.=array_rand($nums);}
		$tail=$code;
		$no=0;
		}
	elseif($role=='guardian'){
		$email='';
		//$email=$Newuser['EmailAddress']['value'];
		//$start=iconv('UTF-8', 'ASCII//TRANSLIT', $surname);
		$start=utf8_to_ascii($surname);
		$name=$Newuser['Title']['value'].' '.$Newuser['Surname']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Guardian';
		$epftemplate=3;
		$password=good_strtolower($Newuser['firstchild']);
		$assword=md5($password);
		$classtable='guardian';
		$classfield='id';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<2){$code.=array_rand($nums);}
		$tail=$code;
		$no=0;
		}
	elseif($role=='staff'){
		$email=$Newuser['EmailAddress']['value'];
		/* Staff usernames are unique within their own ClaSS but need
			to maintain that within theBox by adding the school's clientid.*/
		if(isset($CFG->clientid)){$start=$CFG->clientid;}
		else{$start='';}
		$tail=$Newuser['Username']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Staff';
		$epftemplate=1;
		$assword=$Newuser['Password']['value'];
		$no='';/*Not needed - all staff usernames are already unique.*/
		$classtable='users';
		$classfield='uid';
		}
	$epfusername=good_strtolower($start. $tail);
	$epfusername=str_replace("'",'',$epfusername);
	$epfusername=clean_text($epfusername);


	/* Iterate over $no until we get a unique username. */
	$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no';");
	while($olduser=mysql_fetch_array($d_user)){
		$no++;
		$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no';");
		}

	//trigger_error($epfusername. $no.' '.$password,E_USER_WARNING);

	mysql_query("INSERT INTO $table (username, password, name, 
					email, active, user_type,icon,template_id,template_name) VALUES 
					('$epfusername$no', '$assword', '$name',
					'$email', '$active','$epfusertype','$epftemplate',
					'$epftemplate','$epftemplate_name')");
	$epfuid=mysql_insert_id();
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	if(isset($Newuser['id_db'])){
		$classid=$Newuser['id_db'];
		mysql_query("UPDATE $classtable SET epfusername='$epfusername$no'
					WHERE $classfield='$classid';");
		}
	return $epfuid;
	}


/**
 *
 * Checks for a community and either updates or creates
 * expects an array with at least type and name set.
 */
function elgg_update_community($community,$communityfresh=array('type'=>'','name'=>''),$epfuidowner=''){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';
	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if(isset($community['type']) and isset($community['name']) 
	   and $community['type']!='' and $community['name']!=''){
		$type=$community['type'];
		$name=$community['name'];
		$typefresh=$communityfresh['type'];
		$namefresh=$communityfresh['name'];
		/*The portfolio communities want the real name not the yid etc.*/
		if(isset($community['displayname'])){$epfname=$community['displayname'];}
		else{$epfname=$name;}
		/* Make sure username is still unique across all user types.*/
		$epfusername=str_replace(' ','',$epfname);
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
			mysql_query("INSERT INTO $table (username, name, 
				    active, moderation, owner, user_type,icon,template_id,template_name) 
					VALUES ('$epfusername', '$epfname',
					'yes', 'yes', '1', 'community','$epftemplate', 
					'$epftemplate','$epftemplate_name')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				/*TODO update all references to epfusername, if
					allowed by elgg?*/
			 /*all communities are named according to their school and type*/
				$epfusername=$school. $typefresh . $namefresh;
				mysql_query("UPDATE $table SET username='$epfusername'
						   WHERE ident='$comid'");
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
		$dbepf=db_connect($CFG->eportfolio_db);
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
function elgg_update_group($group,$groupfresh=array('owner'=>'','name'=>'','access'=>'')){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'groups';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$name=$group['name'];
	$owner=$group['owner'];
	if(isset($group['access'])){$access=$group['access'];}
	else{$access='';}
	$namefresh=$groupfresh['name'];
	if($owner!='' and $name!=''){
		$d_group=mysql_query("SELECT ident FROM $table WHERE
				owner='$owner' AND name='$name'");
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
						   WHERE ident='$epfgroupid'");
				}
			}
		}
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	return $epfgroupid;
	}

/**
 *
 */
function elgg_join_group($epfuid,$group){
	global $CFG;
	$table_group=$CFG->eportfolio_db_prefix.'groups';
	$table_member=$CFG->eportfolio_db_prefix.'group_membership';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
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
 */
function elgg_new_folder($owner,$name,$access){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'file_folders';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	if($owner!='' and $name!='' and $access!=''){
		mysql_query("INSERT INTO $table SET owner='$owner', files_owner='$owner',
							name='$name', access='$access',
							parent='-1', handler='class'");
		}

	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}

/** 
 * Returns the epfuid - usually called from other elgg_ functions but
 * set dbc=true if its to be called elsewhere.
 * The owner is the epfusername and type is the elgg user_type 
 * currently only recognised as either 'person' or 'community'.
 */
function elgg_get_epfuid($owner,$type,$dbc=false){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';

	if($CFG->eportfolio_db!='' and $dbc==true){
		$dbepf=db_connect($CFG->eportfolio_db);
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

function elgg_new_homework($tid,$cid,$bid,$pid,$title,$body,$dateset){
	$dbepf='';
	list($year,$month,$day)=explode('-',$dateset);
	$posted=mktime(0,0,0,$month,$day,$year);
	global $CFG;
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	if(isset($CFG->clientid)){$school=$CFG->clientid;}
	else{$school='';}

	$epfcid=str_replace('/','',$cid);
	$epfcid=str_replace('-','',$epfcid);
	$epfblogname=$school. 'class'. $epfcid;
	$epfuidweblog=elgg_get_epfuid($epfblogname,$type='community');
	$epfuidowner=elgg_get_epfuid($school. $tid,$type='person');
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

?>