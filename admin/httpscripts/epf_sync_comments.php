#! /usr/bin/php -q
<?php
/**
 *												 epf_sync_comments.php
 *
 */ 
$book='admin';
$current='epf_sync_comments.php';

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


$d_s=mysql_query("SELECT id,enrolstatus FROM student JOIN info ON student.id=info.student_id;");
while($s=mysql_fetch_array($d_s,MYSQL_ASSOC)){
	if($s['enrolstatus']=='C'){$Students[]=fetchStudent($s['id']);}
	}

foreach($Students as $Student){
	$epfusername=$Student['EPFUsername']['value'];
	$sid=$Student['id_db'];
	echo "Student: ".$sid.">".$epfusername."\n";

	$Comments=fetchComments($sid,'','');
	foreach($Comments['Comment'] as $Comment){
		$commentid=$Comment['id_db'];

		global $CFG;
		echo $Comment['id_db']."=".$Comment['Detail']['value']."\n";
		$title='Subject: ' .display_subjectname($Comment['Subject']['value']);
		$message='<p>'.$Comment['Detail']['value'].'</p>';
		$tid=$Comment['Teacher']['username'];
		$entrydate=$Comment['EntryDate']['value'];
		if($CFG->eportfolio_db!='' and $epfusername!=''){
			mysql_query("UPDATE comments SET guardians='1' WHERE id='$commentid';");
			elgg_new_comment($epfusername,$entrydate,$message,$title,$tid);
			$files=list_files($epfusername,'assessment',$commentid);

			foreach($files as $file){
				$img=epf_photo_display($file);
				epf_append_to_comment($img,$epfusername,$commentid);
				}
			}
		}
		echo "\n\n";
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');

?>
