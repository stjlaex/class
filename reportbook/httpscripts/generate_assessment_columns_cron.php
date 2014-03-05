#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/generate_assessment_columns_cron.php
 *
 */

$book='reportbook';
$current='generate_assessment_columns_cron.php';

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

/*
 * Fetch all the assessment with creationdate today, current year and for all
 * courses where the result value is R and the columns are hidden (MarkCount is 0)
 */
function fetchAssessmentsColumnsGenerate(){
	$creationdate=date('Y-m-d');
	$curryear=get_curriculumyear();
	$courses=list_courses();
	foreach($courses as $course){
		$crid=$course['id'];
		$d_eid=mysql_query("SELECT id FROM assessment WHERE creation='$creationdate' and year='$curryear' and course_id='$crid';");
		while($eid=mysql_fetch_array($d_eid,MYSQL_ASSOC)){
			$AssDef=fetchAssessmentDefinition($eid['id']);
			$AssCount=fetchAssessmentCount($eid['id']);
			if($AssCount['MarkCount']['value']==0 and $AssDef['ResultStatus']['value']=='R'){
				$AssDef=array_merge($AssDef,$AssCount);
				$AssDefs[]=$AssDef;
				}
			}
		}
	return $AssDefs;
	}

/*Generates the columns for creationdate today*/
$AssDefs=fetchAssessmentsColumnsGenerate();
foreach($AssDefs as $AssDef){
	$eid=$AssDef['id_db'];
	generate_assessment_columns($eid);
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>
