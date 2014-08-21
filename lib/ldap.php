<?php
/**
 *											ldap.php
 *
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2011
 *	@version
 *	@since
 */


/** 
 *
 * @param string $epfu ePortfolio user name
 *
 * @return resource
 */
function get_student_photo($epfu,$enrolno,$size=''){
	global $CFG;
	$s_base_tree_node='ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	return get_photo($epfu,$enrolno,$s_base_tree_node,$size);
	}



/**
 *
 * @param string $userid user name
 *
 * @return resource
 */
function get_user_photo($epfu,$size=''){
	global $CFG;
	$u_base_tree_node='ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	return get_photo($epfu,-1,$u_base_tree_node,$size);
	}



/**
 *
 * $base_tree_node: default: 'ou=student,ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 *                        or 'ou=people,dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
 *
 * @param string $epfu eportfolio user name
 * @param string $enrolno alternative unique id no for students only
 * @param string $base_tree_node base node for searching people
 *
 * @return resource
 */
function get_photo($epfu,$enrolno,$base_tree_node=null,$size=''){

    global $CFG;
	$blank_photo=$CFG->installpath.'/'.$CFG->applicationdirectory.'/images/blank_profile.jpeg';
	$error=false;

	//Creates a miniature or displays it
	if(($size=='mini' or $size=='midi' or $size=='maxi') and $epfu!=''){
		$cached_photo=get_photo_miniature($epfu,$size);
		if(file_exists($cached_photo)){
			$photo=$cached_photo;
			}
		}

	/* First try for a cached file named with epfusername */
	if($epfu!='' and !isset($photo)){
		$cached_photo=$CFG->eportfolio_dataroot.'/cache/images/'.$epfu.'.jpeg';
		if(file_exists($cached_photo)){$photo=$cached_photo;}
		}

	/* Then try for a file manually uploaded to the cache with enrolno (only valid for students!) */
	/* TODO: remove enrolno option as should be deprecated! */
	if(!isset($photo) and $enrolno!='' and $enrolno!='-1'){
		$cached_photo=$CFG->eportfolio_dataroot.'/cache/images/'.$enrolno.'.jpeg';
		if(file_exists($cached_photo)){$photo=$cached_photo;}
		}

	/* Try and fetch the photo from epfdata repository */
	if(!isset($photo) and $epfu!=''){
		$stored_photo=$CFG->eportfolio_dataroot.'/icons/' . substr($epfu,0,1) . '/' . $epfu.'/'.$epfu.'.jpeg';
		if(file_exists($stored_photo)){$photo=$stored_photo;}
		}


	/* Last try and fetch photo from the LDAP */
	if(!isset($photo) and !empty($CFG->ldapserver)){
		$ldap_host=$CFG->ldapserver;
		$ldap_rdn ='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
		$ldap_pass=$CFG->ldappasswd;
		$search_filter= '( & (objectClass=inetOrgPerson) (uid='.$epfu.') )';
		if(is_null($base_tree_node)){
		    $base_tree_node='ou=people,dc=example,dc=com';
			}

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
		else{
			$search_result=ldap_search( $ldap_connection, $base_tree_node, $search_filter, array( 'jpegPhoto' ));
			if($search_result){
				$entry=ldap_first_entry( $ldap_connection, $search_result );

				if(!$entry){
					trigger_error('LDAP: user id: '.$epfu.', not found! ', E_USER_WARNING);
					$error=true;
					}
				else{
					$attrs=ldap_get_attributes($ldap_connection, $entry);
					if($attrs['jpegPhoto']['count']>0){
						$jpeg_data=ldap_get_values_len( $ldap_connection, $entry, "jpegPhoto");
						$outfile=$CFG->eportfolio_dataroot.'/cache/images/'.$epfu.'.jpeg';
						$handle=fopen($outfile, 'wb');
						fwrite($handle,$jpeg_data[0]);
						fclose($handle);
						$photo=$outfile;
						}
					}
				}
			}
		}

	if($error or !isset($photo)){
		$photo=$blank_photo;
		}

	return $photo;
	}




/**
 * The only required variables are: 
 *		$user_type: s=student, t=teachers & others (at the moment)
 *		$epfu (eportfolio user name, get student photo) or userid (get_user_photo)
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
 * @param string $epfu ePortfolio user name
 * @param string $s_ldap_host host:port
 * @param string $s_ldap_rdn ldap user authority
 * @param string $s_ldap_pass ldap user authority
 * @param string $s_base_tree_node base node for searching people
 * @param string $s_object_class 
 * @return resource
 */
function get_photo_small($user_type, $epfu, $s_photo_size, $s_ldap_host=null, $s_ldap_rdn=null, $s_ldap_pass=null, $s_base_tree_node=null, $s_object_class=null){
	global $CFG;
	if (strtolower($user_type)=='s') {
		$uid=$epfu;
		} else {
		$uid=$CFG->clientid.$epfu;
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
					$filename=get_photo($uid,-1,$s_base_tree_node);
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
 * @param string  $epfu ePortfolio user name
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




/**
 * 
 * Creates a miniature profile picture from the original photo
 *
 */
function set_photo_miniature($owner,$size){
	global $CFG;

	//files
	$file=$CFG->eportfolio_dataroot.'/icons/'.substr($owner,0,1).'/'.$owner.'/'.$owner.'.jpeg';
	$blank=$CFG->installpath.'/'.$CFG->applicationdirectory.'/images/'.'blank_profile.jpeg';

	//if there is no profile photo it gets a blank profile photo miniature
	if(!file_exists($file)){
		$file=$blank;
		$owner='blank';
		}
	$min=$CFG->eportfolio_dataroot.'/cache/images/'.$owner.'_'.$size.'.jpeg';

	//get the times of files to compare them
	$file_time=filemtime($file);
	$min_time=filemtime($min);

	//dimensions of the miniature
	if($size=='mini'){
		$new_width=40;
		$new_height=45;
		}
	elseif($size=='midi'){
		$new_width=80;
		$new_height=90;
		}
	elseif($size=='maxi'){
		$new_width=160;
		$new_height=180;
		}


	//if there is no miniature or there is a new profile photo it creates a new miniature
	if(!file_exists($min) or $file_time>=$min_time){
		list($width, $height)=getimagesize($file);
		$new_file=imagecreatetruecolor($new_width, $new_height);
		$image=imagecreatefromjpeg($file);
		imagecopyresampled($new_file, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagejpeg($new_file, $min, 100);
		chmod($min,0777);
		}
	}

/**
 * Search for a miniature profile picture
 *
 */
function get_photo_miniature($owner,$size){
	global $CFG;

	//filepath
	$file=$CFG->eportfolio_dataroot.'/icons/'.substr($owner,0,1).'/'.$owner.'/'.$owner.'.jpeg';
	if(!file_exists($file)){
		$owner='blank';
		}

	$min=$CFG->eportfolio_dataroot.'/cache/images/'.$owner.'_'.$size.'.jpeg';

	//get the times of files to compare them
	$file_time=filemtime($file);
	$min_time=filemtime($min);

	//if there is no miniature or there is a new profile photo it creates one
	if(!file_exists($min) or $file_time>=$min_time){
		set_photo_miniature($owner,$size);
		}

	//return the miniature path
	return $min;
	}

?>
