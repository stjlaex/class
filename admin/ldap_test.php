<?php 
/**								  		ldap_test.php
 *
 * This is a two steps process for inserting:
 * 1) Teachers
 * 2) Students
 * into the LDAP directory for logging in to Moodle
 *
 */

$choice='ldap_test.php';
$action='ldap_test_action.php';

require_once('../school.php');
require_once('../classdev/logbook/permissions.php');
require_once('../classdev/lib/fetch_student.php');

/* Connect to LDAP server */
//$ds = ldap_connect("localhost");
$ds = ldap_connect($CFG->ldapserver);
echo "---> $ds: ".$ds."<br />";

/* Make sure of right LDAP version is being used */
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ds) {
	/* Bind to LDAP DB */
	$userrdn="cn=" . $CFG->ldapuser . ",dc=example,dc=com";
	echo '---> $userrdn: '.$userrdn.'<br />';
	
  $bind_result = ldap_bind($ds, $userrdn, $CFG->ldappasswd);
	echo '---> $bind_result: '.$bind_result.'<br />';
	
	$info = array();
	$row=array();
	
	if ($bind_result) {

/**
 *	STEP 1: Process all users (teachers) from ClaSS
 *	
 */
		$users = list_all_users('0');

		/* process result */
		foreach($users as $uid => $row) {

			$info = array();

	    /* Search for entry in LDAP */
			$username=$row['username'];
			$classrole=$row['role'];
	    //$sr=ldap_search($ds, 'ou='.$classrole.',ou=people,dc=example,dc=com', "uid=$username");
	    $sr=ldap_search($ds, 'ou=people,dc=example,dc=com', "uid=$username", $info);
	    //echo "$username".'<br />';

			
	    if (ldap_count_entries($ds, $sr) > 0) {
				/* When the entry exists, LDAP db is updated with values coming from ClaSS */
		    //$info = ldap_get_entries($ds, $sr);
				$info = ldap_first_entry($ds, $sr);				
				$attrs = ldap_get_attributes($ds, $info);

				if ($attrs['uid'][0]=='Prof9') { 
					echo '(0): '.'<br />';
					echo '<pre><br />';
					echo $attrs['employeeType'][0].'<br />';
					//print_r($attrs);
					echo '</pre><br />';
				}
				
				if ($row['username']=='Prof9') { echo '(1): '.$row['username'].'<br />';}
				
		    /* prepare data -in LDIF format- for LDAP insertion into DB */
			$info = array();
		    $info['uid'] 					= $row['username'];
		    $info['userPassword'] = $row['passwd'];
		    $info['cn'] 					= $row['forename'] . ' ' . $row['surname'];
		    $info['givenName'] 		= $row['forename'];
		    $info['sn'] 					= $row['surname'];
		    $info['mail'] 				= $row['email'];
		    $info['objectclass'] 	= 'inetOrgPerson';
		    
		    //if ($info['employeeType']<>$row['role']) {
		    if ($attrs['employeeType'][0]<>$row['role']) {
		    	/* change the LDAP entry to other superior RDN */

		    	
			    /* Read Entry again using detailed RDN, the old one */
			    //$distinguishedName = 'uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
			    $distinguishedName = 'uid='.$username.',ou='.$attrs['employeeType'][0].',ou=people,dc=example,dc=com';

					if ($row['username']=='Prof9') { 
						echo '(2): '.$row['username'].'<br />';
						echo '(2): '.$distinguishedName.'<br />';

					}


			    $sr=ldap_search($ds, $distinguishedName, "uid=$username");
			    /* change type: modify */
			    if ($sr) {
			    	
				    $info['employeeType'] 	= $row['role'];
						$info['ou']							= $row['role'];
						
			    	$mod=ldap_modify ( $ds, $distinguishedName , $info );

					if ($row['username']=='Prof9') { 
						echo '(3): '.$row['role'].'<br />';
						echo '(3): '.$info['employeeType'].'<br />';
						echo '(3): '.$distinguishedName.'<br />';

					}
			    	
			    	$full_old_dn= $distinguishedName;
						//$new_rdn= 'uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
						$new_rdn= 'uid='.$username;
						$new_superior='ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
						
					if ($row['username']=='Prof9') { 
						echo '(4): '.$full_old_dn.'<br />';
						echo '(4): '.$new_rdn.'<br />';
						echo '(4): '.$new_superior.'<br />';

					}
						
						
						$ldren=ldap_rename( $ds, $full_old_dn, $new_rdn, $new_superior, TRUE);					
			    }
		    } else {
		    
			    /* modify ldap entry */
			    $distinguishedName = 'uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
			    //echo 'DN : '. $distinguishedName.'<br />';
			    $r = ldap_modify($ds, $distinguishedName, $info);
			    if (!$r) {
			    	trigger_error('Unable to modify entry in LDAP DB: '.$distinguishedName, E_USER_WARNING);
			    }
		    
		    }

	    } else {
				/* OK, the entry does not exist in LDAP so insert it into LDAP DB	 */
				$info = array();

		    /* prepare data -in LDIF format- for LDAP insertion into DB */
		    $info['uid'] 					= $row['username'];
		    $info['userPassword'] = $row['passwd'];
		    $info['cn'] 					= $row['forename'] . ' ' . $row['surname'];
		    $info['givenName'] 		= $row['forename'];
		    $info['sn'] 					= $row['surname'];
		    $info['mail'] 				= $row['email'];
		    $info['objectclass'] 	= 'inetOrgPerson';
		    $info['ou']						= $row['role'];
		    $info['employeeType']	= $row['role'];
		    

		    /* add data to ldap directory */
		    $distinguishedName = 'uid='.$username.',ou='.$info['employeeType'].',ou=people,dc=example,dc=com';
		    //echo "$distinguishedName".'<br />';
		    $r = ldap_add($ds, $distinguishedName, $info);
		    if (!$r) {
		    	trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
		    }
	    } 
		}

/**
 * STEP 2: Process all Students from ClaSS DB
 *
 */

	$yearcoms=(array)list_communities('year');

	$Students=array();

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
			$username=$Students[$sid]['EPFUsername']['value'];

	    $sr=ldap_search($ds, 'ou=students,ou=people,dc=example,dc=com', "uid=$username");

			$entry=array();
			$info=array();

	    if (ldap_count_entries($ds, $sr) > 0) {

				/* When the entry exists, LDAP db is updated with values coming from ClaSS */
				$entry = ldap_first_entry($ds, $sr);
				$attrs = ldap_get_attributes($ds, $entry);
				//echo '> >'.$attrs['count'] . ' attributes held for this entry:<p>';
				for ($i=0; $i < $attrs['count']; $i++) {
				    //echo '-> '.$attrs[$i]. ' ';
						$values = ldap_get_values($ds, $entry, $attrs[$i]);
						//echo $values[0] . '<br />';
						// field updated with likely new value
						// $info["$attrs[$i]"]	= $values[0];
				}
				//echo '<pre>';
				//print_r($info);
				//echo '</pre>';
				
		    /* prepare data -in LDIF format- for LDAP field replacement */
				$info=array();
		    $info['uid'] 					= $username;
		    //$info['userPassword'] = md5('abc123');
		    $info['userPassword'] = 'abc123';
		    $info['cn'] 					= $Students[$sid]['Forename']['value'] . ' ' . $Students[$sid]['Surname']['value'];
		    $info['givenName']		= $Students[$sid]['Forename']['value'];
		    $info['sn'] 					= $Students[$sid]['Surname']['value'];
		    $info['mail'] 				= $Students[$sid]['EmailAddress']['value'];
		    $info['objectclass'] 	= 'inetOrgPerson';
		    
				//echo '<pre>';
				//print_r($info);
				//echo '</pre>';
				
		    /* add data to ldap directory */
		    $distinguishedName = "uid=$username" . ',ou=students,ou=people,dc=example,dc=com';
		    //echo 'DN : '. $distinguishedName.'<br />';
		    $r = ldap_modify($ds, $distinguishedName, $info);
		    if (!$r) {
		    	trigger_error('Unable to modify LDAP DB entry', E_USER_WARNING);
		    }
					
			} else {
					/* OK, the entry does not exist in LDAP so insert it into LDAP DB */
					$info=array();
			    /* prepare data -in LDIF format- for LDAP insertion into DB */
			    $info['uid'] 					= $username;
			    /* $info['userPassword'] = md5('abc123'); */
			    $info['userPassword'] = 'abc123';
			    $info['cn'] 					= $Students[$sid]['Forename']['value'] . ' ' . $Students[$sid]['Surname']['value'];
			    $info['givenName']		= $Students[$sid]['Forename']['value'];
			    $info['sn'] 					= $Students[$sid]['Surname']['value'];
			    $info['mail'] 				= $Students[$sid]['EmailAddress']['value'];
			    $info['objectclass'] 	= 'inetOrgPerson';

			    /* add data to ldap directory */
			    $distinguishedName = "uid=$username" . ',ou=students,ou=people,dc=example,dc=com';
			    $r = ldap_add($ds, $distinguishedName, $info);
			    if (!$r) {
			    	trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
			    }
		    } 
			} 			
		}
  ldap_close($ds);
	} else {
	  trigger_error('Unable to bind to LDAP server. Nothing has been done.', E_USER_WARNING);
		echo '---> eop. $bind_result: '.$bind_result.'<br />';
	}
} else {
  trigger_error('Unable to connect to LDAP server. Nothing has been done.', E_USER_WARNING);
} 
?>
