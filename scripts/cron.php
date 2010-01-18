#! /usr/bin/php -q
<?php
/**
 *													cron.php
 *
 * To run this every 30 minutes you are probably best placing a file called class in
 * /etc/cron.d containing the single line: 
 *
 *         30 * * * *	www-data  /usr/bin/php  <fullpath>/scripts/cron.php  --path=<install-path>
 *
 * Where <install-path> is the same assigned to $CFG->installpath, in the script school.php
 * 
 * Make sure you have <fullpath> (which includes the application directory) correct for your install.
 *
 */

$current='cron.php';

/* The path is passed as a command line argument. */

function arguments($argv) {
    $ARGS = array();
    foreach ($argv as $arg) {
		if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);

require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');

$fullpath=$CFG->installpath.'/'.$CFG->applicationdirectory;

/**
 * Run every time.
 */


if(isset($CFG->emailsys) and $CFG->emailsys=='pearmail'){
	$cmd='/usr/bin/php '.$fullpath.'/infobook/httpscripts/message_event_cron.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}

/**
 * Run nightly only
 */
$latehour=date('H',$starttime);
$latemin=date('i',$starttime);
if($latehour>0 and $latehour<6){

	$cmd='/usr/bin/php '.$fullpath.'/reportbook/httpscripts/eportfolio_reports_publish.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");

	}

/**
 * Run once only (early morning)
 */
if($latehour==6 and $latemin<05){

	$cmd='/usr/bin/php '.$fullpath.'/admin/httpscripts/ldap_sync_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");

	}
if($latehour==7 and $latemin<05){

	//$cmd='/usr/bin/php '.$CFG->installpath.'/'.$CFG->applicationdirectory.'/admin/httpscripts/eportfolio_sync_users.php --path='.$CFG->installpath;
	//exec("$cmd > /dev/null &");

	$cmd='/usr/bin/php '.$CFG->installpath.'/'.$CFG->applicationdirectory.'/admin/httpscripts/ldap_enrol_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");

	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
