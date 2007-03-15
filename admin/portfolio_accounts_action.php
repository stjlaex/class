<?php
/**									portfolio_accounts_action.php
 *
 * NOTE: all ClaSS functions must be called first, once an elgg_
 * function call has been made then a page reload is needed to
 * re-connect with the ClaSS db.
 *
 */

$action='portfolio_accounts.php';

//if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
//if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
//if(isset($_POST['fid'])){$fid=$_POST['fid'];}else{$fid='';}

include('scripts/sub_action.php');

	/*get all ClaSS data first*/
	$yearcoms=(array)list_communities('year');
	$formcoms=(array)list_communities('form');
	$Students=array();
	$formusers=array();
	$epf_contacts=array();
	while(list($index,$com)=each($formcoms)){
		$fid=$com['name'];
		$d_form=mysql_query("SELECT teacher_id FROM form WHERE id='$fid'");
		$formusers[]=mysql_result($d_form,0);
		$students=listin_community($com);
		while(list($index,$student)=each($students)){
			$sid=$student['id'];
			$Students[$sid]=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];

			$Contacts=fetchContacts($sid);
			while(list($index,$Contact)=each($Contacts)){
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
	reset($formcoms);

	$users=list_teacher_users();

	/*now insert into elgg*/
	/*all db calls beneath this must now be to elgg and NOT class*/
	elgg_refresh();
	$staff=array();

	while(list($index,$user)=each($users)){
		$Newuser['Surname']['value']=$user['surname'];
		$Newuser['Forename']['value']=$user['forename'];
		$Newuser['Email']['value']=$user['email'];
		$Newuser['Username']['value']=$user['username'];
		$Newuser['Password']['value']=$user['passwd'];
		$epfuid=elgg_newUser($Newuser,'staff');
		$staff[$user['username']]=$epfuid;
		}

	while(list($index,$com)=each($yearcoms)){
		elgg_update_community($com);
		}
	while(list($index,$com)=each($formcoms)){
		elgg_update_community($com);
		$tid=$formusers[$index];
		$epfuid=$staff[$tid];
		elgg_join_community($epfuid,$com);
		}

	while(list($sid,$Student)=each($Students)){
		$epfuid=elgg_newUser($Student,'student');
		$Students[$sid]['epfuid']=$epfuid;
		$com=array('epfcomid'=>'','type'=>'form','name'=>$Student['RegistrationGroup']['value']);
		elgg_join_community($epfuid,$com);
		$com=array('epfcomid'=>'','type'=>'year','name'=>$Student['YearGroup']['value']);
		elgg_join_community($epfuid,$com);
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'family','access'=>'');
		$epfgroupid=elgg_update_group($group);
		elgg_new_folder($owner=$epfuid,$name='Reports',$access='group'.$epfgroupid);
		elgg_new_folder($owner=$epfuid,$name='Portfolio Work',$access='group'.$epfgroupid);
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

//trigger_error('Family epfuid: '.$epfuid.' sids:'.$index,E_USER_WARNING);}

include('scripts/results.php');
include('scripts/redirect.php');


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
	$active='yes';
	$no=0;

	/*while testing guardians get a fake email address*/
	/*and the password is always just the surname*/
	if($role=='student'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$epfusername=good_strtolower($Newuser['Forename']['value'][0].$surname[0].$no);
		$epfusertype='person';
		$epftemplate='Student_Template';
		$password=good_strtolower($surname[0]);
		$assword=md5($password);
		}
	elseif($role=='guardian'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$epfusername=good_strtolower($surname[0]).'family'.$no;
		$epfusertype='guardian';
		$epftemplate='Guardian_Template';
		$password=good_strtolower($surname[0]);
		$assword=md5($password);
		}
	elseif($role=='staff'){
		//$email=$Newuser['EmailAddress']['value'];
		$email='';
		$epfusername=$Newuser['Username']['value'];
		$epfusertype='person';
		$epftemplate='Staff_Template';
		$assword=$Newuser['Password']['value'];
		}


	$d_user=mysql_query("SELECT ident 
							FROM $table WHERE username='$epfusername'");
	while($olduser=mysql_fetch_array($d_user)){
		$d_user=mysql_query("SELECT ident 
							FROM $table WHERE username='$epfusername'");
		$no++;
		if($role=='student'){
			$epfusername=good_strtolower($Newuser['Forename']['value'][0].$surname[0].$no);
			}
		elseif($role=='guardian'){
			$epfusername=good_strtolower($surname[0]).'family'.$no;
			}
		elseif($role=='staff'){
			$epfusername=good_strtolower($surname[0]).'family'.$no;
			}
		}

	mysql_query("INSERT INTO $table (username, password, name, 
					email, active, user_type, template_name) VALUES 
					('$epfusername', '$assword', '$name',
					'$email', '$active', '$epfusertype','$epftemplate')");
	$epfuid=mysql_insert_id();
	return $epfuid;
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	}

/* checks for a community and either updates or creates*/
/* expects an array with at least type and name set*/

function elgg_update_community($community,$communityfresh=array('type'=>'','name'=>'')){
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
		$epfusername=$type . $name;
		$epfname=$type . ' ' . $name;
		$d_community=mysql_query("SELECT ident FROM $table WHERE
				username='$epfusername'");
		if(mysql_num_rows($d_community)==0){
			$epftemplate='Staff_Template';
			mysql_query("INSERT INTO $table (username, name, 
				    active, moderation, owner, user_type, template_name) 
					VALUES ('$epfusername', '$epfname',
					'yes', 'yes', '1', 'community','$epftemplate')");
			$comid=mysql_insert_id();
			}
		else{
			$comid=mysql_result($d_community,0);
			if($typefresh!='' and $namefresh!=''){
				if(isset($communityfresh['details'])){$detailsfresh=$communityfresh['details'];}
				/*TODO update all references to epfusername, if
					allowed by elgg?*/
				$epfusername=$typefresh . $namefresh;
				mysql_query("UPDATE $table SET username='$epfusername'
						   WHERE ident='$comid'");
				}
			}
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
		$name=$community['name'];
		$epfcomname=$type . $name;
		$d_elgg=mysql_query("SELECT ident FROM $table_users WHERE
							username='$epfcomname' AND user_type='community'");
		$epfcomid=mysql_result($d_elgg,0);
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
