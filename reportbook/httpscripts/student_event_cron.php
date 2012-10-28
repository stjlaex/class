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

$year=get_curriculumyear();
$d_a=mysql_query("SELECT id FROM assessment WHERE year='$year' AND label='Progress';");

while($ass=mysql_fetch_array($d_a, MYSQL_ASSOC)){
	$eid=$ass['id'];
	/* List students with more than 2 negative scores.  */
	$d_s=mysql_query("SELECT DISTINCT student_id FROM eidsid 
						WHERE assessment_id='$eid' AND value='-1' GROUP BY student_id HAVING COUNT(id)>2;");
	while($eidsid=mysql_fetch_array($d_s, MYSQL_ASSOC)){
		$sid=$eidsid['student_id'];
		support_review($sid,$eid);
		}
	}

function support_review($sid,$eid){
	global $CFG;
	$Student=fetchStudent_short($sid);
	$todate=date('Y')."-".date('n')."-".date('j');
	$reviewdate=date('Y-m-d',mktime(0,0,0,date('m')+3,date('d'),date('Y')));
	if($Student['SENFlag']['value']=='N'){
		mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid'");
		/*set up first blank record for the profile*/
		mysql_query("INSERT INTO senhistory SET startdate='$todate', reviewdate='$reviewdate', student_id='$sid'");
		$senhid=mysql_insert_id();
		/*creates a blank entry for general comments applicable to all subjects*/
		mysql_query("INSERT INTO sencurriculum SET senhistory_id='$senhid', subject_id='General'");
		}
	else{
		$SEN=fetchSEN($sid);
		$senhid=$SEN['id_db'];
		mysql_query("UPDATE senhistory SET reviewdate='$reviewdate' WHERE student_id='$sid' AND id='$senhid';");
		}

	$Asses=fetchAssessments_short($sid,$eid);
	foreach($Asses as $Ass){
		if($Ass['Value']['value']=='-1'){
			$bid=$Ass['Subject']['value'];
			$com=$Ass['Comment']['value'];
			if(mysql_query("INSERT INTO sencurriculum SET senhistory_id='$senhid', subject_id='$bid', comments='$com'")){}
			else{
				mysql_query("UPDATE sencurriculum SET comments='$com'
									WHERE senhistory_id='$senhid' AND subject_id='$bid'");
				}
			}
		}

	return;
	}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>