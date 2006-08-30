<?php 
/**													fix8.php
 * temporary quick-fix to populate yeargroup and formgroup communities
 */

$action='';

$d_course=mysql_query("SELECT id FROM course");
$years=array('2003','2004','2005','2006','2007','2008');
while($course=mysql_fetch_array($d_course,MYSQL_ASSOC)){
	$crid=$course['id'];
	$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
				course_id='$crid'");
	while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
		$stagename=$stage['stage'];
		reset($years);
		while(list($index,$year)=each($years)){
			mysql_query("INSERT INTO cohort (course_id, stage, year, season) VALUES
			('$crid','$stagename','$year','S')");
			}
		}
	}

$d_community=mysql_query("SELECT id, name FROM community WHERE type='year'");
while($community=mysql_fetch_array($d_community,MYSQL_ASSOC)){
	$comid=$community['id'];
	$yid=$community['name'];
	$d_student=mysql_query("SELECT id  FROM student WHERE yeargroup_id='$yid'");
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
		$sid=$student['id'];
				mysql_query("INSERT INTO comidsid (community_id, student_id) VALUES
			('$comid','$sid')");
		}
	$cohorts=array();
	if($yid==-2){$cohorts[]=array('crid'=>'FS','stage'=>'P');}
	if($yid==-1){$cohorts[]=array('crid'=>'FS','stage'=>'N');}
	if($yid==0){$cohorts[]=array('crid'=>'FS','stage'=>'R');}
	if($yid==1){$cohorts[]=array('crid'=>'KS1','stage'=>'Y01');}
	if($yid==2){$cohorts[]=array('crid'=>'KS1','stage'=>'Y02');}
	if($yid==3){$cohorts[]=array('crid'=>'KS2','stage'=>'Y03');}
	if($yid==4){$cohorts[]=array('crid'=>'KS2','stage'=>'Y04');}
	if($yid==5){$cohorts[]=array('crid'=>'KS2','stage'=>'Y05');}
	if($yid==6){$cohorts[]=array('crid'=>'KS2','stage'=>'Y06');}
	if($yid==7){$cohorts[]=array('crid'=>'KS3','stage'=>'Y07');
	$cohorts[]=array('crid'=>'Conv','stage'=>'Pri');}
	elseif($yid==8){$cohorts[]=array('crid'=>'KS3','stage'=>'Y08');
	$cohorts[]=array('crid'=>'Conv','stage'=>'Seg');}
	elseif($yid==9){$cohorts[]=array('crid'=>'KS3','stage'=>'Y09');
	$cohorts[]=array('crid'=>'Conv','stage'=>'Ter');}
	elseif($yid==10){$cohorts[]=array('crid'=>'GCSE','stage'=>'Y10');
	$cohorts[]=array('crid'=>'PreBach','stage'=>'Pri');}
	elseif($yid==11){$cohorts[]=array('crid'=>'GCSE','stage'=>'Y11');
	$cohorts[]=array('crid'=>'PreBach','stage'=>'Seg');}
	elseif($yid==12){$cohorts[]=array('crid'=>'AS','stage'=>'Y12');}
	elseif($yid==13){$cohorts[]=array('crid'=>'A2','stage'=>'Y13');}
	while(list($index,$cohort)=each($cohorts)){
		$cohid=getcurrentCohortId($cohort['crid'],$cohort['stage'],'2006');
		if($cohid!=''){mysql_query("INSERT INTO cohidcomid (cohort_id,
								community_id) VALUES
								('$cohid','$comid')");}
		}
	}

$d_community=mysql_query("SELECT id, name FROM community WHERE type='form'");
while($community=mysql_fetch_array($d_community,MYSQL_ASSOC)){
	$comid=$community['id'];
	$fid=$community['name'];
	mysql_query("UPDATE form SET name='$fid' WHERE id='$fid'");
	$d_student=mysql_query("SELECT id FROM student WHERE form_id='$fid'");
	while($student=mysql_fetch_array($d_student,MYSQL_ASSOC)){
		$sid=$student['id'];
		mysql_query("INSERT INTO comidsid (community_id, student_id) VALUES
				('$comid','$sid')");
		}
	}

$d_ass=mysql_query("SELECT * FROM assessment");
while($ass=mysql_fetch_array($d_ass,MYSQL_ASSOC)){
	$eid=$ass['id'];
	$gradingname='';
	if($ass['resultqualifier']=='KG'){$gradingname='KG';}
	elseif($ass['resultqualifier']=='NL'){$gradingname='broad nc levels';}
	elseif($ass['resultqualifier']=='CL'){$gradingname='1st certificate';}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='GF'){
		$gradingname='GCSE';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='AL'){
		$gradingname='A Level';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='NA'){
		$gradingname='IGCSE Exam Tier';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='A2'){
		$gradingname='A2 Points Score';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='AS'){
		$gradingname='AS Points Score';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='TA' and $ass['course_id']=='A2'){
		$gradingname='A2 Points Score';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']=='TA' and $ass['course_id']=='AS'){
		$gradingname='AS Points Score';
		}
	elseif($ass['resultqualifier']=='EG' and $ass['method']='TA' and $ass['course_id']=='GCSE'){
		$gradingname='GCSE';
		}

	mysql_query("UPDATE assessment SET grading_name='$gradingname' WHERE id='$eid'");

	if($ass['stage']=='END'){
		$stage='';
		if($ass['course_id']=='KS3'){$stage='Y09';}
		if($ass['course_id']=='A2'){$stage='Y13';}
		if($ass['course_id']=='AS'){$stage='Y12';}
		if($stage!=''){
			mysql_query("UPDATE assessment SET stage='$stage' WHERE id='$eid'");
			}
		}

	}

$ncyears=array('1' => '-1', '2' => '0', '3' => '1',
	'4' => '2', '5' => '3', '6' => '4', '7' =>
	'5', '8' => '6', '9' => '7', '10' => '8', '11' => '9', '12' =>
			  '10', '13' => '11', '14' => '12', '15' => '13');
while(list($ncy,$yid)=each($ncyears)){
	mysql_query("UPDATE background SET yeargroup_id='$yid' WHERE yeargroup_id='$ncy'");
	mysql_query("UPDATE comments SET yeargroup_id='$yid' WHERE yeargroup_id='$ncy'");
	mysql_query("UPDATE incidents SET yeargroup_id='$yid' WHERE yeargroup_id='$ncy'");
	}
?>