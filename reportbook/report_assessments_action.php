<?php 
/**									 report_assessments_action.php
 *
 */

$action='report_assessments.php';
$action_post_vars=array('selfid','selyid');

if(isset($_POST['selfid'])){$selfid=$_POST['selfid'];}
if(isset($_POST['selyid'])){$selyid=$_POST['selyid'];}
if(isset($_POST['newfid']) and $_POST['newfid']!=$selfid){$selfid=$_POST['newfid'];$selyid='';}
elseif(isset($_POST['newyid']) and $_POST['newyid']!=$selyid){$selyid=$_POST['newyid'];$selfid='';}


include('scripts/sub_action.php');

if($sub=='Submit'){
	$action='report_assessments_view.php';
	}
elseif(isset($_POST['profid']) and $_POST['profid']!=''){


	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN cridbid ON cridbid.course_id=cohort.course_id WHERE
				cridbid.subject_id='$rbid' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		/*TODO: this just guesses a date in the middle of the academic year! */
		$todate=$year-1;
		$todate=$todate.'-12-31';
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage),$todate);
		}

	foreach($students as $index=>$student){
		$sid=$student['id'];


		}

	}

include('scripts/redirect.php');
?>
