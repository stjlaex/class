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

$curryear=get_curriculumyear();
$yearcoms=array();
$yearcoms=(array)list_communities('year');


/**
 * Option to search for students with attendance below a certain
 * threshold and trigger an incident
 *
 */
if($CFG->student_event_attendance=='yes'){

	/* TODO: this needs to be defined as an option in the database */
	$percent_threshold='85';
	/**/

	foreach($yearcoms as $com){
		$students=listin_community($com);
		foreach($students as $student){
			$sid=$student['id'];

			fetchAttendanceSummary($sid,$startdate,$enddate,$session='%');

			if($Attendance['Summary']['Attended']['value']/$Attendance['Summary']['Possible']['value']<0.85){

				$Students[$sid]=fetchStudent_short($sid);

				}

			}
		}



	}


/**
 * Option to search for students with negative review scores and add
 * to SEN support list
 *
 */
if($CFG->student_event_support=='yes'){
	/**
	 * Use an elementid to identify the relevant assessments because we
	 * need to search across courses.
	 *
	 */

	/* TODO: this needs to be defined as an option in the database */
	$elementid='P2';
	/**/

	$d_a=mysql_query("SELECT id FROM assessment WHERE year='$curryear' AND element='$elementid';");
	while($ass=mysql_fetch_array($d_a, MYSQL_ASSOC)){
		$eids[]=$ass['id'];
		}

	/* List students with more than 2 negative scores.  */
	$d_s=mysql_query("SELECT DISTINCT student_id FROM eidsid JOIN assessment ON
					eidsid.assessment_id=assessment.id WHERE assessment.year='$curryear' 
					AND assessment.element='$elementid' AND eidsid.value='-1' GROUP BY student_id HAVING COUNT(eidsid.id)>2;");
	while($eidsid=mysql_fetch_array($d_s, MYSQL_ASSOC)){
		$sid=$eidsid['student_id'];
		support_review($sid,$eids);
		}
	}


/**
 *
 */
function support_review($sid,$eids){
	global $CFG;
	$Student=fetchStudent_short($sid);
	$todate=date('Y')."-".date('n')."-".date('j');
	$reviewdate=date('Y-m-d',mktime(0,0,0,date('m')+3,date('d'),date('Y')));

	$senhid=set_student_senstatus($sid,'Y');
	mysql_query("UPDATE senhistory SET reviewdate='$reviewdate' WHERE id='$senhid';");

	foreach($eids as $eid){
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
		}

	return;
	}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>