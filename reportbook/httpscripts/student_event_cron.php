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
	$attendance_percent_threshold=85;
	/**/
	$startdate=date('Y-m-d');
	$enddate=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-90,date('Y')));
	/* Students with a percentage attendnace less than a threshold*/
	foreach($yearcoms as $com){
		$students=listin_community($com);
		foreach($students as $student){
			$Attendance=(array)fetchAttendanceSummary($student['id'],$startdate,$enddate,$session='%');
			if($Attendance['Summary']['Possible']['value']>10){
				if(round(100*$Attendance['Summary']['Attended']['value']/$Attendance['Summary']['Possible']['value'])<$attendance_percent_threshold){
					$Students[$student['id']]=fetchStudent_short($student['id']);
					trigger_error('PERCENT!!!!!!!!!!!!!!!!!! '.$student['id'],E_USER_WARNING);
					}
				}
			}
		}


	/* TODO: this needs to be defined as an option in the database */
	$attendance_unexplained_number=3;
	/**/
	/* Students with a consecutive number of unexplained absences */
	$Students=(array)list_absentStudents($eveid='',$yid='%',$lates=0);
	foreach($Students['Student'] as $Student){
		$no=0;
		if($Student['Attendance']['Code']['value']=='O'){
			$Attendances=fetchAttendances($Student['id_db'],0,3);
			foreach($Attendances['Attendance'] as $Attendance){
				if($Attendance['Code']['value']=='O'){
					$no++;
					}
				}
			}
		if($no>=$attendance_unexplained_number){
			$Students[$Student['id_db']]=fetchStudent_short($Student['id_db']);
			trigger_error('ALERT!!!!!!!!!!!!!!!!!! '.$Student['id_db'],E_USER_WARNING);



			}
		}


	}


/**
 * Option to search for students with negative review scores and add
 * to SEN support list
 *
 */
if(isset($CFG->student_event_support) and isset($CFG->student_event_support_elementid) and 
				$CFG->student_event_support=='yes' and $CFG->student_event_support_elementid!=''){
	/**
	 * Use an elementid to identify the relevant assessments because we
	 * need to search across courses.
	 *
	 */

	/* TODO: this needs to be defined as an option in the database */
	$elementid='P2';
	/**/
	$elementid=$CFG->student_event_support_elementid;

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



if(isset($CFG->student_event_support) and $CFG->student_event_support=='tidy'){

	/**
	 * Temporary hack to tidy up out of date enrolment concerns...
	 */
	$d_s=mysql_query("SELECT senhistory.id, senhistory.student_id, sentype.entryn FROM senhistory 
							JOIN sentype ON senhistory.student_id=sentype.student_id
							WHERE sentype.entryn='1' AND sentype.senassessment='I' AND sentype.sentype='ENC';");
	while($s=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$sid=$s['student_id'];
		$senhid=$s['id'];
		mysql_query("DELETE FROM senhistory WHERE student_id='$sid' AND id='$senhid';");
		mysql_query("DELETE FROM sencurriculum WHERE senhistory_id='$senhid';");
		mysql_query("DELETE FROM sentype WHERE student_id='$sid' AND sentype='ENC';");
		mysql_query("UPDATE info SET sen='N' WHERE student_id='$sid';");
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