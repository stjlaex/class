<?php 
/**								  		ldap_test.php
 *
 *
 *
 */

$choice='ldap_test.php';
$action='ldap_test_action.php';

require_once('../classdev/logbook/permissions.php');

// Make sure of right LDAP version is being used
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

// Connect to LDAP server
$ds = ldap_connect("localhost");

if ($ds) {
	// Bind to LDAP DB
  $bind_result = ldap_bind($ds, "cn=admin,dc=example,dc=com", "yqz3350");
	
	if ($bind_result) {
		//
		// Get all users (teachers) from ClaSS
		//
		$users = list_all_users('0');

		// process result
		foreach($users as $uid => $row) {

			// display 1 row (just for debugging)
			trigger_error($row['uid'].' - '
										. $row['username'].' - '
										. $row['passwd'].' - '
										. $row['forename'].' - '
										. $row['surname'].' - '
										. $row['email'], E_USER_WARNING);

			
			$info = array();
			
			// search ldap uid
			
	    // Search for entry
			$username=$row['username'];
	    $sr=ldap_search($ds, 'dc=example,dc=com', "uid=$username");
	    trigger_error('Search result is: uid=' . $username, E_USER_WARNING);
	    trigger_error('Number of LDAP entries returned is ' . ldap_count_entries($ds, $sr), E_USER_WARNING);

			// When the entry exists, there isn't any insertion into LDAP DB	    
	    if (ldap_count_entries($ds, $sr) > 0) {
		    trigger_error('Getting entries ...', E_USER_WARNING);
		    $info = ldap_get_entries($ds, $sr);
	  		trigger_error('Data for ' . $info['count'] . ' items returned: ', E_USER_WARNING);
										       
		    for ($i=0; $i<$info['count']; $i++) {
	  			trigger_error(
							'dn is: ' . $info[$i]['dn'] . '<br />'
							.'first cn entry is: ' . $info[$i]['cn'][0] . '<br />'
							.'first email entry is: ' . $info[$i]['mail'][0] . '<br /><hr />'
							, E_USER_WARNING);
		    } // end for
	    } else {
				// OK, the entry does not exist so insert it into LDAP DB	    

		    // prepare data -in LDIF format- for LDAP insertion into DB
		    $info['uid'] 					= $row['username'];
		    $info['userPassword'] = $row['passwd'];
		    $info['cn'] 					= $row['forename'] . ' ' . $row['surname'];
		    $info['sn'] 					= $row['surname'];
//		    $info['mail'] 				= $row['email'];
		    $info['objectclass'] 	= 'inetOrgPerson';

/*
			// display 1 row (just for debugging)
			trigger_error($info['uid'].' | '
										. $info['username'].' | '
										. $info['passwd'].' | '
										. $info['forename'].' | '
										. $info['surname'].' | '
										. $info['email'], E_USER_WARNING);
*/

		    // add data to ldap directory
  			trigger_error('Add data to ldap directory', E_USER_WARNING);
		    $distinguishedName = 'uid='.$username . ',ou=people,dc=example,dc=com';
  			trigger_error('Distinguished name: ' . $distinguishedName, E_USER_WARNING);
		    $r = ldap_add($ds, $distinguishedName, $info);
		    //$r = ldap_modify($ds, $distinguishedName, $info);
  			trigger_error('Done.', E_USER_WARNING);
	    } // end if
		} // end foreach
	// Unbind from LDAP DB
  ldap_close($ds);
	} // end if
	
} else {
  trigger_error('Unable to connect to LDAP server', E_USER_WARNING);
} // end if
?>
