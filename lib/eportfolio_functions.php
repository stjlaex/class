<?php
/**											lib/eportfolio_functions.php
 *
 * Currently only appropriate to integration with elgg.
 *
 */

/*Not really do what it should yet!!!!!!!!!!!!!*/
function elgg_refresh(){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	if(isset($CFG->clientid) and $CFG->clientid!=''){$school=$CFG->clientid;}
	else{$school='';}

	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_friends=$CFG->eportfolio_db_prefix.'friends';
	$table_groups=$CFG->eportfolio_db_prefix.'groups';
	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_members=$CFG->eportfolio_db_prefix.'group_membership';
	if($school!=''){
		$d_users=mysql_query("SELECT * FROM $table_users 
							WHERE ident!='1' AND username LIKE '$school';");
		}
	else{
		mysql_query("DELETE FROM $table_users WHERE ident!='1';");
		mysql_query("DELETE FROM $table_groups;");
		mysql_query("DELETE FROM $table_members;");
		mysql_query("DELETE FROM $table_friends;");
		mysql_query("DELETE FROM $table_folders;");
		}
	}

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
	$forename=(array)split(' ',$Newuser['Forename']['value']);
    $name=$Newuser['Forename']['value'].' '.$Newuser['Surname']['value'];
	$active='yes';
	setlocale(LC_CTYPE,'en_GB');

	if($role=='student'){
		$email='';
		//$email=$Newuser['EmailAddress']['value'];
		$dob=(array)split('-',$Newuser['DOB']['value']);
		$start=iconv('UTF-8', 'ASCII//TRANSLIT', $forename[0]);
		$epfusertype='person';
		$epftemplate_name='Default_Student';
		$epftemplate=2;
		$password=good_strtolower('guest');
		/*this takes the first three letters of the surname and day,
		month, year of dob to be password*/
		//$password=substr($surname,0,2).$dob[2].$dob[1].$dob[0];
		//$password=good_strtolower($surname[0]). $dob[2].$dob[1].$dob[0];
		$assword=md5($password);
		$classtable='info';
		$classfield='student_id';
		$nums='';
		$code='';
		while(count($nums)<9){$nums[rand(1,9)]=null;}
		while(strlen($code)<2){$code.=array_rand($nums);}
		$tail=$code;
		$no=0;
		}
	elseif($role=='guardian'){
		$email='';
		//$email=$Newuser['EmailAddress']['value'];
		$start=iconv('UTF-8', 'ASCII//TRANSLIT', $surname);
		$name=$Newuser['Title']['value'].' '.$Newuser['Surname']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Guardian';
		$epftemplate=3;
		//$password=good_strtolower('guest');
		$password=good_strtolower($Newuser['firstchild']);
		$assword=md5($password);
		$classtable='guardian';
		$classfield='id';
		$nums='';
		$code='';
		//while(count($nums)<9){$nums[rand(1,9)]=null;}
		//while(strlen($code)<2){$code.=array_rand($nums);}
		$tail=$code;
		$no=0;
		}
	elseif($role=='staff'){
		$email=$Newuser['EmailAddress']['value'];
		/* Staff usernames are unique within their own ClaSS but need
		to maintain that within box by adding the the school clientid.*/
		if(isset($CFG->clientid)){$start=$CFG->clientid;}
		else{$start='';}
		$tail=$Newuser['Username']['value'];
		$epfusertype='person';
		$epftemplate_name='Default_Staff';
		$epftemplate=1;
		$assword=$Newuser['Password']['value'];
		$no='';
		$classtable='users';
		$classfield='uid';
		}
	$epfusername=good_strtolower($start. $tail);
	$epfusername=str_replace("'",'',$epfusername);
	$epfusername=clean_text($epfusername);
	//	trigger_error($epfusername.' '.$password,E_USER_WARNING);

	$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no';");
	while($olduser=mysql_fetch_array($d_user)){
		$no++;
		$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no';");
		}

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

/* checks for a community and either updates or creates*/
/* expects an array with at least type and name set*/
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

function elgg_update_group($group,$groupfresh=array('owner'=>'','name'=>'','access'=>'')){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'groups';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$access=$group['access'];
	$name=$group['name'];
	$owner=$group['owner'];
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
			if($$namefresh!=''){
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

/* Only to be called from other elgg_ functions.*/
function elgg_get_epfuid($owner,$type){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';

	//	if($CFG->eportfolio_db!='' and $dbepf==''){
	//	$dbepf=db_connect($CFG->eportfolio_db);
	//	mysql_query("SET NAMES 'utf8'");
	//	}

	$d_u=mysql_query("SELECT ident FROM $table WHERE username='$owner'
									AND user_type='$type';");
	if(mysql_num_rows($d_u)==1){
		$uid=mysql_result($d_u,0);
		}
	else{
		$uid=-1;
		}
	//$db=db_connect();
	//mysql_query("SET NAMES 'utf8'");

	return $uid;
	}

function elgg_new_homework($tid,$cid,$bid,$pid,$title,$body,$dateset){
	$access='LOGGED_IN';
	list($year,$month,$day)=explode('-',$dateset);
	$posted=mktime(0,0,0,$month,$day,$year);
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'weblog_posts';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$epfcid=str_replace('/','',$cid);
	$epfcid=str_replace('-','',$epfcid);
	$epfblogname='class'.$epfcid;
	$epfuidweblog=elgg_get_epfuid($epfblogname,$type='community');
	$epfuidowner=elgg_get_epfuid($tid,$type='person');

	if($epfuidowner!='' and $title!='' and $body!=''){
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