<?php
/**								login_action.php
 *
 */
	require_once('../../dbh_connect.php');
	require_once('../../school.php');
	require_once('../classdata.php');
	require_once('session.php');
	$db=db_connect();
	mysql_query("SET NAMES 'utf8'");
	start_class_phpsession();
	if(isset($_SESSION['lang'])){$langchoice=$_SESSION['lang'];}else{$langchoice='';}
	if(isset($_POST['lang'])){$langchoice=$_POST['lang'];$_SESSION['lang']=$langchoice;}
	$username=$_POST['username'];
	$password=$_POST['password'];
	if(!isset($remember)){$remember=false;}
	include('authenticate.php');
	$date=gmdate("'Y-m-d'");
	$user=new user($db);
	$user->_checkLogin($username, $password, $remember);
	if($_SESSION['logged']!==true){session_defaults();}
	require_once('../lib/include.php');
	if($langchoice!=''){update_user_language($langchoice);}
	$uid=$_SESSION['uid'];
	$ip=$_SERVER['REMOTE_ADDR'];
	$browser=$_SERVER['HTTP_USER_AGENT'];
	mysql_query("INSERT INTO history SET uid='$uid', page='login.php',classis_version='$CFG->version',browser_version='$browser',ip='$ip';");
?>
<script>self.location.href = '../logbook.php'</script>
