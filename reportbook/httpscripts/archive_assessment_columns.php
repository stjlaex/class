<?php
/**                    httpscripts/assessment_columns.php
 *
 * Only a transitional function for installations going from 0.6 to 0.8!
 *
 */

require_once('../../scripts/http_head_options.php');


if(!isset($xmlid)){print "Failed"; exit;}

$eid=$xmlid;
$d_score=mysql_query("SELECT * FROM score JOIN eidmid ON
				score.mark_id=eidmid.mark_id WHERE eidmid.assessment_id='$eid'");
while($score=mysql_fetch_array($d_score,MYSQL_ASSOC)){
			$sid=$score['student_id'];
			$mid=$score['mark_id'];
			$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
			$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
			$markdefname=$mark['def_name'];
			$d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$markdefname'");
			$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);
			$scoretype=$markdef['scoretype'];
			if($scoretype=='grade'){
				$gradingname=$markdef['grading_name'];
				$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$gradingname'");
				$grading_grades=mysql_result($d_grading,0);
				}
   			$pid=$mark{'component_id'};
			$date=$mark{'entrydate'};
			$d_ass=mysql_query("SELECT id, subject_id, course_id FROM assessment JOIN eidmid ON
				eidmid.assessment_id=assessment.id WHERE eidmid.mark_id='$mid'");
			while($ass=mysql_fetch_array($d_ass,MYSQL_ASSOC)){
				$eid=$ass{'id'};
				$d_eidmid=mysql_query("SELECT * FROM eidmid WHERE
					mark_id='$mid' AND assessment_id='$eid'");
				$eidmid=mysql_fetch_array($d_eidmid,MYSQL_ASSOC);

/*			fetch the subject from that of the class, hopefully(!) the mark is
				defined uniquely for classes of one subject*/
				if($ass['subject_id']=='%'){
					$d_class=mysql_query("SELECT DISTINCT subject_id 
						FROM class JOIN midcid ON
						midcid.class_id=class.id WHERE midcid.mark_id='$mid'");
					$bid=mysql_result($d_class,0);
					}
				else{$bid=$ass['subject_id'];}


				if($scoretype=='grade'){
					$value=$score['grade'];
					$res=scoreToGrade($value,$grading_grades);
					}
				elseif($scoretype=='value'){
					$value=$score['value'];
					$res=$value;
					}
				elseif($scoretype=='percentage'){
					$value=$score['value'];
					$score_value=$score['value'];
					$total=$score['outoftotal'];
					include('../../markbook/percent_score.php');
					if(isset($percent)){
						$res=$percent.' ('.number_format($score_value,1,'.','').')';
					}
					else{$res='';}
					}

				mysql_query("INSERT INTO eidsid (assessment_id,
					student_id, subject_id, component_id, result,
					value, date) 
					VALUES ('$eid','$sid','$bid','$pid','$res','$value','$date');");
			
				}
		}

mysql_free_result($d_score);
mysql_free_result($d_ass);
mysql_free_result($d_mark);
mysql_free_result($d_class);
mysql_free_result($d_grading);
mysql_free_result($d_markdef);

$returnXML=fetchAssessmentDefinition($eid);
$rootName='AssessmentDefinition';
require_once('../../scripts/http_end_options.php');
exit;
?>

















