<?php 
/**								  		ldap_test.php
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

	    /* Search for entry in LDAP */
			$username=$row['username'];
	    $sr=ldap_search($ds, 'dc=example,dc=com', "uid=$username");
	    //echo "$username".'<br />';

	    if (ldap_count_entries($ds, $sr) > 0) {
				/* When the entry exists, LDAP db is updated with values coming from ClaSS */
		    $info = ldap_get_entries($ds, $sr);

		    /* prepare data -in LDIF format- for LDAP insertion into DB */
		    $info['uid'] 					= $row['username'];
		    $info['userPassword'] = $row['passwd'];
		    $info['cn'] 					= $row['forename'] . ' ' . $row['surname'];
		    $info['givenName'] 		= $row['forename'];
		    $info['sn'] 					= $row['surname'];
		    $info['mail'] 				= $row['email'];
		    $info['objectclass'] 	= 'inetOrgPerson';
		    

				//echo '<pre>';
				//print_r($info);
				//echo '</pre>';
	    	//if ($username=='admin1') {
		    //	echo ">>> $username".'<br />';
				//	echo '<pre>';
				//	print_r($row);
				//	echo '</pre>';
	    	//}

		    /* add data to ldap directory */
		    $distinguishedName = 'uid='.$username . ',ou=people,dc=example,dc=com';
		    //echo 'DN : '. $distinguishedName.'<br />';
		    $r = ldap_modify($ds, $distinguishedName, $info);
		    if (!$r) {
		    	trigger_error('Unable to modify entry in LDAP DB: '.$distinguishedName, E_USER_WARNING);
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
		    

				//echo '<pre>';
				//print_r($info);
				//echo '</pre>';
	    	if ($username=='Prof1' or $username=='library1') {
		    	echo ">>> $username".'<br />';
					echo '<pre>';
					print_r($row);
					echo '</pre>';
	    	}

				
		    /* add data to ldap directory */
		    $distinguishedName = 'uid='.$username . ',ou=people,dc=example,dc=com';
		    echo "$distinguishedName".'<br />';
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

	    $sr=ldap_search($ds, 'dc=example,dc=com', "uid=$username");

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
		    $distinguishedName = "uid=$username" . ',ou=people,dc=example,dc=com';
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
			    $distinguishedName = "uid=$username" . ',ou=people,dc=example,dc=com';
			    $r = ldap_add($ds, $distinguishedName, $info);
			    if (!$r) {
			    	trigger_error('Unable to insert entry into LDAP DB: '.$distinguishedName, E_USER_WARNING);
			    }
		    } 
			} 
		}
  ldap_close($ds);
	}
} else {
  trigger_error('Unable to connect to LDAP server', E_USER_WARNING);
} 
?>
