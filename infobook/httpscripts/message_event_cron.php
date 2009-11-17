#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/message_event_cron.php
 *
 */

$book='reportbook';
$current='message_event_cron.php';

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

/* The PEAR library */
require_once 'Mail/Queue.php';

$db_options['type']='db';
$db_options['db']=$db;
$db_options['mail_table']='message_event';  

$mail_options['driver']='smtp';
$mail_options['host']=$CFG->smtphosts;
$mail_options['port']=25;
$mail_options['auth']=true;
$mail_options['username']=$CFG->smtpuser;
$mail_options['password']=$CFG->smtppasswd;

$queue=& new Mail_Queue($db_options, $mail_options);
$send_limit=50;
$queue_offset=0;
$send_attempts=10;
/* Reference:  
 * http://pear.php.net/package/Mail_Queue/docs/latest/Mail_Queue/Mail_Queue.html#methodsendMailsInQueue  
 */
$queue->sendMailsInQueue($send_limit, $queue_offset, $send_attempts);

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>