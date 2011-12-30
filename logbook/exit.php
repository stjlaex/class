<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	require_once('session.php');
	start_class_phpsession();
	$past=time()-7200;
	foreach($_COOKIE as $key=>$value){
		setcookie($key, $value, $past,$CFG->sitepath);
		}
	session_unset();
	session_destroy();
	header("Location: ../../index.html");
?>
