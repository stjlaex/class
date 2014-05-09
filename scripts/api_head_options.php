<?php
$result=array();
$errors=array();
require_once('../../../dbh_connect.php');
require_once('../../../school.php');
require_once('../../classdata.php');
require_once('../../logbook/session.php');
include('../../../lib/functions.php');
include('../../../lib/fetch_student.php');
include('../../../lib/ldap.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");
require_once('../../logbook/authenticate.php');
$user=new user($db);

	/*TODO: different token for each user*/
	$username=$_GET['username'];
	$token=$_GET['password'];
	$salt='1234';
	$secret=md5($salt);
	$usertoken=md5(strtolower($username) . $secret);
	if($token!=$usertoken){
		$errors[]=print_string('invalidauthentication','admin');
		require('../../scripts/api_end_options.php');
		exit;
		}
	$ip=$_SERVER["REMOTE_ADDR"];
	$device='Test api';
	$uid='457';

require_once('../../lib/include.php');
require_once('../../logbook/permissions.php');

function api_log_to_history($uid,$action,$device,$ip){
	mysql_query("INSERT INTO history (uid,page,time,classis_version,browser_version,ip) 
						VALUES ('$uid', '$action',CURRENT_TIMESTAMP,'api','$device','$ip');");
	}

?>
