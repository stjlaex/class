<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	session_name("$session");
	session_start();
	$past=time()-7200;
	foreach($_COOKIE as $key=>$value){setcookie($key, $value, $past, '/' );}
	session_unset();
	session_destroy();
	header("Location: ../../index.html");
?>
