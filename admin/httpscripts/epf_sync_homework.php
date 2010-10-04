#! /usr/bin/php -q
<?php
/**								epf_sync_homework.php
 *
 * Instead of insert homework live into the epf db when its written in the MarkBook.
 * This allows a cron script to run and update homeworks periodically.
 *
 * TODO: currently just does all homeworks and it needs a since date to be useful. 
 */

$book='admin';
$current='epf_sync_homework.php';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if (ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/eportfolio_functions.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/functions.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/lib/curriculum_functions.php');


	$d_m=mysql_query("SELECT entrydate AS datedue, midlist AS hwid, 
				topic AS title, author AS tid, comment AS 'dateset', midcid.class_id AS cid 
				FROM mark JOIN midcid ON midcid.mark_id=mark.id WHERE mark.marktype='hw';");

	while($m=mysql_fetch_array($d_m, MYSQL_ASSOC)){
		$hwid=$m['hwid'];
		$d_h=mysql_query("SELECT description, refs, subject_id, component_id FROM homework 
						WHERE id='$hwid';");
		trigger_error('HOME!!!!!!!!!!!!!!!'.mysql_error(),E_USER_WARNING);
		$hw=mysql_fetch_array($d_h, MYSQL_ASSOC);
		$body=$hw['description']. '<hr />'.$hw['refs']. 
			'<hr /> <p>Work set: '.display_date($m['dateset']). 
				'&nbsp;&nbsp;&nbsp; Work due by: '. display_date($m['datedue']).'</p><hr />';
		$subject=get_subjectname($hw['subject_id']);
		$component=get_subjectname($hw['component_id']);
		elgg_new_homework($m['tid'],$m['cid'],$subject,$component,$m['title'],$body,$m['dateset']);
		}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>