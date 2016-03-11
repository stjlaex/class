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

	if($error or !isset($photo)){
		$photo=$blank_photo;
		}

	return $photo;
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
