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

function arguments($argv){
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

if(!isset($ARGS['option'])){

	if($CFG->emailoff!='yes'){
		/* Run the mail queue */
		$cmd='/usr/bin/php '.$fullpath.'/infobook/httpscripts/message_event_cron.php --path='.$CFG->installpath;
		exec("$cmd > /dev/null &");
		}

	/**
	 * Run outside of peak registration times only.
	 */
	$latehour=date('H',$starttime);
	if(($latehour>=0 and $latehour<8) or $latehour>=10){
		/* Generate PDFs of reports queued for publication */
		$cmd='/usr/bin/php '.$fullpath.'/reportbook/httpscripts/eportfolio_reports_publish.php --path='.$CFG->installpath;
		exec("$cmd > /dev/null &");
		}
	}

/**
 * Run once only when specified directly from the cron line --option parameter
 */
elseif($ARGS['option']=='ldapsync'){
	/* Synchronise students, staff and contacts with LDAP */
	$cmd='/usr/bin/php '.$fullpath.'/admin/httpscripts/ldap_sync_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}
elseif($ARGS['option']=='dbsync'){
	/* Generate local students, staff and contacts epfusernames (only when NOT using ldap!) */
	$cmd='/usr/bin/php '.$fullpath.'/admin/httpscripts/db_sync_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}
elseif($ARGS['option']=='epfsync'){
	/* Update accounts for contacts in the ClaSSIC database */
	$cmd='/usr/bin/php '.$CFG->installpath.'/'.$CFG->applicationdirectory.'/admin/httpscripts/eportfolio_sync_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}
elseif($ARGS['option']=='ldapenrol'){
	/* Synchronise courses and enrolments in ldap for use by Moodle */
	$cmd='/usr/bin/php '.$CFG->installpath.'/'.$CFG->applicationdirectory.'/admin/httpscripts/ldap_enrol_users.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}
elseif($ARGS['option']=='hwsync'){
	/* Update accounts for contacts in the ClaSSIC database */
	$cmd='/usr/bin/php '.$CFG->installpath.'/'.$CFG->applicationdirectory.'/admin/httpscripts/epf_sync_homework.php --path='.$CFG->installpath;
	exec("$cmd > /dev/null &");
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>