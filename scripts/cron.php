#! /usr/bin/php -q
<?php
/**
 *													cron.php
 *
 * To run this every 30 minutes you are probably best placing a file called class in
 * /etc/cron.d containing the single line: 
 *
 *         30 *  * * *	www-data	/usr/bin/php fullpath/scripts/cron.php
 *
 * and make sure you have the fullpath correct for your install.
 */

$result=array();
$error=array();
$starttime=time();

$result=array();
$error=array();


$currentpath=getcwd();
require_once($currentpath.'/devclass/school.php');
$fullpath=$CFG->installpath.'/'.$CFG->applicationdirectory;

require_once($CFG->installpath.'/dbh_connect.php');
require_once($fullpath.'/classdata.php');
require_once($fullpath.'/lib/include.php');
require_once($fullpath.'/logbook/permissions.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8';");

include($fullpath.'/admin/httpscripts/ldap_sync_users.php');

include($fullpath.'/reportbook/httpscripts/eportfolio_reports_publish.php');


trigger_error('CRON RUN '.$starttime,E_USER_WARNING);

?>