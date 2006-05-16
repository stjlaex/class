<?php

function sync_users() {
    global $CFG ;

	$users=(array)auth_get_userlist();
	foreach($users as $user){
		$name=$user['username'];
		$sql = "SELECT * FROM users WHERE username=$name";
		//		$user=(array)auth_get_userinfo($name);
		}
	}

/**
 * syncronizes user fron external db to moodle user table
 *
 * Sync shouid be done by using idnumber attribute, not username.
 * You need to pass firstsync parameter to function to fill in
 * idnumbers if they dont exists in moodle user table.
 * 
 * Syncing users removes (disables) users that dont exists anymore in external db.
 * Creates new users and updates coursecreator status of users. 
 * 
 * @param mixed $firstsync  Optional: set to true to fill idnumber fields if not filled yet
 */

function auth_ldap_sync_users ($bulk_insert_records = 1000, $do_updates=1) {
//Syncronizes userdb with ldap
//This will add, rename 
/// OPTIONAL PARAMETERS
/// $bulk_insert_records = 1 // will insert $bulkinsert_records per insert statement
///                         valid only with $unsafe. increase to a couple thousand for
///                         blinding fast inserts -- but test it: you may hit mysqld's 
///                         max_allowed_packet limit.
/// $do_updates = 1 // will do pull in data updates from ldap if relevant


    global $CFG ;
    $pcfg = get_config('auth/ldap');

    // configure a temp table 
    print "Configuring temp table\n";    
	// help old mysql versions cope with large temp tables
	execute_sql('SET SQL_BIG_TABLES=1', false); 
	execute_sql('CREATE TEMPORARY TABLE ' . $CFG->prefix .'extuser (idnumber VARCHAR(64), PRIMARY KEY (idnumber)) TYPE=MyISAM',false); 

        
    print "connecting to ldap\n";
    $ldapconnection = auth_ldap_connect();

    ////
    //// get user's list from ldap to sql in a scalable fashion
    ////
    // prepare some data we'll need
    if(!empty($CFG->ldap_objectclass)){
        $CFG->ldap_objectclass="objectClass=*";
	    }

    $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";

    $contexts = explode(";",$CFG->ldap_contexts);
 
    if (!empty($CFG->ldap_create_context)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    $fresult = array();
    $count = 0;
    foreach ($contexts as $context) {
        $context = trim($context);
        if (empty($context)) {
            continue;
        }
        begin_sql();
        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,
                                       $filter,
                                       array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     array($CFG->ldap_user_attribute));
        }

        $entry = ldap_first_entry($ldapconnection, $ldap_result);
        do {
            $value = ldap_get_values_len($ldapconnection, $entry,$CFG->ldap_user_attribute);
            $value = $value[0];
            $count++;
            array_push($fresult, $value);
            if(count($fresult) >= $bulk_insert_records){
                auth_ldap_bulk_insert($fresult);
                //print var_dump($fresult);
                $fresult=array();
            }         
        }
        while ($entry = ldap_next_entry($ldapconnection, $entry));
        
        // insert any remaining users and release mem
        if(count($fresult)){
            ldap_bulk_insert($fresult);
            $fresult=array();
        }
        commit_sql();
    }
    // free mem
    $ldap_results = 0;

    /// preserve our user database
    /// if the temp table is empty, it probably means that something went wrong, exit
    /// so as to avoid mass deletion of users; which is hard to undo
    $count = get_record_sql('SELECT COUNT(idnumber) AS count, 1 FROM ' . $CFG->prefix .'extuser');
    $count = $count->{'count'};
    if($count < 1){
        print "Did not get any users from LDAP -- error? -- exiting\n";
        exit;
    }

    ////
    //// User removal
    ////
    // find users in DB that aren't in ldap -- to be removed!
    // this is still not as scalable
    $sql = 'SELECT u.id, u.username 
            FROM ' . $CFG->prefix .'user u LEFT JOIN ' . $CFG->prefix .'extuser e 
                    ON u.idnumber = e.idnumber 
            WHERE u.auth=\'' . AUTH_LDAP_NAME . '\' AND u.deleted=\'0\' AND e.idnumber IS NULL';
    //print($sql);            
    $remove_users = get_records_sql($sql); 

    if (!empty($remove_users)){
        print "User entries to remove: ". count($remove_users) . "\n";

        begin_sql();
        foreach ($remove_users as $user) {
            //following is copy pasted from admin/user.php
            //maybe this should moved to function in lib/datalib.php
            unset($updateuser);
            $updateuser->id = $user->id;
            $updateuser->deleted = "1";
            //$updateuser->username = "$user->username".time();  // Remember it just in case
            //$updateuser->email = "";               // Clear this field to free it up
            $updateuser->timemodified = time();
            if (update_record("user", $updateuser)) {
                unenrol_student($user->id);  // From all courses
                remove_teacher($user->id);   // From all courses
                remove_admin($user->id);
                notify(get_string("deletedactivity", "", fullname($user, true)) );
            } else {
                notify(get_string("deletednot", "", fullname($user, true)));
            }
            //copy pasted part ends
        }     
        commit_sql();
    } 
    $remove_users = 0; // free mem!   

    ////
    //// User Updates
    //// (time-consuming, optional)
    ////
    if ($do_updates) {
        // narrow down what fields we need to update
        $all_keys = array_keys(get_object_vars($pcfg));
        $updatekeys = array();
        foreach ($all_keys as $key) {
            if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                // if we have a field to update it from
                // and it must be updated 'onlogin' we 
                // update it on cron
                if ( !empty($pcfg->{'field_map_'.$match[1]})
                     && $pcfg->{$match[0]} === 'onlogin') { 
                    array_push($updatekeys, $match[1]); // the actual key name
                }
            }
        }
        // print_r($all_keys); print_r($updatekeys);
        unset($all_keys); unset($key);
        
    }
    if ( $do_updates && !(empty($updatekeys)) ) { // run updates only if relevant
        $users = get_records_sql('SELECT u.username, u.id FROM ' . $CFG->prefix . 'user  AS u WHERE u.deleted=0 and u.auth=\'' . AUTH_LDAP_NAME . '\'' );
        if (!empty($users)) {
            print "User entries to update: ". count($users). "\n";
            
            

            begin_sql();
            $xcount=0; $maxxcount=100;
            foreach ($users as $user) { 
                echo "updating user $user->username \n";
                auth_ldap_update_user_record($user->username, $updatekeys);
                // update course creators
                if ( !empty($CFG->ldap_creators) && !empty($CFG->ldap_memberattribute) ) {
                    if (auth_iscreator($user->username)) {
                        if (! record_exists("user_coursecreators", "userid", $user->id)) {
                            $creator = insert_record("user_coursecreators",$user->id);
                            if (! $creator) {
                                error("Cannot add user to course creators.");
                        }
                      }
                    } else {
                         if ( record_exists("user_coursecreators", "userid", $user->id)) {
                              $creator = delete_records("user_coursecreators", "userid", $user->id);
                              if (! $creator) {
                                  error("Cannot remove user from course creators.");
                              }
                         }
                    }
                }
                if ($xcount++ > $maxxcount) {
                  commit_sql();
                  begin_sql(); 
                  $xcount=0;
                }
            }
            commit_sql();
            $users = 0; // free mem
        }
    } // end do updates
    
    ////
    //// User Additions
    ////
    // find users missing in DB that are in LDAP
    // note that get_records_sql wants at least 2 fields returned,
    // and gives me a nifty object I don't want.
    $sql = 'SELECT e.idnumber,1 
            FROM ' . $CFG->prefix .'extuser e  LEFT JOIN ' . $CFG->prefix .'user u
                    ON e.idnumber = u.idnumber 
            WHERE  u.id IS NULL OR (u.id IS NOT NULL AND u.deleted=1)';
    $add_users = get_records_sql($sql); // get rid of the fat        
    
    if(!empty($add_users)){
        print "User entries to add: ". count($add_users). "\n";
        begin_sql();
        foreach($add_users as $user){
            $user = auth_get_userinfo_asobj($user->idnumber);
            //print $user->username . "\n";
            
            // prep a few params
            $user->modified  = time();
            $user->confirmed = 1;
            $user->auth      = AUTH_LDAP_NAME;
            
            // insert it
            $old_debug=$CFG->debug; 
            $CFG->debug=10;
            
            // maybe the user has been deleted before
            if ($old_user = get_record('user', 'idnumber', $user->idnumber, 'deleted', 1)) {
                $user->id = $old_user->id;
                set_field('user', 'deleted', 0, 'idnumber', $user->idnumber);
                echo "Revived user $user->username with idnumber $user->idnumber id $user->id\n";
            } elseif ($id=insert_record ('user',$user)) { // it is truly a new user
                echo "inserted user $user->username with idnumber $user->idnumber id $id\n";
                $user->id = $id;
            } else {
                echo "error inserting user $user->username with idnumber $user->idnumber \n";
            }
            $CFG->debug=$old_debug;
            $userobj = auth_ldap_update_user_record($user->username);
            if(isset($CFG->{'auth_ldap_forcechangepassword'}) && $CFG->{'auth_ldap_forcechangepassword'}){
                set_user_preference('auth_forcepasswordchange', 1, $userobj->id);
            }
            
            // update course creators
            if ( !empty($CFG->ldap_creators) && !empty($CFG->ldap_memberattribute) ) {
                if (auth_iscreator($user->username)) {
                    if (! record_exists("user_coursecreators", "userid", $user->id)) {
                        $creator = insert_record("user_coursecreators",$user->id);
                        if (! $creator) {
                            error("Cannot add user to course creators.");
                    }
                  }
                } else {
                     if ( record_exists("user_coursecreators", "userid", $user->id)) {
                          $creator = delete_records("user_coursecreators", "userid", $user->id);
                          if (! $creator) {
                              error("Cannot remove user from course creators.");
                          }
                     }
                }
            }
        }
        commit_sql();
        $add_users = 0; // free mem
    }
    return true;
}

function auth_ldap_update_user_record($username, $updatekeys=false) {
/// will update a local user record from an external source. 
/// is a lighter version of the one in moodlelib -- won't do 
/// expensive ops such as enrolment
///
/// If you don't pass $updatekeys, there is a performance hit and 
/// values removed from LDAP won't be removed from moodle. 

    global $CFG;

    $pcfg = get_config('auth/ldap');

    //just in case check text case
    $username = trim(moodle_strtolower($username));
    
    // get the current user record
    $user = get_record('user', 'username', $username);
    if (empty($user)) { // trouble
        error_log("Cannot update non-existent user: $username");
        die;
    }

    if (function_exists('auth_get_userinfo')) {
        if ($newinfo = auth_get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);
            
            if (empty($updatekeys)) { // all keys? this does not support removing values
                $updatekeys = array_keys($newinfo);
            }
            
            foreach ($updatekeys as $key){
                unset($value);
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                    $value = addslashes(stripslashes($value)); // Just in case
                } else {
                    $value = '';
                }
                if (!empty($pcfg->{'field_updatelocal_' . $key})) { 
                       if ($user->{$key} != $value) { // only update if it's changed
                           set_field('user', $key, $value, 'username', $username);
                       }
                }
            }
        }
    }
    return get_record_select("user", "username = '$username' AND deleted <> '1'");
}

function auth_ldap_bulk_insert($users){
// bulk insert in SQL's temp table
// $users is an array of usernames
    global $CFG;
    
    // bulk insert -- superfast with $bulk_insert_records
    $sql = 'INSERT INTO '.$CFG->prefix.'extuser (idnumber) VALUES ';
    // make those values safe
    array_map('addslashes', $users);
    // join and quote the whole lot
    $sql = $sql . '(\'' . join('\'),(\'', $users) . '\')';
    print "+ " . count($users) . " users\n";
    execute_sql($sql, false); 

}

?>