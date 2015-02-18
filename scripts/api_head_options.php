<?php

$result=array();
$errors=array();
require_once('../../../dbh_connect.php');
require_once('../../../school.php');

if($_SERVER['HTTP_REFERER']!="https://classis.learningdata.net/api.php" and $CFG->debug!='dev'){
	$errors[]="Invalid URL.";
	require('../../scripts/api_end_options.php');
	exit;
	}

$CFG->dirroot=$CFG->installpath.'/'.$CFG->theme20;
require_once('../../lib/include.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");

function register($username,$device,$ip,$register_status,$last_use='',$expire=''){
	$success=false;
	//$d_a=mysql_query("SELECT id FROM api WHERE username='$username' AND device='$device';");
	$d_a=mysql_query("SELECT id FROM api WHERE username='$username';");
	if(mysql_num_rows($d_a)==0){
		$register_timestamp='CURRENT_TIMESTAMP';
		if($expire==''){$expire='NOW() + INTERVAL 65 DAY';}
		mysql_query("INSERT INTO api (username,device,register_status,register_timestamp,token,last_use,expire,ip) 
							VALUES ('$username', '$device','$register_status',$register_timestamp,'','$last_use',$expire,'$ip');");
		$register_id=mysql_insert_id();
		$d_t=mysql_query("SELECT register_timestamp FROM api WHERE id='$register_id';");
		if(mysql_num_rows($d_t)>0){
			$timestamp=mysql_result($d_t,0,'register_timestamp');
			$token=generateToken($username,$timestamp);
			mysql_query("UPDATE api SET token='$token' WHERE id='$register_id';");
			$success=true;
			}
		}
	return $success;
	}

function api_log_to_history($uid,$log,$device,$ip){
	mysql_query("INSERT INTO history (uid,page,time,classis_version,browser_version,ip) 
						VALUES ('$uid', '$log',CURRENT_TIMESTAMP,'api','$device','$ip');");
	mysql_query("UPDATE api JOIN users ON users.uid=$uid SET last_use=NOW(), api.ip='$ip' WHERE api.username=users.username;");
	}

function generateToken($username,$salt){
	if($username!='' and $salt!=''){
		$secret=md5($salt);
		$token=md5(strtolower($username).$secret);
		return $token;
		}
	return false;
	}

function generateCode($token,$codelength=4){
	$tokenlength=strlen($token);
	if($tokenlength<=$codelength){return $token;}
	if($codelength>0){
		$start=rand(0,$tokenlength-$codelength);
		$code=substr($token,$start,$codelength);
		}
	return $code;
	}

function checkToken($username,$token,$device=''){
	if($username!='' and $token!=''){
		if($device!=''){$device=" AND device='$device' ";}
		$d_a=mysql_query("SELECT register_status,register_timestamp,expire,token FROM api WHERE username='$username' LIMIT 1;");
		$register_status=mysql_result($d_a,0,'register_status');
		if($register_status){
			$register_time=mysql_result($d_a,0,'register_timestamp');
			$expire=mysql_result($d_a,0,'expire');
			$dbtoken=mysql_result($d_a,0,'token');
			$checktoken=generateToken($username,$register_time);
			if($token==$dbtoken and $token==$checktoken){return true;}
			}
		}
	return false;
	}


	if(isset($_GET['action']) and $_GET['action']!=''){$action=$_GET['action'];}else{$action='';}
	if(isset($_GET['username']) and $_GET['username']!=''){$username=$_GET['username'];}else{$username='';}
	if(isset($_GET['token']) and $_GET['token']!=''){$token=$_GET['token'];}else{$token='';}
	if(isset($_POST['ip']) and $_POST['ip']!=''){$ip=$_POST['ip'];}else{$ip='';}
	if(isset($_POST['device']) and $_POST['device']!=''){$device=$_POST['device'];}else{$device='';}

	if(isset($_POST['epfusername']) and $_POST['epfusername']!=''){$epfusername=$_POST['epfusername'];}else{$epfusername='';}
	if(isset($_POST['studentid']) and $_POST['studentid']!=''){$sid=$_POST['studentid'];}else{$sid='';}

	$today=date('Y-m-d');

	$d_u=mysql_query("SELECT uid FROM users WHERE username='$username';");
	$uid=mysql_result($d_u,0,'uid');
	$curryear=get_curriculumyear();

	$checktoken=checkToken($username,$token);
	if(!$checktoken and $action!='register'){
		$errors[]="Invalid authentication.";
		require('../../scripts/api_end_options.php');
		exit;
		}
	/*elseif($checktoken and $action=='authenticate'){
		$result['success']=true;
		require('../../scripts/api_end_options.php');
		exit;
		}*/

require_once('../../logbook/permissions.php');

?>
