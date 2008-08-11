<?php
/**							lib/curriculum_functions.php
 *
 */

/**
 * Returns an array of all possible courses
 *
 */
function list_courses(){
	$courses=array();
	$d_c=mysql_query("SELECT * FROM course ORDER BY sequence");
	while($course=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$courses[]=$course;
		}
	return $courses;
	}

/**
 * Returns an array of all posible yeargroups for a single section
 *
 */
function list_yeargroups($secid='%'){
	$yeargroups=array();
	$d_y=mysql_query("SELECT DISTINCT * FROM yeargroup WHERE
					section_id='%' OR section_id LIKE '$secid' 
					ORDER BY section_id,sequence;");
	while($y=mysql_fetch_array($d_y,MYSQL_ASSOC)){
		$yeargroups[]=$y;
		}
	return $yeargroups;
	}

/**
 * Returns an array of all posible formgroups, can limited by $yid
 *
 */
function list_formgroups($yid='%'){
	$forms=array();
	$d_f=mysql_query("SELECT DISTINCT id, name FROM form WHERE
					yeargroup_id LIKE '$yid' ORDER BY yeargroup_id, id;");
	while($form=mysql_fetch_array($d_f,MYSQL_ASSOC)){
		$forms[]=$form;
		}
	return $forms;
	}

/**
 * Returns an array of all posible stages for a single course
 *
 */
function list_course_stages($crid=''){
	$stages=array();
	if($crid!=''){
		$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
			   	course_id='$crid' AND stage!='%' ORDER BY year, stage");
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$stages[]=array('id'=>$stage['stage'],'name'=>$stage['stage']);
			}
		}
	return $stages;
	}

/**
 * Returns an array of all subjects for a single course
 *
 */
function list_course_subjects($crid=''){
	$subjects=array();
	if($crid!=''){
		$d_cridbid=mysql_query("SELECT DISTINCT id, name FROM subject
					JOIN cridbid ON cridbid.subject_id=subject.id
					WHERE cridbid.course_id LIKE '$crid' ORDER BY subject.id");
		while($subject=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
			$subjects[]=$subject;
			}
		}
	return $subjects;
	}

/**
 * Returns an array of all components (id,name,status) for a single
 * subject. If the subject is itself a component then you'll really
 * get strands.
 *
 */
function list_subject_components($bid,$crid,$compstatus='%'){
	if($compstatus=='A'){$compstatus='%';}
	$components=array();
	if($bid!='' and $crid!=''){
		$d_com=mysql_query("SELECT subject.id, subject.name,
						component.status, component.sequence FROM subject
						JOIN component ON subject.id=component.id
						WHERE component.status LIKE '$compstatus' AND
						component.status!='U'  AND 
						component.course_id='$crid' AND
						component.subject_id='$bid' ORDER BY
						component.sequence, subject.name;");
		while($component=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$components[]=$component;
			}
		}
	return $components;
	}

/**
 * Returns an array of all cohorts for a single course year
 *
 */
function list_course_cohorts($crid,$year='',$season='S'){
	$cohorts=array();
	if($year==''){
		$year=get_curriculumyear($crid);
		$season='S';
		}
	$d_coh=mysql_query("SELECT * FROM cohort WHERE
						course_id='$crid' AND year='$year' AND 
						season='$season' ORDER BY stage");
	while($cohort=mysql_fetch_array($d_coh,MYSQL_ASSOC)){
		$cohorts[]=array('id'=>$cohort['id'],
						 'stage'=>$cohort['stage'], 'year'=>$cohort['year'], 
						 'name'=>'('.$cohort['stage'].' '.$cohort['year'].')');
		}
	return $cohorts;
	}

/**
 * Returns an array listing the cids for all classes associated with
 * this form where the class is actually populated by just this
 * form's sids
 *
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

/** 
 * Returns an array listing the classes associated with
 * this course and subject
 *
 */
function list_course_classes($crid='%',$bid='%',$stage='%'){
	$classes=array();
	$d_c=mysql_query("SELECT id, detail, subject_id FROM  
					class WHERE course_id LIKE '$crid' AND
					subject_id LIKE '$bid' AND stage LIKE '$stage' 
					ORDER BY course_id, id");   
   	while($class=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$classes[]=$class;
		}
	return $classes;
	}


/** 
 * Returns an id-name array listing the teachers of a class identified 
 * by its cid
 * 
 *
 */
function list_class_teachers($cid){
	$teachers=array();
	$d_t=mysql_query("SELECT teacher_id FROM  
					tidcid WHERE class_id='$cid';");   
   	while($teacher=mysql_fetch_array($d_t,MYSQL_ASSOC)){
		$teachers[]=array('id'=>$teacher['teacher_id'],'name'=>$teacher['teacher_id']);
		}
	return $teachers;
	}

/**
 * Returns a record form the classes table, Must have crid, bid, and
 * stage set.
 *
 */
function get_subjectclassdef($crid,$bid,$stage){
	$d_c=mysql_query("SELECT many, generate, naming, sp, dp, block FROM classes WHERE
				subject_id='$bid' AND stage='$stage' AND course_id='$crid';");
	if(mysql_num_rows($d_c)>0){
		$classdef=mysql_fetch_array($d_c,MYSQL_ASSOC);
		}
	else{
		$classdef=array();
		$classdef['many']=-1;
		}
	$classdef['crid']=$crid;
	$classdef['bid']=$bid;
	$classdef['stage']=$stage;
	return $classdef;
	}

/**
 * Updates the classes table with a record identified by crid/bid/stage
 * naming is optional, many=0 will delete the record
 *
 */
function update_subjectclassdef($classdef){
	$many=$classdef['many'];
	$generate=$classdef['generate'];
	if($generate=='none'){$generate='sets';}
	if(isset($classdef['naming']) and $classdef['naming']!=''){
		$naming=$classdef['naming'];
		}
	$sp=$classdef['sp'];
	$dp=$classdef['dp'];
	$block=$classdef['block'];
	$bid=$classdef['bid'];
	$stage=$classdef['stage'];
	$crid=$classdef['crid'];

	if($many!='0'){
		$d_classes=mysql_query("SELECT * FROM classes WHERE
						subject_id='$bid' AND stage='$stage' AND course_id='$crid';");
		if(mysql_numrows($d_classes)>0){
			mysql_query("UPDATE classes SET many='$many',
						generate='$generate', sp='$sp',
						dp='$dp', block='$block' WHERE stage='$stage' AND
						subject_id='$bid' AND course_id='$crid';");
			}
		else{
			mysql_query("INSERT INTO classes (many, generate, sp, dp,
						course_id, subject_id, stage) VALUES ('$many',
						'$generate', '$sp', '$dp', '$crid', '$bid', '$stage');");
			}
		if(isset($naming)){
			mysql_query("UPDATE classes SET naming='$naming'
						WHERE stage='$stage' AND
						subject_id='$bid' AND course_id='$crid';");
			}
		}
	else{
		mysql_query("DELETE FROM classes WHERE
						stage='$stage' AND  course_id='$crid' AND
						subject_id='$bid' LIMIT 1;");
		}

	}


/**
 * Keeping things simple by fixing season and year to a single value
 * to sophisticate in the future
 *
 */
function get_classdef_classes($classdef,$currentseason='S'){
	$newcids=array();

	$many=$classdef['many'];
	$generate=$classdef['generate'];
	$bid=$classdef['bid'];
	$stage=$classdef['stage'];
	$crid=$classdef['crid'];
	$currentyear=get_curriculumyear($crid);
	$d_cohidcomid=mysql_query("SELECT cohidcomid.community_id FROM
			cohidcomid JOIN cohort ON cohidcomid.cohort_id=cohort.id 
			WHERE cohort.course_id='$crid' AND cohort.year='$currentyear'
			AND cohort.season='$currentseason' AND cohort.stage='$stage'");
	$communities=array();
	$name=array();
	$name_counter='';
	while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
		$comid=$cohidcomid['community_id'];
		$d_community=mysql_query("SELECT * FROM community WHERE id='$comid'");
		$communities[$comid]=mysql_fetch_array($d_community,MYSQL_ASSOC);
		/*TODO: this only works for one yid!!!!*/
		if($communities[$comid]['type']=='year'){
			$yid=$communities[$comid]['name'];
			$d_form=mysql_query("SELECT id FROM form
								WHERE yeargroup_id='$yid'");
			while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
				$fids[]=$form['id'];
				}
			}
		}


	if($classdef['naming']=='' and $classdef['generate']=='forms'){
			$name['root']=$bid;
			$name['stem']='-';
			$name['branch']='';
			}
	elseif($classdef['naming']=='' and $classdef['generate']=='sets'){
			$name['root']=$bid;
			$name['stem']=$stage;
			$name['branch']='/';
			}
	else{
		list($name['root'],$name['stem'],$name['branch'],$name_counter)=split(';',$classdef['naming'],4);
		while(list($index,$namecheck)=each($name)){
				if($namecheck=='subject'){$name["$index"]=$bid;}
				if($namecheck=='stage'){$name["$index"]=$stage;}
				if($namecheck=='course'){$name["$index"]=$crid;}
				if($namecheck=='year'){$name["$index"]=$yid;}
				}
		}


		/* class_counters will be either a fid or an integer counter */
	$class_counters=array();
	if($classdef['generate']=='forms'){
		$class_counters=$fids;
		$groups=$fids;
		}
	elseif($classdef['many']>0){
		
		$groups=array_fill(0,$classdef['many'],get_yeargroupname($yid));
		if($name_counter!=''){
			for($c=0;$c<$classdef['many'];$c++){
				$class_counters[]=$name_counter[$c];
				}
			}
		else{
			$class_counters=range('1',$classdef['many']);
			}
		}
	else{
		$groups=array();
		$class_counters=array();
		}

	foreach($class_counters as $counter){
		$newcids[]=$name['root'].$name['stem'].$name['branch'].$counter;
		}

	return array($newcids,$groups);
	}

/**
 * Keeping things simple by fixing season and year to a single value
 * to sophisticate in the future
 *
 */
function populate_subjectclassdef($classdef,$currentseason='S'){

	list($newcids,$groups)=get_classdef_classes($classdef,$currentseason);

	foreach($newcids as $newcid){
		$bid=$classdef['bid'];
		$crid=$classdef['crid'];
		$stage=$classdef['stage'];
		if(mysql_query("INSERT INTO class (id,subject_id,course_id,stage) 
				VALUES ('$newcid','$bid','$crid','$stage')")){
			if($classdef['generate']=='forms'){
				$fid=$groups[$newcid];
				$d_sids=mysql_query("SELECT id FROM student WHERE form_id='$fid';");
				while($sids=mysql_fetch_array($d_sids, MYSQL_ASSOC)){
					$sid=$sids['id'];
					mysql_query("INSERT INTO cidsid
								(class_id, student_id) VALUES ('$newcid','$sid')");
					}
				}
			}
		}
	}


/**
 * Checks for a cohort and creates if it doesn't exist
 * expects an array with at least course_id and stage set
 * returns the cohort_id
 *
 */
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


/**
 * Lists all sids who are current members of a cohort
 *
 */
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


/**
 * Defined as the calendar year that the current academic year ends 
 * TODO to sophisticate in future to cover definite endmonths for
 * courses
 */
function get_curriculumyear($crid=''){
	$d_course=mysql_query("SELECT endmonth FROM course WHERE id='$crid'");
	if(mysql_num_rows($d_course)>0){$endmonth=mysql_result($d_course,0);}
	else{$endmonth='';}
	if($endmonth==''){$endmonth='8';/*defaults to August*/}
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

/**
 *
 */
function display_curriculumyear($year){
	$lastyear=$year-1;
	$dispyear=$lastyear.'/'. substr($year,-2);
	return $dispyear;
	}

/**
 *
 */
function get_yeargroupname($yid){
	$d_y=mysql_query("SELECT name FROM yeargroup WHERE id='$yid'");
	if(mysql_num_rows($d_y)>0){
		$yeargroupname=mysql_result($d_y,0);	      
		}
	else{
		$yeargroupname='';
		}
	return $yeargroupname;
	}

/**
 * Just a convenient synonym for get_yeargroupname 
 */
function display_yeargroupname($yid){
	$name=get_yeargroupname($yid);
	return $name;
	}

/**
 * Just a convenient synonym for get_subjectname 
 */
function display_subjectname($bid){
	$subjectname=get_subjectname($bid);
	return $subjectname;
	}

/**
 *
 */
function get_teachername($tid){
	$d_teacher=mysql_query("SELECT forename, surname 
							FROM users WHERE username='$tid'");
	$teacher=mysql_fetch_array($d_teacher,MYSQL_NUM);	      
	$teachername=$teacher[0][0].' '.$teacher[1];
	return $teachername;
	}

/** 
 * Returns the subject name for that bid
 */
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

/**
 * Returns the course name for that bid
 */
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

/**
 * Returns the section name for that secid
 */
function get_sectionname($secid){
	if($secid!=''){
		$d_s=mysql_query("SELECT name FROM section WHERE id='$secid';");
		$name=mysql_result($d_s,0);
		}
	else{
		$name='';
		}
	return $name;
	}
?>