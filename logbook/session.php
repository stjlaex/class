<?php
/**
 *
 * Configure PHP session handling and start a session.
 *
 * The _SetSession function in autheticate must be run after this to
 * establish the session_id.
 *
 * Just to ensure sensible values this over rides the PHP defaults:
 * gc_probability is divided by gc_divisor which means 1/100
 *
 * The save path will in sessions directory under the eportfolio_dataroot.
 *
 */
function start_class_phpsession(){
	global $CFG;
	$sessionname='ClaSS'.$CFG->shortname;
	$path=$CFG->sitepath;
	$domain=$_SERVER['SERVER_NAME'];
	$secure=isset($_SERVER['HTTPS']);

	ini_set('globals','off');
	ini_set('session.gc_probability',1);
	ini_set('session.gc_divisor',100);
	if(!empty($CFG->sessiontimeout)){ini_set('session.gc_maxlifetime',$CFG->sessiontimeout);}
	else{ini_set('session.gc_maxlifetime',7200);}
	session_save_path('0;640;'.$CFG->eportfolio_dataroot .'/sessions');
	session_name("$sessionname");
	session_cache_limiter('nocache');
	/* session_set_cookie_params ($lifetime,$path,$domain,$secure,$httponly) */
	session_set_cookie_params(0, $path, $domain, $secure, true);
	session_start();
	}
/**
 *
 * Close the PHP session and cookies.
 *
 */
function kill_class_phpsession(){
	global $CFG;
	$sessionname='ClaSS'.$CFG->shortname;
	$path=$CFG->sitepath;
	$past=time()-7200;
	foreach($_COOKIE as $key=>$value){
		setcookie($key, $value, $past,$path);
		}
	session_unset();
	session_destroy();
	}
?>
