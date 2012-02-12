#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/message_text_event_cron.php
 *
 */

$book='infobook';
$current='message_text_event_cron.php';

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

/* CFG->smslib needs to be set depending on your provider */
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/'.$CFG->smslib); 


$send_limit=50;
$queue_offset=0;
$send_attempts=2;


/**
 * To ensure we don't get a race condition the report_event is touched
 * to update the timestamp and each cron will limit the reports it
 * processes by the age of the event and only process a batch of ten at a time.
 *
 * The age limit also prevents the queue being swamped with retries of any failures.
 *
 */
	$agelimit=6;//in minutes
	$d_e=mysql_query("SELECT id, phonenumber, textbody FROM message_text_event 
					WHERE success='0' AND time + interval $agelimit minute < now()  AND try < 2 LIMIT 80;");
	$d_u=mysql_query("UPDATE message_text_event SET success='0' 
					WHERE success='0' AND time + interval $agelimit minute < now() AND try < 2 LIMIT 80;");

	while($send=mysql_fetch_array($d_e,MYSQL_ASSOC)){
		$success=true;
		$messid=$send['id'];
		$result_xml=peticionCURL($send['phonenumber'],$send['textbody']);
		
		if($result_xml==-1){
			$success=false;
			}


		if($success){
			/* Mark the event table as succesful. */
			mysql_query("UPDATE message_text_event SET success='1', time=NOW(), try=try+1 WHERE id='$messid';");
			trigger_error('SMS text sent to: '.$send['phonenumber'],E_USER_WARNING);
			}
		else{
			mysql_query("UPDATE message_text_event SET success='0', time=NOW(), try=try+1 WHERE id='$messid';");
			$d_r=mysql_query("SELECT try FROM message_text_event WHERE id='$messid';");
			if(mysql_result($d_r,0)>2){
				$messagesubject=$CFG->clientid.': SMS message event failed.';
				send_email_to('support@'.$CFG->support,'',$messagesubject,$messagesubject,$messagesubject);
				}
			trigger_error($messagesubject,E_USER_WARNING);
			}


		}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>