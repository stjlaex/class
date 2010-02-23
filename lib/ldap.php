<?php
/**
 *											ldap.php
 *
 * The only required variable is: $epfun (eportfolio name, get student photo) or userid (get_user_photo)
 *
 * The following three variables work all together. Or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 * 
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2; 
 *                        or 'ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 */

function get_student_photo($epfun, $s_ldap_host=null, $s_ldap_rdn=null, $s_ldap_pass=null, $s_base_tree_node=null, $s_object_class=null){
	global $CFG;
	$sid=$epfun;
	if(!$s_base_tree_node){
		$s_base_tree_node='ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		}
	return get_photo($sid, $s_ldap_host, $s_ldap_rdn, $s_ldap_pass,$s_base_tree_node, $s_object_class);
	}

function get_user_photo($userid, $u_ldap_host=null, $u_ldap_rdn=null, $u_ldap_pass=null, $u_base_tree_node=null, $u_object_class=null){
	global $CFG;
	$uid=$CFG->clientid.$userid;
	if(!$u_base_tree_node){
		$u_base_tree_node='ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		}	
	return get_photo($uid, $u_ldap_host, $u_ldap_rdn, $u_ldap_pass, $u_base_tree_node, $u_object_class);
	}

function get_photo($uid, $ldap_host=null, $ldap_rdn=null, $ldap_pass=null, $base_tree_node=null, $object_class=null){
	
    global $CFG;
    
    if(is_null($ldap_host)){
        $ldap_host=$CFG->ldapserver;
        $ldap_rdn ='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
        $ldap_pass=$CFG->ldappasswd;

        $ldap_connection=ldap_connect($ldap_host);
        if(!ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3)){
			trigger_error('Failed to set protocol version to 3', E_USER_WARNING);
			$error=true;
			}        
        $ldapbind=ldap_bind($ldap_connection,$ldap_rdn,$ldap_pass );
        if(!$ldapbind){
			trigger_error('Unable to bind', E_USER_WARNING);
			$error=true;
			}
		} 
	else{
        $ldap_connection=ldap_connect($ldap_host);
        if(!ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3)){
			trigger_error('Failed to set protocol version to 3', E_USER_WARNING);
			$error=true;
			}
        $ldapbind=ldap_bind($ldap_connection,$ldap_rdn,$ldap_pass );
        if(!$ldapbind){ 
			trigger_error('Unable to bind', E_USER_WARNING);
			$error=true;
			}
		}
    if(is_null($base_tree_node)){
        $base_tree_node='ou=people,dc=example,dc=com';
		}
    if(is_null($object_class)){
        $search_filter= '( & (objectClass=inetOrgPerson) (uid='.$uid.') )';
		} 
	else{
        $search_filter= '( & (objectClass='.$object_class.') (uid='.$uid.') )';
		}
    $search_result=ldap_search( $ldap_connection, $base_tree_node, $search_filter, array( 'jpegPhoto' ));
    if($search_result){
        $entry=ldap_first_entry( $ldap_connection, $search_result );
        if(!$entry){
			trigger_error($uid.' not found! ', E_USER_WARNING);
			$error=true;
			}
        $attrs=ldap_get_attributes($ldap_connection, $entry);
        if($attrs['count']>0){
            $jpeg_data=ldap_get_values_len( $ldap_connection, $entry, "jpegPhoto");
            $outfile=$CFG->installpath.'/images/tmp/'.$uid.'.jpeg';
            $handle=fopen($outfile, 'wb');
       		fwrite($handle,$jpeg_data[0]);
            fclose($handle);
            $photo=$CFG->siteaddress.$CFG->sitepath.'/images/tmp/'.$uid.'.jpeg';
			} 
		else{
            $photo=$CFG->siteaddress.$CFG->installpath.'/images/blank.jpeg';
			}
		}
	
	if($error){
		$photo=$CFG->siteaddress.$CFG->installpath.'/images/blank.jpeg';
		}
	$uid_image='<div class="icon"><img src="http://'.$photo.'" /></div>';
	return $uid_image;
	}
?>