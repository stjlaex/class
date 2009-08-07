#! /usr/bin/php -q
<?php
$result=array();
$error=array();
$starttime=time();
$fullpath=$CFG->installpath.'/'.$CFG->applicationdirectory;
require_once($CFG->installpath.'/dbh_connect.php');
require_once($fullpath.'/classdata.php');
require_once($fullpath.'/lib/include.php');
require_once($fullpath.'/logbook/permissions.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8';");
?>