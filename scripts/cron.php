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
 * Make sure you have the fullpath correct for your install.
 *
 */

$current='cron.php';
$currentpath=getcwd();
require_once($currentpath.'/devclass/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');


/**
 * Run every time.
 */

//$cmd='/usr/bin/php '.$fullpath.'/infobook/httpscripts/message_event_cron.php --path='.$CFG->installpath;
//exec("$cmd > /dev/null &");

$cmd='/usr/bin/php '.$fullpath.'/reportbook/httpscripts/eportfolio_reports_publish.php --path='.$CFG->installpath;
exec("$cmd > /dev/null &");


/**
 * Run nightly only.
 */
$latehour=date('H',$starttime);
if($latehour>0 and $latehour<6){

	//$cmd='/usr/bin/php '.$fullpath.'/admin/httpscripts/eportfolio_sync_cron.php --path='.$CFG->installpath;
	//exec("$cmd > /dev/null &");

	$cmd='/usr/bin/php '.$fullpath.'/admin/httpscripts/ldap_sync_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");

	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
