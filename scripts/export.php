<?php
require_once('../../dbh_connect.php');
require_once('../../school.php');
require_once('../classdata.php');
require_once('../logbook/authenticate.php');
require_once('../logbook/session.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");
start_class_phpsession();
require_once('../logbook/authenticate.php');
if(!isset($_SESSION['uid'])){session_defaults();} 
$user=new user($db);
if($_SESSION['uid']==0){exit;}
include('../lib/functions.php');
$ftype=clean_text($_GET['ftype']);
if($ftype=='csv'){$mimetype='csv';$filepath='/tmp/class_export.csv';}
elseif($ftype=='xml'){$mimetype='xml';$filepath='/tmp/class_export.xml';}
elseif($ftype=='fet'){$mimetype='xml';$filepath='/tmp/class_export.fet';}
elseif($ftype=='xls'){$mimetype='application/ms-excel';$filepath='/tmp/class_export.xls';}
else{exit;}
if(!file_exists($filepath)){exit;}
header("Content-type: text/$mimetype");
header("Content-disposition: attachment; filename=class_export.$ftype");
readfile($filepath);
?>
