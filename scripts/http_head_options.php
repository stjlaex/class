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
if($_SESSION['uid']==0){exit;}
require_once('../../lib/include.php');
require_once('../../logbook/permissions.php');
$respons=getRespons($_SESSION['username']);
if(isset($_GET['uniqueid'])){$xmlid=$_GET['uniqueid'];}
elseif(isset($_POST['uniqueid'])){$xmlid=$_POST['uniqueid'];}
?>
