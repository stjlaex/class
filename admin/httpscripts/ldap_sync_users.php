#! /usr/bin/php -q
<?php
/**
 *												 ldap_sync_users.php
 *
 */
 
$book='admin';
$current='ldap_sync_users.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
    $ARGS = array();
    foreach ($argv as $arg) {
		if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');
/**/

/* Connect to LDAP server */
$ds = ldap_connect($CFG->ldapserver);

/* Make sure of right LDAP version is being used */
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ds) {
  /* Bind to LDAP DB */
  $userrdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
  $bind_result = ldap_bind($ds, $userrdn, $CFG->ldappasswd);

  $info = array();
  $row=array();

  if ($bind_result) {

	/**
	 *	STEP 1: Process all users (teachers) from ClaSS
	 *	
	 */
	$users = list_all_users('0');

	/* process result */
	$entries=0.0;
	foreach($users as $uid => $row) {

	  $info = array();

	  /* Search for entry in LDAP */
	  $username=$row['username'];
	  $classrole=$row['role'];
	  $sr=ldap_search($ds, 'ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2, "uid=$username", $info);
			
	  if (ldap_count_entries($ds, $sr) > 0) {
		/* When the entry exists, LDAP db is updated with values coming from ClaSS */
		$info = ldap_first_entry($ds, $sr);				
		$attrs = ldap_get_attributes($ds, $info);

		/* prepare data -in LDIF format- for LDAP insertion into DB */
		$info = array();
		$info['uid'] = $row['username'];
		$info['userPassword'] = '{MD5}' . base64_encode(pack('H*',$row['passwd']));
		$info['cn'] = $row['forename'] . ' ' . $row['surname'];
		$info['givenName'] = $row['forename'];
		$info['sn'] = $row['surname'];
		$info['mail'] = $row['email'];
		$info['objectclass'] = 'inetOrgPerson';
		    
		if ($attrs['employeeType'][0]<>$row['role']) {
		  /* change the LDAP entry to other superior RDN */
		    	
		  /* Read Entry again using detailed RDN, the old one */
		  $distinguishedName = 'uid='.$username.',ou='.$attrs['employeeType'][0].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		  $sr=ldap_search($ds, $distinguishedName, "uid=$username");
		  /* change type: modify */
		  if ($sr) {
			$info['employeeType'] = $row['role'];
			$info['ou'] = $row['role'];
			$mod=ldap_modify ( $ds, $distinguishedName , $info );
			$full_old_dn= $distinguishedName;
			$new_rdn= 'uid='.$username;
			$new_superior='ou='.$info['employeeType'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
			$ldren=ldap_rename( $ds, $full_old_dn, $new_rdn, $new_superior, TRUE);					
		  }
		} else {
		  /* modify ldap entry */
		  $distinguishedName = 'uid='.$username.',ou='.$row['role'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		  $r = ldap_modify($ds, $distinguishedName, $info);
		  if (!$r) {
			trigger_error('Unable to modify entry in LDAP DB: '.$distinguishedName, E_USER_WARNING);
		  }
		}
	  } else {
		/* OK, the entry does not exist in LDAP so insert it into LDAP DB	 */
		$info = array();
		/* prepare data -in LDIF format- for LDAP insertion into DB */
		$info['uid'] = $row['username'];
		$info['userPassword'] = '{MD5}' . base64_encode(pack('H*',$row['passwd']));
		$info['cn'] = $row['forename'] . ' ' . $row['surname'];
		$info['givenName'] = $row['forename'];
		$info['sn'] = $row['surname'];
		$info['mail'] = $row['email'];
		$info['objectclass'] = 'inetOrgPerson';
		$info['ou']	= $row['role'];
		$info['employeeType'] = $row['role'];
		/* add data to ldap directory */
		$distinguishedName = 'uid='.$username.',ou='.$info['employeeType'].',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		$r = ldap_add($ds, $distinguishedName, $info);
		if (!$r) {
		  trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
		}
	  }
	  /* entry counter */
	  if (fmod($entries,50.0)==0.0) {
		//echo '/'.$entries;
		if ($entries>=50) {
		  echo '.';
		}
	  }
	  $entries++;
	}
  	echo "\n".'Step 1: '.$entries.' User entries have been processed'."\n";
	

	/**
	 * STEP 2: Process all Students from ClaSS DB
	 *
	 */
	$yearcoms=(array)list_communities('year');
	$Students=array();
	$entries=0.0;
	while(list($yearindex,$com)=each($yearcoms)){
	  $yid=$com['name'];
	  $students=listin_community($com);
	  while(list($studentindex,$student)=each($students)){
		$sid=$student['id'];
		$Students[$sid]=fetchStudent_short($sid);
		$Email=fetchStudent_singlefield($sid,'EmailAddress');
		$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
		$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');
		$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
		$Students[$sid]['EPFUsername']['value']=$EPFUsername['EPFUsername']['value'];
	
		/* Search for entry in LDAP */
		//$username=$Students[$sid]['EPFUsername']['value'];
		$username=get_epfusername($student['id']);
		$sr=ldap_search($ds, 'ou=students'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2, "uid=$username");

		$entry=array();
		$info=array();
	    if (ldap_count_entries($ds, $sr) > 0) {
		  /* When the entry exists, LDAP db is updated with values coming from ClaSS */
		  $entry = ldap_first_entry($ds, $sr);
		  $attrs = ldap_get_attributes($ds, $entry);
		  for ($i=0; $i < $attrs['count']; $i++) {
			$values = ldap_get_values($ds, $entry, $attrs[$i]);
		  }
		  /* prepare data -in LDIF format- for LDAP field replacement */
		  $info=array();
		  $info['uid']= $username;
		  $info['userPassword']= '{MD5}' . base64_encode(pack('H*',md5('abc123')));
		  $info['cn']= $Students[$sid]['Forename']['value'] . ' ' . $Students[$sid]['Surname']['value'];
		  $info['givenName']= $Students[$sid]['Forename']['value'];
		  $info['sn']= $Students[$sid]['Surname']['value'];
		  $info['mail']= $Students[$sid]['EmailAddress']['value'];
		  $info['ou']	= 'Students'; 
		  $info['objectclass']= 'inetOrgPerson';
		  /* add data to ldap directory */
		  $distinguishedName = "uid=$username" . ',ou=students'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		  $r = ldap_modify($ds, $distinguishedName, $info);
		  if (!$r) {
			trigger_error('Unable to modify LDAP DB entry', E_USER_WARNING);
		  }			
		} else {
		  /* OK, the entry does not exist in LDAP so insert it into LDAP DB */
		  $info=array();
		  /* prepare data -in LDIF format- for LDAP insertion into DB */
		  $info['uid']= $username;
		  $info['userPassword']= '{MD5}' . base64_encode(pack('H*',md5('abc123')));
		  $info['cn']	= $Students[$sid]['Forename']['value'] . ' ' . $Students[$sid]['Surname']['value'];
		  $info['givenName']= $Students[$sid]['Forename']['value'];
		  $info['sn']= $Students[$sid]['Surname']['value'];
		  $info['mail']= $Students[$sid]['EmailAddress']['value'];
		  $info['ou']	= 'Students'; 
		  $info['objectclass']= 'inetOrgPerson';
		  /* add data to ldap directory */
		  $distinguishedName = "uid=$username" . ',ou=students'.',ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		  $r = ldap_add($ds, $distinguishedName, $info);
		  if (!$r) {
			trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
		  }
		}
		/* entry counter */
		if (fmod($entries,50.0)==0.0) {
		  //echo '/'.$entries;
		  echo '.';
		}
		$entries++;
	  }
	}
	ldap_close($ds);
	echo "\n".'Step 2: '.$entries.' Student entries have been processed'."\n";
  } else {
	trigger_error('Unable to bind to LDAP server. Nothing has been done.', E_USER_WARNING);
	echo '---> eop. $bind_result: '.$bind_result.'<br />';
  }
} else {
  trigger_error('Unable to connect to LDAP server. Nothing has been done.', E_USER_WARNING);
}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
