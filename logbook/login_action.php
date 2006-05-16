<?php
/**								login_action.php
 *
 */
	require_once('../../dbh_connect.php');
	require_once('../../school.php');
	require_once('../classdata.php');
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	$session='ClaSS'.$CFG->shortname;
	session_name("$session");
	ini_set('globals','off');
	session_start();
	$langchoice=$_SESSION['lang'];
	$username=$_POST['username'];
	$password=$_POST['password'];
	if(!isset($remember)){$remember=false;}
	include('authenticate.php');
	$date=gmdate("'Y-m-d'");
	$user=new user($db);
	$user->_checkLogin($username, $password, $remember);
	if($_SESSION['logged']==false){session_defaults();}
	require_once('../lib/include.php');
	if($langchoice!=''){update_user_language($langchoice);}
?>
<script>self.location.href = '../logbook.php'</script>


