<?php 
/**								  		ldap_test.php
 *
 * This is a two steps process for inserting:
 * 1) Teachers
 * 2) Students
 * into the LDAP directory for login to Moodle
 *
 */

$choice='ldap_test.php';
//$action='ldap_test_action.php';

//require_once('../school.php');
//require_once('../classdev/logbook/permissions.php');
//require_once('../classdev/lib/fetch_student.php');
//require_once('../classdev/lib/functions.php');

/* Connect to LDAP server */
$ds=ldap_connect($CFG->ldapserver);

/* Make sure of right LDAP version is being used */
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

if($ds){
	/* Bind to LDAP DB */
	$userrdn='cn=' . $CFG->ldapuser . ',dc=example,dc=com';

	$bind_result=ldap_bind($ds, $userrdn, $CFG->ldappasswd);
	if(!$bind_result){$error='Unable to bind to LDAP server. Nothing has been done.';}
	//echo '---> eop. $bind_result: '.$bind_result.'<br />';
	}
else{
	$error='Unable to connect to LDAP server. Nothing has been done.';
	}

if(isset($error)){
	trigger_error($error, E_USER_WARNING);
	exit;
	}


	/**
	 *	STEP 1: Process all users (teachers) from ClaSS
	 *	
	 */
	$users=list_all_users('0');

	/* process result */
	foreach($users as $uid => $row){

		$info=array();

		/* Search for entry in LDAP */
		$username=$row['username'];
		$sr=ldap_search($ds, 'ou=people,dc=example,dc=com', "uid=$username", $info);

		/* prepare data -in LDIF format- for LDAP insertion into DB */
		$info['uid']=$row['username'];
		$info['userPassword']=$row['passwd'];
		$info['cn']=$row['forename'] . ' ' . $row['surname'];
		$info['givenName']=$row['forename'];
		$info['sn']=$row['surname'];
		$info['mail']=$row['email'];
		$info['objectclass']='inetOrgPerson';
		$info['ou']=$row['role'];
		$info['employeeType']=$row['role'];
			
		if(ldap_count_entries($ds,$sr)>0){
			/* When the entry exists, LDAP db is updated with values coming from ClaSS */
			$entry=ldap_first_entry($ds,$sr);				
			$attrs=ldap_get_attributes($ds,$entry);
				
			if($attrs['employeeType'][0]!=$row['role']){
				/* change the LDAP entry to other superior RDN */
		    	
				/* Read Entry again using detailed RDN, the old one */
				$distinguishedName='uid='.$username. ',ou='.$attrs['employeeType'][0]. ',ou=people,dc=example,dc=com';
				$sr=ldap_search($ds, $distinguishedName, "uid=$username");

				/* change type: modify */
				if($sr){
					//$info['employeeType']=$row['role'];
					//$info['ou']=$row['role'];
					$mod=ldap_modify($ds,$distinguishedName,$info);
					$full_old_dn= $distinguishedName;
					$new_rdn= 'uid='.$username;
					$new_superior='ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
					$ldren=ldap_rename( $ds, $full_old_dn, $new_rdn, $new_superior, TRUE);					
					}
				}
			else{
				/* modify ldap entry */
				$distinguishedName='uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
				$r=ldap_modify($ds, $distinguishedName, $info);
				if(!$r){
					trigger_error('Unable to modify entry in LDAP DB: '.$distinguishedName, E_USER_WARNING);
					}
				}
			}
		else{
			/* OK, the entry does not exist in LDAP so insert it into LDAP DB	 */
			$distinguishedName='uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
			$r=ldap_add($ds, $distinguishedName, $info);
			if(!$r){
				trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
				}
			}
		}

	/**
	 * STEP 2: Process all Students from ClaSS DB
	 *
	 */
		
	$yearcoms=(array)list_communities('year');

	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$students=listin_community($com);

		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Student=array();
			$Student=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
			$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');
			$Student['EmailAddress']['value']=$Email['EmailAddress']['value'];
			$Student['EPFUsername']['value']=$EPFUsername['EPFUsername']['value'];
			$dob=(array)split('-',$Newuser['DOB']['value']);
			$pwstart=utf8_to_ascii($surname);
			$pword=good_strtolower($pwstart[0]).$dob[0];

			/* Search for entry in LDAP */
			$username=$Student['EPFUsername']['value'];

			/* If no username set then ignore this sid */
			if($username!='' and $username!=' '){
				$sr=ldap_search($ds,'ou=students,ou=people,dc=example,dc=com',"uid=$username");
				$entry=array();

				/* Prepare data -in LDIF format- for LDAP field replacement */
				$info=array();
				$info['uid']=$username;
				$info['userPassword']=md5($pword);
				$info['cn']=$Student['Forename']['value'] . ' ' . $Student['Surname']['value'];
				$info['givenName']=$Student['Forename']['value'];
				$info['sn']=$Student['Surname']['value'];
				$info['mail']=$Student['EmailAddress']['value'];
				$info['objectclass']='inetOrgPerson';

				if(ldap_count_entries($ds,$sr)>0){
					/* When the entry exists, LDAP db is updated with values coming from ClaSS */
					$entry=ldap_first_entry($ds,$sr);
					$attrs=ldap_get_attributes($ds, $entry);
					for($i=0; $i<$attrs['count']; $i++){
						$values=ldap_get_values($ds,$entry,$attrs[$i]);
						}
					/* add data to ldap directory */
					$distinguishedName="uid=$username" . ',ou=students,ou=people,dc=example,dc=com';
					$r=ldap_modify($ds,$distinguishedName,$info);
					if(!$r){
						trigger_error('Unable to modify LDAP DB entry', E_USER_WARNING);
						}
					}
				else{
					/* OK, the entry does not exist in LDAP so insert it into LDAP DB */
					
					/* add data to ldap directory */
					$distinguishedName="uid=$username" . ',ou=students,ou=people,dc=example,dc=com';
					$r=ldap_add($ds, $distinguishedName, $info);
					if(!$r){
						trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
						}
					}
				}
			}
		}

ldap_close($ds);

?>
