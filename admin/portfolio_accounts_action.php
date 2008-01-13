<?php
/**									portfolio_accounts_action.php
 *
 * NOTE: all ClaSS functions must be called first, once an elgg_
 * function call has been made then a page reload is needed to
 * re-connect with the ClaSS db.
 *
 */

$action='portfolio_accounts.php';

include('scripts/sub_action.php');

	/*get all ClaSS data first*/
	$yearcoms=(array)list_communities('year');
	$formcoms=(array)list_communities('form');
	$classes=(array)list_course_classes();
	$Students=array();
	$yearusers=array();
	$epf_contacts=array();
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$yearusers[$yid]=array();
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*heads of year only*/
		$owners=(array)list_pastoral_users($yid,$yearperms);
		while(list($uid,$user)=each($owners)){
			if($user['role']!='office' and $user['role']!='admin'){
				$yearusers[$yid][]=$user['username'];
				}
			}
		$students=listin_community($com);
		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Students[$sid]=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
			$Contacts=fetchContacts($sid);
			while(list($contactindex,$Contact)=each($Contacts)){
				$mailing=$Contact['ReceivesMailing']['value'];
				if($mailing=='0' or $mailing=='1' or $mailing=='2'){
					if(!array_key_exists($Contact['id_db'],$epf_contacts)){
						$epf_contacts[$Contact['id_db']]['sids']=array();
						$epf_contacts[$Contact['id_db']]['Contact']=$Contact;
						}
					$epf_contacts[$Contact['id_db']]['sids'][]=$sid;
					}
				}
			}
		}

	$formusers=array();
	while(list($index,$com)=each($formcoms)){
		$fid=$com['name'];
		$d_form=mysql_query("SELECT teacher_id FROM form WHERE id='$fid'");
		$formusers[]=mysql_result($d_form,0);
		}
	reset($formcoms);

	$allteachers=list_teacher_users();

	/* Now insert into elgg*/
	/* All db calls beneath this must now be to elgg and NOT class*/
	elgg_refresh();
	$staff=array();

	while(list($index,$user)=each($allteachers)){
		$Newuser['Surname']['value']=$user['surname'];
		if($user['title']!=''){
			$Newuser['Forename']['value']=get_string(displayEnum($user['title'],'title'),'infobook');
			}
		else{
			$Newuser['Forename']['value']=$user['forename'];
			}
		$Newuser['Email']['value']=$user['email'];
		$Newuser['Username']['value']=$user['username'];
		$Newuser['Password']['value']=$user['passwd'];
		$epfuid=elgg_newUser($Newuser,'staff');
		$staff[$user['username']]=$epfuid;
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
		/*Only one can be the owner and this takes it the last in the list.*/
		elgg_update_community($com,$com,$epfuid);
		}
	reset($formcoms);
	while(list($formindex,$com)=each($formcoms)){
		$tid=$formusers[$formindex];
		$epfuid=$staff[$tid];
		elgg_update_community($com,$com,$epfuid);
		elgg_join_community($epfuid,$com);
		}
	while(list($sid,$Student)=each($Students)){
		$epfuid=elgg_newUser($Student,'student');
		$Students[$sid]['epfuid']=$epfuid;
		$com=array('epfcomid'=>'','type'=>'form','name'=>$Student['RegistrationGroup']['value']);
		elgg_join_community($epfuid,$com);
		$yid=$Student['YearGroup']['value'];
		$com=array('epfcomid'=>$yearepfcomids[$yid],'type'=>'year','name'=>'');
		elgg_join_community($epfuid,$com);
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'family','access'=>'');
		$epfgroupid=elgg_update_group($group);
		elgg_new_folder($owner=1,$name='Reports',$access='group'.$epfgroupid);
		elgg_new_folder($owner=1,$name='Portfolio Work',$access='group'.$epfgroupid);
		$Students[$sid]['epfgroupid']=$epfgroupid;
		}

	while(list($gid,$epf_contact)=each($epf_contacts)){
		$Contact=$epf_contact['Contact'];
		$epfuid=elgg_newUser($Contact,'guardian');
		$sids=$epf_contact['sids'];
		while(list($index,$sid)=each($sids)){
			/*joining a family community involves simply an entry in
				friends and an access group, a family does not have a community of
				its own*/
			elgg_join_community($epfuid,array('epfcomid'=>$Students[$sid]['epfuid']));
			elgg_join_group($epfuid,array('epfgroupid'=>$Students[$sid]['epfgroupid'],'name'=>'family','owner'=>$Students[$sid]['epfuid'],'access'=>''));
			}
		}



include('scripts/results.php');
include('scripts/redirect.php');
exit;

/*******************************************
 *						elgg functions
 */

function elgg_refresh(){
	global $CFG;
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$table_users=$CFG->eportfolio_db_prefix.'users';
	$table_friends=$CFG->eportfolio_db_prefix.'friends';
	$table_groups=$CFG->eportfolio_db_prefix.'groups';
	$table_folders=$CFG->eportfolio_db_prefix.'file_folders';
	$table_members=$CFG->eportfolio_db_prefix.'group_membership';
	mysql_query("DELETE FROM $table_users WHERE ident!='1'");
	mysql_query("DELETE FROM $table_groups");
	mysql_query("DELETE FROM $table_members");
	mysql_query("DELETE FROM $table_friends");
	mysql_query("DELETE FROM $table_folders");
	}

function elgg_newUser($Newuser,$role){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	$surname=(array)split(' ',$Newuser['Surname']['value']);
    $name=$Newuser['Forename']['value'].' '.$Newuser['Surname']['value'];
	$no=0;
	$active='yes';
	setlocale(LC_CTYPE,'en_GB');

	if($role=='student'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$start=iconv('UTF-8', 'ASCII//TRANSLIT', $Newuser['Forename']['value'][0]);
		$tail=iconv('UTF-8', 'ASCII//TRANSLIT', $surname[0]);
		$epfusertype='person';
		$epftemplate_name='Student_Template';
		$epftemplate=6;
		$password=good_strtolower('guest');
		$assword=md5($password);
		}
	elseif($role=='guardian'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$start=iconv('UTF-8', 'ASCII//TRANSLIT', $surname[0]);
		$tail='family';
		$name='Family '.$Newuser['Surname']['value'];
		$epfusertype='guardian';
		$epftemplate_name='Guardian_Template';
		$epftemplate=6;
		$password=good_strtolower('guest');
		$assword=md5($password);
		}
	elseif($role=='staff'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$start='';
		$tail=$Newuser['Username']['value'];
		$epfusertype='person';
		$epftemplate_name='Staff_Template';
		$epftemplate=6;
		$assword=$Newuser['Password']['value'];
		$no='';
		}
	$epfusername=good_strtolower($start. $tail);
	$epfusername=str_replace("'",'',$epfusername);
	$epfusername=clean_text($epfusername);

	$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no'");
	while($olduser=mysql_fetch_array($d_user)){
		$no++;
		$d_user=mysql_query("SELECT ident FROM $table WHERE username='$epfusername$no'");
		}

	mysql_query("INSERT INTO $table (username, password, name, 
					email, active, user_type, template_id) VALUES 
					('$epfusername$no', '$assword', '$name',
					'$email', '$active', '$epfusertype','$epftemplate')");
	$epfuid=mysql_insert_id();
	return $epfuid;
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}

/* checks for a community and either updates or creates*/
/* expects an array with at least type and name set*/
function elgg_update_community($community,$communityfresh=array('type'=>'','name'=>''),$epfuidowner=''){
	global $CFG;
	$table=$CFG->eportfolio_db_prefix.'users';
	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}

	$type=$community['type'];
	$name=$community['name'];
	$typefresh=$communityfresh['type'];
	$namefresh=$communityfresh['name'];
	if($type!='' and $name!=''){
		/*The portfolio communities want the real name not the yid etc.*/
		if(isset($community['displayname'])){$epfname=$community['displayname'];}
		else{$epfname=$name;}
		$epfname=str_replace("-",'',$epfname);
		/* Make sure username is still unique across all user types.*/
		$epfusername=str_replace(' ','',$epfname);
		$epfusername=$type . $epfusername;
		$d_community=mysql_query("SELECT ident FROM $table WHERE username='$epfusername'");
		if(mysql_num_rows($d_community)==0){
			$epftemplate_name='Staff_Template';
			$epftemplate=6;
			mysql_query("INSERT INTO $table (username, name, 
				    active, moderation, owner, user_type, template_id) 
					VALUES ('$epfusername', '$epfname',
					'yes', 'yes', '1', 'community','$epftemplate')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				/*TODO update all references to epfusername, if
					allowed by elgg?*/
				$epfusername=$typefresh . $namefresh;
				mysql_query("UPDATE $table SET username='$epfusername'
						   WHERE ident='$comid'");
				}
			}
		}
	if(isset($comid) and $epfuidowner!=''){
		mysql_query("UPDATE $table SET owner='$epfuidowner' WHERE ident='$comid'");
		}

	return $comid;
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
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
	else{
		$epfcomid=elgg_update_community($community);
		if($community['name']=='-2'){trigger_error($epfuid.' '.$epfcomid,E_USER_WARNING);}
		}
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
					VALUES ('$owner', '$name','$access')");
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
	return $epfgroupid;
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
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
		//$epfgroupid=mysql_result($d_elgg,0);
		}
	mysql_query("INSERT INTO $table_member SET user_id='$epfuid',
							group_id='$epfgroupid'");
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

?>
