<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	session_name("$session");
	session_start();
	setcookie($session, '', time() - 3600);
	setcookie('ClaSSsharedLogin', '', time() - 3600);
	session_unset();
	session_destroy();
	header("Location: ../../index.html");
?>
