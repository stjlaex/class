<?php
	require_once('../../school.php');
	require_once('../classdata.php');
	require_once('session.php');
	start_class_phpsession();
	kill_class_phpsession();
	header("Location: ../../index.html");
	exit;
?>
