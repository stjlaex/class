#! /usr/bin/php -q
<?php
/**
 *												 ldap_sync_users.php
 *
 */ 
$book='admin';
$current='ldap_sync_users.php';

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


$ds=false;

if(isset($CFG->ldapserver) and $CFG->ldapserver!=''){
	/* Connect to LDAP server */
	$ds=ldap_connect($CFG->ldapserver);

	/* Make sure of right LDAP version is being used */
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	}

if($ds){
	/* Bind to LDAP DB */
	$userrdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	$bind_result=ldap_bind($ds, $userrdn, $CFG->ldappasswd);

	$info=array();
	$row=array();
	/* This is the default password for students and parents. */
	$firstpass=$CFG->clientid.'1234';

	if($bind_result){
		/**
		 *	STEP 1: Process all users (teachers) from ClaSS
		 *	
		 */
		$users=(array)list_all_users();
		/* process result */
		$countno=0;
		foreach($users as $uid => $row){
			$info=array();
			$Newuser=(array)fetchUser_short($row);
			$epfusername=$Newuser['EPFUsername']['value'];
			if($epfusername=='' or $epfusername==' '){
				$epfusername=new_epfusername($Newuser,'staff',$ds);
				}
			/*The cn for a user takes the first part of their email address (removing any dots).*/
			$atpos=strpos($row['email'], '@');
			if($row['email']=='' or $row['email']==' ' or $atpos==false or $atpos==0){
				$cn=-1;
				}
			else{
				$emailfirstpart=substr($row['email'],0,$atpos);
				$atpos=strpos($emailfirstpart, '.');
				if($atpos!=0){
					$emailfirstpartwop=substr($row['email'],0,$atpos);
					$remainder=substr($emailfirstpart,$atpos+1);
					$emailfirstpartwop=$emailfirstpartwop.$remainder;
					} 
				else{
					$emailfirstpartwop=$emailfirstpart;
					}
				$cn=$emailfirstpartwop;
				}
			$classrole=$row['role'];
			$sr=ldap_search($ds, 'ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2, "uid=$epfusername", $info);
			if(ldap_count_entries($ds, $sr) > 0){
				if($row['nologin']=='1'){
					$distinguishedName='uid='.$epfusername.',ou='.$row['role'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
					ldap_delete($ds, $distinguishedName);
					//trigger_error('Deleted nologin user LDAP: '.$distinguishedName, E_USER_WARNING);
					}
				else{
					/* When the entry exists, LDAP db is updated with values coming from ClaSS */
					$info=ldap_first_entry($ds, $sr);
					$attrs=ldap_get_attributes($ds, $info);

					/* Prepare data -in LDIF format- for LDAP insertion into DB */
					$info=array();
					$info['uid']=$epfusername;
					$info['userPassword']='{MD5}' . base64_encode(pack('H*',$row['passwd']));
					$info['cn']=$cn;
					$info['givenName']=$row['forename'];
					$info['sn']=$row['surname'];
					$info['title']=$row['title'];
					$info['mail']=$row['email'];
					$info['objectclass']='inetOrgPerson';
				
					if($attrs['employeeType'][0]<>$row['role']){
						/* Change the LDAP entry to other superior RDN */
						/* Read Entry again using detailed RDN, the old one */
						$distinguishedName='uid='.$epfusername.',ou='.$attrs['employeeType'][0].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
						$sr=ldap_search($ds, $distinguishedName, "uid=$epfusername");
						/* change type: modify */
						if($sr){
							$info['employeeType']=$row['role'];
							$info['ou']=$row['role'];
							$mod=ldap_modify ( $ds, $distinguishedName , $info );
							$full_old_dn= $distinguishedName;
							$new_rdn= 'uid='.$epfusername;
							$new_superior='ou='.$info['employeeType'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
							$ldren=ldap_rename($ds, $full_old_dn, $new_rdn, $new_superior, TRUE);					
							}
						} 
					else{
						/* modify ldap entry */
						$distinguishedName='uid='.$epfusername.',ou='.$row['role'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
						$r=ldap_modify($ds, $distinguishedName, $info);
						if(!$r){
							trigger_error('Unable to modify entry in LDAP DB: '.$distinguishedName, E_USER_WARNING);
							}
						}
					}
				}
			elseif($cn!=-1 and $row['nologin']=='0') {
				/* OK, the entry does not exist in LDAP so insert it into LDAP DB	 */
				$info=array();
				/* prepare data -in LDIF format- for LDAP insertion into DB */
				$info['uid']=$epfusername;
				$info['userPassword']='{MD5}' . base64_encode(pack('H*',$row['passwd']));
				$info['cn']=$cn;
				$info['givenName']=$row['forename'];
				$info['title']=$row['title'];
				$info['sn']=$row['surname'];
				$info['mail']=$row['email'];
				$info['objectclass']='inetOrgPerson';
				$info['ou']	= $row['role'];
				$info['employeeType']=$row['role'];
				/* add data to ldap directory */
				$distinguishedName='uid='.$epfusername.',ou='.$info['employeeType'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$r=ldap_add($ds, $distinguishedName, $info);
				if(!$r){
					trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName. ' with cn: '.$cn, E_USER_WARNING);
					}
				}
				$group_name='cn=users,ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$group_info['member']='uid='.$epfusername.',cn=users,ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$s=ldap_mod_add($ds,$group_name,$group_info);
			/* entry counter */
			$countno++;
			}
		trigger_error('Step 1: '.$countno.' User entries have been processed', E_USER_NOTICE);


		/**
		 * STEP 2: Process all Students from ClaSS DB
		 *
		 */
		$yearcoms=(array)list_communities('year');
		//$yearcoms=array();
		$Students=array();
		$countno=0;
		foreach($yearcoms as $com){
			$yid=$com['name'];
			$students=listin_community($com);
			foreach($students as $student){
				$sid=$student['id'];
				$Students[$sid]=fetchStudent_short($sid);
				$Email=fetchStudent_singlefield($sid,'EmailAddress');
				$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
				$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');
				$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
				$Students[$sid]['EPFUsername']['value']=$EPFUsername['EPFUsername']['value'];

				/* Search for entry in LDAP */
				$epfusername=$Students[$sid]['EPFUsername']['value'];
				if($epfusername==''){
					/* Treat as a completely new entry. */
					$epfusername=new_epfusername($Students[$sid],'student',$ds);
					}
				else{
					/* Should already be in LDAP. */
					$sr=ldap_search($ds,'ou=student'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2,"uid=$epfusername");
					}

				/* Prepare data -in LDIF format- for LDAP field replacement */
				$info=array();
				$info['uid']= $epfusername;
				$info['cn']=$Students[$sid]['Forename']['value'] . ' ' . $Students[$sid]['Surname']['value'];
				$info['givenName']=$Students[$sid]['Forename']['value'];
				$info['sn']=$Students[$sid]['Surname']['value'];
				$info['ou']='student';
				$info['objectclass']= 'inetOrgPerson';
				$distinguishedName="uid=$epfusername".',ou=student'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;

				/* When the entry exists, LDAP db is updated with values coming from ClaSS */
				if(ldap_count_entries($ds, $sr)>0){
					/* modify the data in ldap directory */
					$r=ldap_modify($ds, $distinguishedName, $info);
					if(!$r){
						trigger_error('Unable to modify LDAP DB entry', E_USER_WARNING);
						}
					}
				else{
					/* OK, the entry does not exist in LDAP so insert it into LDAP DB */
					if($Students[$sid]['EmailAddress']['value']==''){$info['mail']=$epfusername;}
					else{$info['mail']=$Students[$sid]['EmailAddress']['value'];}
					$info['userPassword']= '{MD5}' . base64_encode(pack('H*',md5($firstpass)));
					/* add data to ldap directory */
					$distinguishedName="uid=$epfusername" . ',ou=student'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
					$r=ldap_add($ds, $distinguishedName, $info);
					if(!$r){
						trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
						}
					}
				$group_name='cn=users,ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$group_info['member']='uid='.$epfusername.',cn=users,ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$s=ldap_mod_add($ds,$group_name,$group_info);

				/* entry counter */
				$countno++;
				}
			}
		trigger_error('Step 2: '.$countno.' Student entries have been processed', E_USER_NOTICE);


		/**
		 * STEP 3: Process all Contacts from ClaSS DB
		 *
		 *
		 * Want all contacts who may recieve any sort of mailing to be
		 * given an account.
		 */

		$Contacts=array();
		$countno=0;
		$d_c=mysql_query("SELECT DISTINCT guardian_id FROM gidsid JOIN
							student ON gidsid.student_id=student.id WHERE gidsid.mailing!='0';");
		while($contact=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$gid=$contact['guardian_id'];
			$Contacts[$gid]=fetchContact(array('guardian_id'=>$gid));
			if($Contacts[$gid]['Surname']['value']!='' and $Contacts[$gid]['Surname']['value']!=' '){
				$epfusername=$Contacts[$gid]['EPFUsername']['value'];
				/* Check for any unpleasant mess from utf8 problems. */
				$pos=strpos($epfusername,'?');
				if($pos!==false){
					$distinguishedName='uid='.$epfusername.',ou=contact,ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
					ldap_delete($ds, $distinguishedName);
					//trigger_error('Deleted: '.$distinguishedName, E_USER_WARNING);
					$epfusername='';
					}
				if($epfusername=='' or $epfusername==' '){
					/* Treat as a completely new entry. */
					$fresh=false;
					/* First check by email for this person in LDAP already.*/
					$mail=$Contacts[$gid]['EmailAddress']['value'];
					if($mail!='' and $mail!=' '){
						$sr=ldap_search($ds,'ou=contact'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2,"mail=$mail",array('uid','mail'));
						if(ldap_count_entries($ds, $sr)>0){
							$entries=ldap_get_entries($ds, $sr);
							$epfusername=$entries[0]['uid'][0];
							mysql_query("UPDATE guardian SET epfusername='$epfusername' WHERE id='$gid';");
							//trigger_error($mail.' MATCH '.$entries[0]['uid'][0], E_USER_WARNING);
							$fresh=true;
							}
						}

					$epfusername=new_epfusername($Contacts[$gid],'contact',$ds);
					}
				else{
					/* Should already be in LDAP so search for entry in LDAP */
					$sr=ldap_search($ds,'ou=contact'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2,"uid=$epfusername");
					}

				/* Prepare data -in LDIF format- for LDAP field replacement */
				$info=array();
				if($Contacts[$gid]['EmailAddress']['value']=='' or $Contacts[$gid]['EmailAddress']['value']==' '){
					$info['mail']=$epfusername;
					}
				else{$info['mail']=$Contacts[$gid]['EmailAddress']['value'];}
				$info['uid']=$epfusername;
				$info['cn']=$Contacts[$gid]['Forename']['value'] . ' ' . $Contacts[$gid]['Surname']['value'];
				//$info['givenName']= $Contacts[$gid]['Forename']['value'];//Often blank for contacts so remove
				$info['sn']=$Contacts[$gid]['Surname']['value'];
				$info['ou']='contact';
				$info['objectclass']='inetOrgPerson';
				$distinguishedName="uid=$epfusername".',ou=contact'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				/* When the entry exists, LDAP db is updated with values coming from ClaSS */
				if(ldap_count_entries($ds, $sr)>0){

					//trigger_error($countno.' MODIFY '.$gid.' '.$Contacts[$gid]['EPFUsername']['value'], E_USER_WARNING);
				
					/* modify the data in ldap directory */
					$r=ldap_modify($ds, $distinguishedName, $info);
					if(!$r){
						trigger_error('Unable to modify LDAP DB entry', E_USER_WARNING);
						}
					}
				else{
					/* OK, the entry does not exist in LDAP so insert it into LDAP DB */
					$info['userPassword']='{MD5}' . base64_encode(pack('H*',md5($firstpass)));
					/* add data to ldap directory */
					$r=ldap_add($ds, $distinguishedName, $info);
					if(!$r){
						trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
						}
					}
				/* entry counter */
				$countno++;
				}
			}

		trigger_error('Step 3: '.$countno.' Contact entries have been processed', E_USER_NOTICE);

		ldap_close($ds);
		}
	else{
		trigger_error('Unable to bind to LDAP server. Nothing has been done.', E_USER_WARNING);
		}

	}
else{
	trigger_error('Unable to connect to LDAP server. Nothing has been done.', E_USER_WARNING);
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
