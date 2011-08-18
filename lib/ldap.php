<?php
/**
 *											ldap.php
 *
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version
 *	@since
 */


/** 
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
 *
 * @param string $epfun ePortfolio user name
 * @param string $s_ldap_host host:port
 * @param string $s_ldap_rdn ldap user authority
 * @param string $s_ldap_pass ldap user authority
 * @param string $s_base_tree_node base node for searching people
 * @param string $s_object_class 
 * @return resource
 */
function get_student_photo($epfun, $s_ldap_host=null, $s_ldap_rdn=null, $s_ldap_pass=null, $s_base_tree_node=null, $s_object_class=null){
	global $CFG;
	if(!$s_base_tree_node){
		$s_base_tree_node='ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		}
	return get_photo($epfun, $s_ldap_host, $s_ldap_rdn, $s_ldap_pass,$s_base_tree_node, $s_object_class);
	}




/**
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
 *
 * @param string $userid user name
 * @param string $u_ldap_host host:port
 * @param string $u_ldap_rdn ldap user authority
 * @param string $u_ldap_pass ldap user authority
 * @param string $u_base_tree_node base node for searching people
 * @param string $u_object_class 
 * @return resource
 */
function get_user_photo($userid, $u_ldap_host=null, $u_ldap_rdn=null, $u_ldap_pass=null, $u_base_tree_node=null, $u_object_class=null){
	global $CFG;
	$uid=$CFG->clientid.$userid;
	if(!$u_base_tree_node){
		$u_base_tree_node='ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		}
	return get_photo($uid, $u_ldap_host, $u_ldap_rdn, $u_ldap_pass, $u_base_tree_node, $u_object_class);
	}



/**
 * The only required variable is: $uid
 *
 * The following three variables work all together. Or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 *
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 *                        or 'ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 *
 * @param string $uid user id
 * @param string $ldap_host host:port
 * @param string $ldap_rdn ldap user authority
 * @param string $ldap_pass ldap user authority
 * @param string $base_tree_node base node for searching people
 * @param string $object_class 
 * @return resource
 */
function get_photo($uid, $ldap_host=null, $ldap_rdn=null, $ldap_pass=null, $base_tree_node=null, $object_class=null){

    global $CFG;

	$error=false;

	$cached_photo=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'.jpeg';
	$blank_photo=$CFG->installpath.'/'.$CFG->applicationdirectory.'/images/blank_profile.jpeg';

	if(file_exists($cached_photo)){
		$photo=$cached_photo;
		}
	else{
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
				trigger_error('LDAP: user id: '.$uid.', not found! ', E_USER_WARNING);
				$error=true;
				}
			else{
				$attrs=ldap_get_attributes($ldap_connection, $entry);

				if($attrs['jpegPhoto']['count']>0){
				    $jpeg_data=ldap_get_values_len( $ldap_connection, $entry, "jpegPhoto");
				    $outfile=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'.jpeg';
				    $handle=fopen($outfile, 'wb');
			   		fwrite($handle,$jpeg_data[0]);
				    fclose($handle);
					$photo=$cached_photo;
					}
				else{
					$photo=$blank_photo;
					}
				}
			}

		if($error){
			$photo=$blank_photo;
			}
		}

	return $photo;
	}



/**
 * The only required variables are: 
 *		$epfun (eportfolio name, get student photo) or userid (get_user_photo)
 *		$s_photo_size: 2 or 3
 *			2 means: 35.0%, 
 *			3 menas: 25.0%		
 *			these percentages have been harcoded inside the function
 *
 * The following three variables work all together. Or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 * 
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2; 
 *                        or 'ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 *
 * @param string $epfun ePortfolio user name
 * @param string $s_photo_size 
 * @param string $s_ldap_host host:port
 * @param string $s_ldap_rdn ldap user authority
 * @param string $s_ldap_pass ldap user authority
 * @param string $s_base_tree_node base node for searching people
 * @param string $s_object_class 
 * @return resource
 */
function get_student_photo_small($epfun, $s_photo_size, $s_ldap_host=null, $s_ldap_rdn=null, $s_ldap_pass=null, $s_base_tree_node=null, $s_object_class=null){

	global $CFG;
	$uid=$epfun;
	
	if($s_photo_size==2 or $s_photo_size==3){
		$cached_thumb=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'_f'. $s_photo_size .'.jpeg';
		if(!file_exists($cached_thumb)){
		
		
			$cached_photo=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'.jpeg';
			if(file_exists($cached_photo)){
				$filename=$cached_photo;
				}
			else{
				
				//chequear si uid es inexistente
				if ( strlen( trim($uid) ) < 3 )  {
					$filename=$CFG->installpath.'/images/blank_profile.jpeg';
					trigger_error('### '.$filename,E_USER_WARNING);

					} else {
					$filename=get_photo($uid, $s_ldap_host, $s_ldap_rdn, $s_ldap_pass,$s_base_tree_node, $s_object_class);
					}
					
				}

			$percent=array(0.35,0.25);

			if(strpos($filename,'blank')){
				// when errors a blank image is shown
				$filename=$CFG->installpath.'/images/blank_profile.jpeg';
				$uid='blank_profile';
				}
			
			list($width, $height) = getimagesize($filename);
			for($i=0; $i<2; $i++) {
				$new_width = $width * $percent[$i];
				$new_height = $height * $percent[$i];
				$thumb = imagecreatetruecolor($new_width, $new_height);
				$source = imagecreatefromjpeg($filename);
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$n=$i+2;
				$outfile=$CFG->eportfolio_dataroot.'/cache/images/' . $uid .'_f'. $n .'.jpeg';
				if(file_exists($outfile)){
					unlink($outfile);
					}
				imagejpeg($thumb,$outfile);
				}
			}
		$cached_photo=$CFG->siteaddress.$CFG->sitepath.'/images/tmp/'.$uid.'_f'. $s_photo_size .'.jpeg';
		return $cached_photo;
		}
	}


/**
 * The only required variables are: 
 *		$user_type: s=student, t=teachers & others (at the moment)
 *		$epfun (eportfolio user name, get student photo) or userid (get_user_photo)
 *		$s_photo_size: 2 or 3
 *			2 means: 35.0%, 
 *			3 means: 25.0%		
 *			these percentages have been harcoded inside the function
 *
 * The following three variables work all together: or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 * 
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2; 
 *                        or 'ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 *
 * @param string $epfun ePortfolio user name
 * @param string $s_ldap_host host:port
 * @param string $s_ldap_rdn ldap user authority
 * @param string $s_ldap_pass ldap user authority
 * @param string $s_base_tree_node base node for searching people
 * @param string $s_object_class 
 * @return resource
 */
function get_photo_small2($user_type, $epfun, $s_photo_size, $s_ldap_host=null, $s_ldap_rdn=null, $s_ldap_pass=null, $s_base_tree_node=null, $s_object_class=null){
	global $CFG;
	if (strtolower($user_type)=='s') {
		$uid=$epfun;
		} else {
		$uid=$CFG->clientid.$epfun;
		}
	
	
	if($s_photo_size==2 or $s_photo_size==3){
		$cached_thumb=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'_f'. $s_photo_size .'.jpeg';
		if(!file_exists($cached_thumb)){
		
		
			$cached_photo=$CFG->eportfolio_dataroot.'/cache/images/'.$uid.'.jpeg';
			if(file_exists($cached_photo)){
				$filename=$cached_photo;
				}
			else{
				
				//chequear si uid es inexistente
				if ( strlen( trim($uid) ) < 3 )  {
					$filename=$CFG->installpath.'/images/blank_profile.jpeg';
					trigger_error('### '.$filename,E_USER_WARNING);

					} else {
					$filename=get_photo($uid, $s_ldap_host, $s_ldap_rdn, $s_ldap_pass,$s_base_tree_node, $s_object_class);
					}
					
				}

			$percent=array(0.35,0.25);

			if(strpos($filename,'blank')){
				// when errors a blank image is shown
				$filename=$CFG->installpath.'/images/blank_profile.jpeg';
				$uid='blank_profile';
				}
			
			list($width, $height) = getimagesize($filename);
			for($i=0; $i<2; $i++) {
				$new_width = $width * $percent[$i];
				$new_height = $height * $percent[$i];
				$thumb = imagecreatetruecolor($new_width, $new_height);
				$source = imagecreatefromjpeg($filename);
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$n=$i+2;
				$outfile=$CFG->eportfolio_dataroot.'/cache/images/' . $uid .'_f'. $n .'.jpeg';
				if(file_exists($outfile)){
					unlink($outfile);
					}
				imagejpeg($thumb,$outfile);
				}
			}
		$cached_photo=$CFG->siteaddress.$CFG->sitepath.'/images/tmp/'.$uid.'_f'. $s_photo_size .'.jpeg';
		return $cached_photo;
		}
	}


/**
 * The only required variables are: 
 *		$uid
 *		$photo
 *
 * The following three variables work all together. Or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 *
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 *
 * @param string $uid user id
 * @param string $photo absolute path to the photo
 * @param string $s_ldap_host host:port
 * @param string $s_ldap_rdn ldap user authority
 * @param string $s_ldap_pass ldap user authority
 * @param string $s_base_tree_node base node for searching people
 * @param string $s_object_class 
 * @return resource
 */
function set_photo($uid, $photo, $ldap_host=null, $lda_rdn=null, $ldap_pass=null, $base_tree_node=null, $object_class=null){

    global $CFG;

	$error=false;
	$msg=0;

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
		    $base_tree_node='ou=student,ou=people,dc=example,dc=com';
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
				trigger_error('user id: '.$uid.', not found! ', E_USER_WARNING);
				$error=true;
				}
			else{
				$attrs=ldap_get_attributes($ldap_connection, $entry);
								
				$ldap_info['jpegPhoto']=array();

				$timestamp=time();
				$rdname='uid='.$uid.',ou=student,ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$ldaprdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;

				$temp_path=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp;
				$outfname=$temp_path.'.ldif';
				$outfile = fopen($outfname,'w');
				$pic_number=$attrs['jpegPhoto']['count'];
				if($pic_number>0){
					// create temporary folder with all user's photos
					mkdir($temp_path, 0700);

					// generate ldif content
					// content for deleting attribute
			        $string='dn: '.$rdname.chr(10);
			        $string.='changetype: modify'.chr(10);
			        $string.='delete: jpegPhoto'.chr(10);
					
					// write the 1st part of the string
			        fwrite($outfile,$string);

					// copy all user's photos to the temporary folder
					for($i=0; $i<$pic_number; $i++) {
						$jpeg_name=$temp_path.'/img_'.$i.'.jpeg';
						$handle=fopen($jpeg_name,'wb');
						fwrite($handle,$attrs['jpegPhoto'][$i]);
						fclose($handle);
					}

					// ldif content for adding attribute
			        $string='-'.chr(10);
			        $string.='add: jpegPhoto'.chr(10);
			        
					// add the new photo definition to ldif
			        $string.='jpegPhoto:< file://'.$photo.chr(10);

					// put the temporary 'photo definitions' into ldif
					for($i=0; $i<$pic_number; $i++) {
						$jpeg_name=$temp_path.'/img_'.$i.'.jpeg';
						$string.='jpegPhoto:< file://'.$jpeg_name.chr(10);
					}
/*										
					// add the new photo definition to ldif
			        $string.='jpegPhoto:< file://'.$photo.chr(10);
*/
			        $string.=chr(10);
					// write the 2nd part of the string
			        fwrite($outfile,$string);

					}
				else{
					// prepare the string for ldif file
			        $string='dn: '.$rdname.chr(10);
			        $string.='changetype: modify'.chr(10);
			        $string.='add: jpegPhoto'.chr(10);
			        $string.='jpegPhoto:< file://'.$photo.chr(10);
			        $string.=chr(10);
					// write the string to ldif file
			        fwrite($outfile,$string);
					}
				// close ldif file		        
				fclose ($outfile);
				
				// prepare & run the line command
				$drfl1=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp;
				$drfl2=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp.'.ldif';
				//$line_cmd='/usr/bin/ldapmodify -v -x -w '.$CFG->ldappasswd.' -D '.$ldaprdn.' -f '.$outfname.';rm -R '.$drfl1.';rm '.$drfl2;
				$line_cmd='/usr/bin/ldapmodify -v -x -w '.$CFG->ldappasswd.' -D '.$ldaprdn.' -f '.$outfname;
				$output = shell_exec($line_cmd);
				
				$error=false;
				}
				
			}
			
		if($error){
			$msg=1;
			}
			
	return $msg;
	}

/**
 * The only required variables are: 
 *		$uid
 *		$photo_to_delete: a sequence number which is the natural order when
 *						reading the jpegPhoto attribute, starting with 0.
 *
 * The following three variables work all together. Or all of them have blank values, or haven't.
 * 		$ldap_host: ldaphost:port
 * 		$ldap_rdn: ldap user authority
 * 		$ldap_pass: ldap password authority
 *
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 * $object_class: default: inetOrgPerson
 *
 * @param string  $epfun ePortfolio user name
 * @param integer $photo_to_delete 
 * @param string  $s_ldap_host host:port
 * @param string  $s_ldap_rdn ldap user authority
 * @param string  $s_ldap_pass ldap user authority
 * @param string  $s_base_tree_node base node for searching people
 * @param string  $s_object_class 
 * @return resource
 */
function delete_photo($uid, $photo_to_delete, $ldap_host=null, $lda_rdn=null, $ldap_pass=null, $base_tree_node=null, $object_class=null){
 
    global $CFG;

	$error=false;
	$msg=0;

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
		    $base_tree_node='ou=student,ou=people,dc=example,dc=com';
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
				trigger_error('user id: '.$uid.', not found! ', E_USER_WARNING);
				$error=true;
				}
			else{
				$attrs=ldap_get_attributes($ldap_connection, $entry);
								
				$ldap_info['jpegPhoto']=array();

				$timestamp=time();
				$rdname='uid='.$uid.',ou=student,ou=people'.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$ldaprdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				$temp_path=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp;
				$outfname=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp.'.ldif';
				$outfile = fopen($outfname,'w');
				$pic_number=$attrs['jpegPhoto']['count'];
				if($pic_number>0){
					// create temporary folder with all user's photos
					mkdir($temp_path, 0700);

					// generate ldif content
					// content for deleting attribute
			        $string='dn: '.$rdname.chr(10);
			        $string.='changetype: modify'.chr(10);
			        $string.='delete: jpegPhoto'.chr(10);
					
					// write the 1st part of the string
			        fwrite($outfile,$string);

					// copy all user's photos to the temporary folder
					for($i=0; $i<$pic_number; $i++) {
						$jpeg_name=$temp_path.'/img_'.$i.'.jpeg';
						$handle=fopen($jpeg_name,'wb');
						fwrite($handle,$attrs['jpegPhoto'][$i]);
						fclose($handle);
					}

					// ldif content for adding attribute
					if ($pic_number>1) {
					    $string='-'.chr(10);
					    $string.='add: jpegPhoto'.chr(10);
			        }
			        
					// put the temporary 'photo definitions' into ldif
					for($i=0; $i<$pic_number; $i++) {
						if ($i==$photo_to_delete) {
							// do nothing
						} else {
							$jpeg_name=$temp_path.'/img_'.$i.'.jpeg';
							$string.='jpegPhoto:< file://'.$jpeg_name.chr(10);
						}
					}
					
					// write the 2nd part of the string
			        fwrite($outfile,$string);

					}
				else{
					// prepare the string for ldif file

					// content for deleting attribute
			        $string='dn: '.$rdname.chr(10);
			        $string.='changetype: modify'.chr(10);
			        $string.='delete: jpegPhoto'.chr(10);
								        
					// write the string to ldif file
			        fwrite($outfile,$string);
					}
				// close ldif file		        
				fclose ($outfile);
				
				// prepare & run the line command
				/*
				$drfl1=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp;
				$drfl2=$CFG->eportfolio_dataroot.'/cache/images/'.'import_'.$uid.'_'.$timestamp.'.ldif';
				$line_cmd='/usr/bin/ldapmodify -v -x -w '.$CFG->ldappasswd.' -D '.$ldaprdn.' -f '.$outfname.';rm -R '.$drfl1.';rm '.$drfl2;
				*/
				$line_cmd='/usr/bin/ldapmodify -v -x -w '.$CFG->ldappasswd.' -D '.$ldaprdn.' -f '.$outfname;
				$output = shell_exec($line_cmd);
				
				$error=false;
				}
				
			}
			
		if($error){
			$msg=1;
			}
			
	return $msg;
	}

?>
