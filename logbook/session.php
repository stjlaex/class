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
	$session='ClaSS'.$CFG->shortname;
	ini_set('globals','off');
	ini_set('session.gc_probability',1);
	ini_set('session.gc_divisor',100);
	if(!empty($CFG->sessiontimeout)){ini_set('session.gc_maxlifetime',$CFG->sessiontimeout);}
	else{ini_set('session.gc_maxlifetime',7200);}
	session_save_path($CFG->eportfolio_dataroot .'/sessions');
	session_name("$session");
	session_cache_limiter('nocache');
	session_start();
	}
?>