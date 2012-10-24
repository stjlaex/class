#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/student_event_cron.php
 *
 */

$book='reportbook';
$current='student_event_cron.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
    $ARGS = array();
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');


$d_a=mysql_query("SELECT id FROM assessment WHERE year='$year' 
						AND deadline='$todate' AND description='Progress';");

while($ass=mysql_fetch_array($d_a, MYSQL_ASSOC)){
	$eid=$ass['id'];
	$d_s=mysql_query("SELECT DISTINCT student_id FROM eidsid WHERE assessment_id='$eid' 
						AND COUNT(id)>2 AND value='-1';");
	while($eidsid=mysql_fetch_array($d_s, MYSQL_ASSOC)){
		$sid=$eidsid['student_id'];
		support_review($sid);
		}
	}

function support_review($sid){
	global $CFG;
	trigger_error($sid,E_USER_WARNING);
	return $success;
	}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>