<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	session_name("$session");
	session_start();
	session_unset();
	session_destroy();
	header("Location: ../../index.html");
?>
