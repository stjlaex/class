<?php
/**							lib/curriculum_functions.php
 */

/* returns an array of all posible courses for a single section*/
function list_courses($secid='%'){
	$courses=array();
	$d_c=mysql_query("SELECT DISTINCT * FROM course WHERE
					section_id='%' OR section_id LIKE '$secid' ORDER BY sequence");
	while($course=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$courses[]=$course;
		}
	return $courses;
	}

/* returns an array of all posible stages for a single course*/
function list_course_stages($crid=''){
	$stages=array();
	if($crid!=''){
		$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
					course_id='$crid' AND stage!='%' ORDER BY year");
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$stages[]=array('id'=>$stage['stage'],'name'=>$stage['stage']);
			}
		}
	return $stages;
	}

/* Returns an array of all subjects for a single course*/
function list_course_subjects($crid=''){
	$subjects=array();
	if($crid!=''){
		$d_cridbid=mysql_query("SELECT id, name FROM subject
					JOIN cridbid ON cridbid.subject_id=subject.id
					WHERE cridbid.course_id LIKE '$crid' ORDER BY subject.id");
		while($subject=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
			$subjects[]=$subject;
			}
		}
	return $subjects;
	}

/* Returns an array of all components for a single subject. If the subject is */
/* itself a component then you'll really get strands. */
function list_subject_components($bid,$crid,$compstatus='%'){
	if($compstatus=='A'){$compstatus='%';}
	$components=array();
	if($bid!='' and $crid!=''){
		$d_com=mysql_query("SELECT subject.id, subject.name FROM subject
						JOIN component ON subject.id=component.id
						WHERE component.status LIKE '$compstatus' AND 
						component.course_id='$crid' AND
						component.subject_id='$bid' ORDER BY subject.name");
		while($component=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$components[]=$component;
			}
		}
	return $components;
	}

/* Returns an array of all cohorts for a single course year*/
function list_course_cohorts($crid,$year='',$season='S'){
	$cohorts=array();
	if($year==''){
		$year=get_curriculumyear($crid);
		$season='S';
		}
	$d_coh=mysql_query("SELECT * FROM cohort WHERE
			   	course_id='$crid' AND year='$year' AND season='$season' ORDER BY stage");
	while($cohort=mysql_fetch_array($d_coh,MYSQL_ASSOC)){
		$cohorts[]=array('id'=>$cohort['id'],
						 'stage'=>$cohort['stage'], 'year'=>$cohort['year'], 
						 'name'=>'('.$cohort['stage'].' '.$cohort['year'].')');
		}
	return $cohorts;
	}

/* Returns an array listing the cids for all classes associated with
 * this form where the class is actually populated by just this
 * form's sids
 */
function list_forms_classes($fid){
	$cids=array();
	$cohorts=list_community_cohorts(array('id'=>'','type'=>'form','name'=>$fid));

   	while(list($index,$cohort)=each($cohorts)){
		$currentyear=get_curriculumyear($cohort['course_id']);
		$currentseason='S';
		if($cohort['year']==$currentyear and $cohort['season']==$currentseason){
			$stage=$cohort['stage'];
			$crid=$cohort['course_id'];
			$d_classes=mysql_query("SELECT subject_id, naming FROM classes 
				WHERE stage='$stage' AND course_id='$crid' AND generate='forms'");
			while($classes=mysql_fetch_array($d_classes, MYSQL_ASSOC)){
				$bid=$classes['subject_id'];
				$name=array();
				if($classes['naming']==''){
					$name['root']=$bid;
					$name['stem']='-';
					$name['branch']='';
					}
				else{
					list($name['root'],$name['stem'],$name['branch'],$name_counter)
								= split(';',$classes['naming'],4);
					while(list($index,$namecheck)=each($name)){
						if($namecheck=='subject'){$name["$index"]=$bid;}
						if($namecheck=='stage'){$name["$index"]=$stage;}
						if($namecheck=='course'){$name["$index"]=$crid;}
						if($namecheck=='year'){$name["$index"]=$yid;}
						}
					}
				$cids[]=$name['root'].$name['stem'].$name['branch'].$fid;;
				}
			}
		}
	return $cids;
	}


/* Checks for a cohort and creates if it doesn't exist*/
/* expects an array with at least course_id and stage set*/
/* returns the cohort_id*/
function update_cohort($cohort){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	if(isset($cohort['year'])){$year=$cohort['year'];}
	else{$year=get_curriculumyear($crid);}
	if(isset($cohort['season'])){$season=$cohort['season'];}
	else{$season='S';}
	if($crid!='' and $stage!=''){
		$d_cohort=mysql_query("SELECT id FROM cohort WHERE
				course_id='$crid' AND stage='$stage' AND year='$year'
				AND season='$season'");
		if(mysql_num_rows($d_cohort)==0){
			mysql_query("INSERT INTO cohort (course_id,stage,year,season) VALUES
				('$crid','$stage','$year','$season')");
			$cohid=mysql_insert_id();
			}
		else{
			$cohid=mysql_result($d_cohort,0);
			}
		}
	return $cohid;
	}


/* Lists all sids who are current members of a cohort*/
function listin_cohort($cohort){
	$todate=date("Y-m-d");
	if($cohort['id']!=''){$cohid=$cohort['id'];}
	else{$cohid=update_cohort($cohort);}
	mysql_query("CREATE TEMPORARY TABLE cohortstudent (SELECT DISTINCT student_id FROM comidsid 
				JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
				WHERE cohidcomid.cohort_id='$cohid' AND
				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL))");
	$d_cohortstudent=mysql_query("SELECT b.id, b.surname,
				b.forename, b.middlenames, b.preferredforename, 
				b.form_id FROM cohortstudent a,
				student b WHERE b.id=a.student_id ORDER BY b.surname");
	mysql_query("DROP TABLE cohortstudent");
	$students=array();
   	while($student=mysql_fetch_array($d_cohortstudent,MYSQL_ASSOC)){
		$students[]=$student;
		}
	return $students;
	}


/* Defined as the calendar year that the current academic year ends */
/* TODO to sophisticate in future to cover definite endmonths for courses*/
function get_curriculumyear($crid=''){
	$d_course=mysql_query("SELECT endmonth FROM course WHERE id='$crid'");
	if(mysql_num_rows($d_course)>0){$endmonth=mysql_result($d_course,0);}
	else{$endmonth='';}
	if($endmonth==''){$endmonth='7';/*defaults to July*/}
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

function display_curriculumyear($year){
	$lastyear=$year-1;
	$dispyear=$lastyear.'/'. substr($year,-2);
	return $dispyear;
	}

function display_teachername($tid){
	$d_teacher=mysql_query("SELECT forename, surname 
							FROM users WHERE username='$tid'");
	$teacher=mysql_fetch_array($d_teacher,MYSQL_NUM);	      
	$teachername=$teacher[0].' '.$teacher[1];
	return $teachername;
	}

function display_yeargroupname($yid){
	$d_y=mysql_query("SELECT name 
							FROM yeargroup WHERE id='$yid'");
	$yeargroupname=mysql_result($d_y,0);	      
	return $yeargroupname;
	}

/* Just a convenient synonym for get_subjectname */
function display_subjectname($bid){
	$subjectname=get_subjectname($bid);
	return $subjectname;
	}

/* Returns the subjectname for that bid from the database*/
function get_subjectname($bid){
	if($bid=='%' or $bid=='G' or $bid=='General'){
		/*this is a fix that should be fixed in future!*/
		$subjectname='General';
		}
	elseif($bid!=' ' and $bid!=''){
		$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
		$subjectname=mysql_result($d_subject,0);
		}
	else{
		$subjectname=$bid;
		}
	return $subjectname;
	}

/* Returns the subjectname for that bid from the database*/
function get_coursename($crid){
	if($crid=='%' or $crid=='G' or $crid=='General'){
		/*this is a fix that should be fixed in future!*/
		$coursename='General';
		}
	elseif($crid!=' ' and $crid!=''){
		$d_course=mysql_query("SELECT name FROM course WHERE id='$crid'");
		$coursename=mysql_result($d_course,0);
		}
	else{
		$coursename=$crid;
		}
	return $coursename;
	}
?>