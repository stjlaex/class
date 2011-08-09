<?php
$result=array();
$error=array();
require_once('../../../dbh_connect.php');
require_once('../../../school.php');
require_once('../../classdata.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");
session_name("$session");
session_cache_limiter('nocache');
session_start();
require_once('../../logbook/authenticate.php');
if(!isset($_SESSION['uid'])){session_defaults();} 
$user=new user($db);
if($_SESSION['uid']==0){
	$username=$_GET['username'];
	$token=$_GET['password'];
	//$ip=$_SERVER['SERVER_ADDR'];
	$salt=$CFG->eportfolioshare;
  	$secret=md5($salt . $ip);
	$guess=md5(strtolower($username) . $secret);
	if($token==$guess){
		//trigger_error('SUCCESS!!! '.$username,E_USER_WARNING);
		$_SESSION['username']=$username;
		}
	else{
		//trigger_error('FAILED!!! '.$username,E_USER_WARNING);
		session_defaults();
		exit;
		}
	}
require_once('../../lib/include.php');
require_once('../../logbook/permissions.php');
$respons=$_SESSION['respons'];
if(isset($_GET['uniqueid'])){$xmlid=$_GET['uniqueid'];}
elseif(isset($_POST['uniqueid'])){$xmlid=$_POST['uniqueid'];}
?>
