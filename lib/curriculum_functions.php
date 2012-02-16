<?php
/**							lib/curriculum_functions.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since				
 *
 */

/**
 * Returns an array of all possible courses
 *
 *	@return array courses
 *
 */
function list_courses($bid=''){
	$courses=array();
	if($bid=='%' or $bid==''){
		$d_c=mysql_query("SELECT * FROM course ORDER BY sequence;");
		}
	else{
		$d_c=mysql_query("SELECT DISTINCT course_id FROM component 
							WHERE id='' AND subject_id='$bid' ORDER BY sequence;");
		}
	while($course=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$courses[]=$course;
		}
	return $courses;
	}

/**
 * Returns an array of the school's sections.
 * First record id=1 is always special (the wholeschool) and is excluded.
 *
 *	@return array sections
 */
function list_sections(){
	$sections=array();
	$d_s=mysql_query("SELECT * FROM section WHERE id>'1' ORDER BY sequence;");
	while($section=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$sections[]=$section;
		}
	return $sections;
	}

/**
 * Returns an array of all posible yeargroups for a single section
 *
 *	@param string $secid
 *	@return array
 *
 */
function list_yeargroups($secid='%'){
	$yeargroups=array();
	$d_y=mysql_query("SELECT DISTINCT id, name, sequence, section_id FROM yeargroup WHERE
					 section_id LIKE '$secid' ORDER BY sequence;");
	while($y=mysql_fetch_array($d_y,MYSQL_ASSOC)){
		$yeargroups[]=$y;
		}
	return $yeargroups;
	}


/**
 * Returns an array of all posible formgroups, can limited by $yid
 *
 *	@param string $yid
 *	@return array
 */
function list_formgroups($yid='%'){

	$forms=(array)list_communities('form','',$yid);

	return $forms;
	}

/**
 * Returns an array of all posible stages for a single course. If no
 * year given then it will stages for the current course
 * structure. There is no explicit sequence defined for stages, it is
 * implicit in their naming scheme and so stage's should be named
 * logically.
 *
 *	@param string $crid
 *	@param string $year
 *	@return array
 *
 */
function list_course_stages($crid='',$year=''){
	if($year==''){$year=get_curriculumyear($crid);}
	$stages=array();
	if($crid!=''){
		$d_stage=mysql_query("SELECT DISTINCT stage FROM cohort WHERE
			   	course_id='$crid' AND stage!='%' AND year='$year' ORDER BY stage ASC;");
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$stages[]=array('id'=>$stage['stage'],'name'=>$stage['stage']);
			}
		}
	return $stages;
	}


/**
 * Returns an array of id,name pairs of every subject in the given course
 *
 *	@param string $crid
 *	@return array
 */
function list_course_subjects($crid='',$substatus='A'){
	$subjects=array();
	if($substatus=='A'){$compmatch="(component.status LIKE '%' AND component.status!='U')";}
	elseif($substatus=='AV'){$compmatch="(component.status='V' OR component.status='O')";}
	else{$compmatch="(component.status LIKE '$substatus' AND component.status!='U')";}
	if($crid!=''){
		$d_c=mysql_query("SELECT DISTINCT subject.id, subject.name, component.sequence, component.status FROM subject
					JOIN component ON component.subject_id=subject.id
					WHERE component.course_id LIKE '$crid' AND component.id='' AND $compmatch ORDER BY subject.id;");
		while($subject=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$subjects[]=$subject;
			}
		}
	return $subjects;
	}


/**
 *
 *  Returns an array of id,name pairs for every subject definition
 *
 *	@return array
 */
function list_subjects($crid,$exist=true){
	$subjects=array();

	if($exist){
		$d_c=mysql_query("SELECT DISTINCT subject.id, CONCAT(subject.name,' (', subject.id,')') AS name FROM subject 
						LEFT JOIN component ON component.subject_id=subject.id 
						WHERE component.course_id='$crid' ORDER BY name;");
		}
	else{
		$d_c=mysql_query("SELECT DISTINCT subject.id, CONCAT(subject.name,' (', subject.id,')') AS name FROM subject 
						LEFT OUTER JOIN component ON component.subject_id=subject.id 
						AND component.course_id='$crid' WHERE component.subject_id IS NULL ORDER BY name;");
		}
	while($subject=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$subjects[]=$subject;
		}

	return $subjects;
	}



/**
 * Returns an array of id,name pairs of every subject taught by a teacher
 *
 *	@param string $tid
 *	@return array
 */
function list_teacher_subjects($tid=''){
	$subjects=array();
	if($tid!=''){
		$d_s=mysql_query("SELECT id, name FROM subject WHERE id=ANY(SELECT DISTINCT subject_id FROM
				class JOIN tidcid ON class.id=tidcid.class_id WHERE
				tidcid.teacher_id='$tid');");
		while($subject=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$subjects[]=$subject;
			}
		}
	return $subjects;
	}


/**
 * Returns an array of all components (id,name,status) for a single
 * subject. If the subject is itself a component then you'll really
 * get strands. Note components can be defined for all subjects
 * (subject_id=%) but strands cannot.
 *
 *	@param string $bid
 *	@param string $crid
 *	@param string $compstatus
 *	@return array
 */
function list_subject_components($bid,$crid,$compstatus='A'){
	$components=array();
	if($compstatus=='A'){$compmatch="(component.status LIKE '%' AND component.status!='U')";}
	elseif($compstatus=='AV'){$compmatch="(component.status='V' OR component.status='O')";}
	else{$compmatch="(component.status LIKE '$compstatus' AND component.status!='U')";}
	if($bid!='' and $crid!=''){
		if($bid!='%'){
			/* Check whether $bid is for a component or a subject. */
			$d_c=mysql_query("SELECT id FROM component WHERE component.course_id='$crid' 
						AND component.id='$bid';");
			if(mysql_num_rows($d_c)==0){
				/* $bid is a subject so listing components */
				$d_com=mysql_query("SELECT subject.id, subject.name,
						component.status, component.sequence FROM subject
						JOIN component ON subject.id=component.id
						WHERE $compmatch AND component.course_id='$crid' AND
						(component.subject_id='$bid' OR component.subject_id='%')  
						ORDER BY component.sequence, subject.name;");
				}
			else{
				/* $bid is a component so listing strands */
				$d_com=mysql_query("SELECT subject.id, subject.name,
						component.status, component.sequence FROM subject
						JOIN component ON subject.id=component.id
						WHERE $compmatch AND component.course_id='$crid' AND
						component.subject_id='$bid'  
						ORDER BY component.status, component.sequence, subject.name;");
				}
			}
		else{
			/* Just list all regardless of subject_id*/
			$d_com=mysql_query("SELECT subject.id, subject.name,
						component.status, component.sequence FROM subject
						JOIN component ON subject.id=component.id
						WHERE $compmatch AND component.course_id='$crid'
						ORDER BY component.status, component.sequence, subject.name;");
			}
		while($component=mysql_fetch_array($d_com,MYSQL_ASSOC)){
			$components[]=$component;
			}
		}

	return $components;
	}


/**
 * Returns an array of all cohorts for a single course year
 *
 *	@param string $crid
 *	@param string $year
 *	@param string $season
 *	@return array
 */
function list_course_cohorts($crid,$year='',$season='S'){
	$cohorts=array();
	if($year==''){
		$year=get_curriculumyear($crid);
		$season='S';
		}
	$d_coh=mysql_query("SELECT * FROM cohort WHERE
						course_id='$crid' AND year='$year' AND 
						season='$season' ORDER BY stage;");
	while($cohort=mysql_fetch_array($d_coh,MYSQL_ASSOC)){
		$cohorts[]=array('id'=>$cohort['id'],
						 'stage'=>$cohort['stage'], 'year'=>$cohort['year'], 
						 'name'=>'('.$cohort['stage'].' '.$cohort['year'].')');
		}
	return $cohorts;
	}



/**
 *
 * Returns an array of all sections that this class could be
 * associated with. Its approximate because there is no guarantee of
 * the enrolment status of the students.
 *
 * If its not linked directly to a yeargroup or form then can't
 * identify its section, just return wholeschool (secid=1).
 *
 *	@param string $cid
 *	@return array
 */
function get_class_section($cid){
	$d_s=mysql_query("SELECT DISTINCT section_id FROM yeargroup WHERE
					id=ANY(SELECT yeargroup_id FROM student
					JOIN cidsid ON cidsid.student_id=student.id WHERE
					cidsid.class_id='$cid' AND yeargroup_id IS NOT NULL);");
	/* Could check for more than one section but will have to assume
	   anyway that all sections associated with this class share the
	   same timetable structure.
	 */
  	$secid=mysql_result($d_s,0,0);

	if($secid=='' or $secid<1){$secid=1;}
	return $secid;
	}



/**
 *
 * Returns an array listing the cids for all classes associated with
 * this form where the class is actually populated by just this
 * form's sids (so does not return sets).
 *
 *	@param string $fid
 *	@return array
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
				WHERE stage='$stage' AND course_id='$crid' AND generate='forms';");
			while($classes=mysql_fetch_array($d_classes, MYSQL_ASSOC)){
				$bid=$classes['subject_id'];
				$name=array();
				if($classes['naming']=='' or $classdef['generate']=='forms'){
					$name['root']=$bid;
					$name['stem']='';
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
 *	@param string $crid
 *	@param string $bid
 *	@param string $stage
 *	@return array
 */
function list_course_classes($crid='%',$bid='%',$stage='%'){
	$classes=array();
	$d_c=mysql_query("SELECT id, id AS name, detail, subject_id, stage FROM  
					class WHERE course_id LIKE '$crid' AND
					subject_id LIKE '$bid' AND stage LIKE '$stage' 
					ORDER BY course_id, id");   
   	while($class=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$classes[$class['id']]=$class;
		}
	return $classes;
	}


/** 
 * Returns an id-name array listing the teachers of a class identified 
 * by its cid
 * 
 *	@param string $cid
 *	@return array
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
 * Returns an id-name array listing all curriculum areas (subject and components) this sid studies.
 *
 *	@param string $sid
 *	@return array
 */
function list_student_subjects($sid){
	if($sid==''){$sid=-1;}
	$classes=array();
   	$d_c=mysql_query("SELECT DISTINCT course_id AS crid, subject_id AS bid FROM
				class JOIN cidsid ON class.id=cidsid.class_id WHERE
				cidsid.student_id='$sid'");

   	while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$subs[]=array('id'=>$c['bid'],'name'=>get_subjectname($c['bid']));
		$coms=list_subject_components($c['bid'],$c['crid'],'A');
		foreach($coms as $com){
			$subs[]=array('id'=>$com['id'],'name'=>$com['name']);
			}
		}

	return $subs;
	}

/** 
 * Returns an array listing all subject classes and their teachers for this sid.
 *
 *	@param string $sid
 *	@param string $crid
 *	@return array
 */
function list_student_course_classes($sid,$crid){
	if($sid==''){$sid=-1;}
	$classes=array();
   	$d_c=mysql_query("SELECT DISTINCT id FROM
				class JOIN cidsid ON class.id=cidsid.class_id WHERE
				cidsid.student_id='$sid' AND class.course_id LIKE '$crid';");
   	while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$cid=$c['id'];
		$d_t=mysql_query("SELECT group_concat(forename, ' ', surname separator ', ') AS tids
								FROM users JOIN tidcid ON tidcid.teacher_id=users.username 
								WHERE tidcid.class_id='$cid' GROUP BY 'class_id';");
		$classes[]=array('id'=>$cid,'teachers'=>mysql_result($d_t,0));
		}

	return $classes;
	}

/** 
 * Returns an id-name array listing all classes this sid attends.
 * The name is a description of the course and subject for each class.
 *
 *	@param string $sid
 *	@return array
 */
function list_student_classes($sid){
	if($sid==''){$sid=-1;}
	$classes=array();
	$d_c=mysql_query("SELECT DISTINCT class.id, class.course_id, class.subject_id FROM  
					class JOIN cidsid ON class.id=cidsid.class_id 
					WHERE cidsid.student_id='$sid';");
   	while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$classes[]=array('id'=>$c['id'],
						 'name'=>$c['course_id'].' '.$c['subject_id']
						 );
		}
	return $classes;
	}


/** 
 * Returns an array of crids for all courses this sid is subscribed to.
 *
 *	@param string $sid
 *	@return array
 */
function list_student_courses($sid,$todate=''){
	if($sid==''){$sid=-1;}
	if($todate==''){$todate=date('Y-m-d');$year=get_curriculumyear();}
	$crids=array();
	$d_c=mysql_query("SELECT DISTINCT course_id FROM cohort 
				WHERE cohort.year='$year' AND cohort.id=ANY(SELECT DISTINCT 
				cohort_id FROM cohidcomid JOIN comidsid ON comidsid.community_id=cohidcomid.community_id
				WHERE comidsid.student_id='$sid' AND
				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL));");
   	while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$crids[]=$c['course_id'];
		}
	return $crids;
	}


/** 
 *
 * Returns an array listing all subject teachers (not trainee
 * teachers!!!) of this student. The array is a partial users record
 * with name and email detials.
 *
 *	@param string $sid
 *	@return array
 */
function list_student_teachers($sid){
	if($sid==''){$sid=-1;}
	$teachers=array();
	$d_t=mysql_query("SELECT DISTINCT username, forename, surname, title, email FROM  
					users JOIN tidcid ON users.username=tidcid.teacher_id 
					WHERE users.nologin!='1' AND
					tidcid.class_id=ANY(SELECT DISTINCT class_id 
					FROM cidsid WHERE student_id='$sid');");
   	while($t=mysql_fetch_array($d_t,MYSQL_ASSOC)){
		$teachers[]=$t;
		}
	return $teachers;
	}


/** 
 *
 * Returns an array listing all staff and teachers with
 * sepsonsibilities for SEN/Support
 *
 *
 *	@return array
 */
function list_support_teachers(){
	$teachers=array();
	$d_t=mysql_query("SELECT  username, forename, surname, title, email FROM users 
						 WHERE (role='sen' OR senrole='1') AND users.nologin!='1';");
	while($t=mysql_fetch_array($d_t)){
		$teachers[]=$t;
		}
	return $teachers;
	}



/**
 *
 * Send a message about a student to the relevant teaching staff.
 *
 *
 * @uses $CFG
 * @param sid    the student message is about
 * @param tid    the teacher sending message 
 * @param string $messagesubject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml html formatted version of the message
 * @param string $teachergroup single character either %, a or p
 */
function message_student_teachers($sid,$tid,$bid,$messagesubject,$messagetext,$messagehtml,$teachergroup='%'){

	global $CFG;
	$result=array();

	/* Teacher group can be a list of set teachers to send to */
	if(is_array($teachergroup)){
		foreach($teachergroup as $teacher){
			if(isset($teacher['email'])){
				$recipients[]=$teacher;
				}
			}
		}
	/* Or make lists of academic and pastoral teacher recipients for the email */
	else{
		$recipients=array();
		if(strpos($teachergroup,'p')!==false){
			$recips=(array)list_sid_responsible_users($sid,$bid);
			$recipients=array_merge($recipients, $recips);
			}
		if(strpos($teachergroup,'a')!==false){
			$recips=(array)list_student_teachers($sid);
			$recipients=array_merge($recipients, $recips);
			}
		if(strpos($teachergroup,'s')!==false){
			$recips=(array)list_support_teachers($sid);
			$recipients=array_merge($recipients, $recips);
			}
		}
	
	/* Decide on the addressee of the message. If possible use teacher's own email address. */
	$teachername=get_teachername($tid);
	$teacher=get_user($tid,'username');
	if($teacher['email']!='' and $teacher['email']!=' '){
		$from['name']=$teachername;
		$from['email']=$teacher['email'];
		}
	else{
		$from['name']='ClaSS';
		if(is_array($CFG->emailnoreply)){
			$from['email']=$CFG->emailnoreply[0];
			}
		else{
			$from['email']=$CFG->emailnoreply;
			}
		}

	if($recipients and $CFG->emailoff!='yes' and $CFG->emailcomments=='yes'){
		if(sizeof($recipients)>0){
			$dones=array();/* Try not to send duplicates. */
			foreach($recipients as $recipient){
				if(!array_key_exists($recipient['username'],$dones)){
					$recipient['email']=strtolower($recipient['email']);
					send_email_to($recipient['email'],$from,$messagesubject,$messagetext,$messagehtml);
					$result[]=get_string('emailsentto','infobook').' '.$recipient['username'];
					$dones[$recipient['username']]=$recipient['username'];
					}
				}
			}
		}

	return $result;
	}



/**
 *
 * Returns a record from the classes table, Must have crid, bid, and
 * stage set.
 *
 *	@param string $crid
 *	@param string $bid
 *	@param string $stage
 *	@return array
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
		$classdef['generate']='';
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
 *	@param string $classdef
 *	@return array
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

	if($many!='' and $generate!=''){
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
						stage='$stage' AND  course_id='$crid' AND subject_id='$bid' LIMIT 1;");
		}
	}


/**
 *
 * Keeping things simple by fixing season and year to a single value
 * to sophisticate in the future
 *
 *	@param string $classdef
 *	@param string $currentseason
 *	@return array
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
			AND cohort.season='$currentseason' AND cohort.stage='$stage';");
	$communities=array();
	$name=array();
	$name_counter='';
	while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
		$comid=$cohidcomid['community_id'];
		$d_community=mysql_query("SELECT * FROM community WHERE id='$comid';");
		$communities[$comid]=mysql_fetch_array($d_community,MYSQL_ASSOC);
		/*TODO: this only works for one yid!!!!*/
		if($communities[$comid]['type']=='year'){
			$yid=$communities[$comid]['name'];
			$forms=(array)list_formgroups($yid);
			foreach($forms as $form){
				$fids[]=$form['name'];
				}
			}
		}

	if($classdef['generate']=='forms'){
		/* form classes always get this format of naming regardless */
		$name['root']=$bid;
		$name['stem']='';
		$name['branch']='';
		}
	elseif($classdef['naming']=='' and $classdef['generate']=='sets'){
		$name['root']=$bid;
		$name['stem']=$yid;
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
	if($classdef['generate']=='forms' and isset($fids)){
		$class_counters=$fids;
		$groups=$fids;
		}
	elseif($classdef['generate']=='sets' and $classdef['many']>0){
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
 *
 * Generates the new teaching classes based on the results of
 * get_classdef_classes for the definition provided by $classdef. Then
 * populates those classes if they are linked to forms leaves empty if
 * they are sets.
 *
 *	@param string $classdef
 *	@param string $currentseason
 *	@return null
 */
function populate_subjectclassdef($classdef,$currentseason='S'){

	list($newcids,$groups)=get_classdef_classes($classdef,$currentseason);

	foreach($newcids as $nindex => $newcid){
		$bid=$classdef['bid'];
		$crid=$classdef['crid'];
		$stage=$classdef['stage'];
		if(mysql_query("INSERT INTO class (id,subject_id,course_id,stage) 
				VALUES ('$newcid','$bid','$crid','$stage')")){
			if($classdef['generate']=='forms'){
				$fid=$groups[$nindex];
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
 *	@param array $cohort
 *	@return string
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
				AND season='$season';");
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
 * Lists all sids who are current members of a cohort, or optionally
 * if todate is set then the membership on that date.
 *
 *	@param array $cohort
 *	@param date $todate
 *	@return array
 */
function listin_cohort($cohort,$todate=''){
	if($todate==''){$todate=date('Y-m-d');}
	if($cohort['id']!=''){$cohid=$cohort['id'];}
	else{$cohid=update_cohort($cohort);}
	mysql_query("CREATE TEMPORARY TABLE cohortstudent (SELECT DISTINCT student_id FROM comidsid 
				JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
				WHERE cohidcomid.cohort_id='$cohid' AND
				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL));");
	$d_cohortstudent=mysql_query("SELECT b.id, b.surname,
				b.forename, b.middlenames, b.preferredforename, 
				b.form_id FROM cohortstudent a,
				student b WHERE b.id=a.student_id ORDER BY b.surname;");
	mysql_query("DROP TABLE cohortstudent;");
	$students=array();
   	while($student=mysql_fetch_array($d_cohortstudent,MYSQL_ASSOC)){
		$students[]=$student;
		}
	return $students;
	}


/**
 * Checks whether a sid is a member of a cohort and returns true or false.
 *
 *	@param string $sid
 *	@param array $cohort
 *	@param date $todate
 *	@return array
 */
function check_student_cohort($sid,$cohort,$todate=''){
	$status=false;
	if($todate==''){$todate=date('Y-m-d');}
	if($cohort['id']!=''){$cohid=$cohort['id'];}
	else{$cohid=update_cohort($cohort);}
	$d_c=mysql_query("SELECT DISTINCT student_id FROM comidsid 
				JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
				WHERE comidsid.student_id='$sid' AND cohidcomid.cohort_id='$cohid' AND
				(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
				AND (comidsid.leavingdate>'$todate' OR 
				comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL);");
	if(mysql_num_rows($d_c)>0){$status=true;}
	return $status;
	}


/**
 * Defined as the calendar year that the current academic year ends 
 * TODO: Currently endmonth and season for a course are not implemented, all
 * courses end at the same time for the whole school, is it too
 * sophisticated or even needed in future to cover different endmonths for
 * courses?
 *
 * If this has not been set then the current year +1 will set. You
 * might want to edit this after ClaSS first runs. TODO: add to install?
 *
 *	@param string $crid TODO
 *	@return date
 */
function get_curriculumyear($crid=''){
	$d_c=mysql_query("SELECT year FROM community WHERE
						name='curriculum year' AND type='';");
	if(mysql_num_rows($d_c)>0){$thisyear=mysql_result($d_c,0);}
	else{
		$thisyear=date('Y')+1;
		set_curriculumyear($thisyear);
		}
	return $thisyear;
	}

/**
 *
 *
 *	@param integer $year
 *	@param string $crid 
 *	@return null
 */
function set_curriculumyear($year,$crid=''){
	$d_c=mysql_query("SELECT year FROM community WHERE
						name='curriculum year' AND type='';");
	if(mysql_num_rows($d_c)>0){
		mysql_query("DELETE FROM community 
						WHERE name='curriculum year' AND type='';");
		}

	mysql_query("INSERT INTO community SET
						name='curriculum year', type='', year='$year';");
	return;
	}

/**
 *
 *	@param integer $year
 *	@return string
 */
function display_curriculumyear($year){
	$lastyear=$year-1;
	$dispyear=$lastyear.'/'. substr($year,-2);
	return $dispyear;
	}

/**
 *
 *	@param integer $yid
 *	@return string
 */
function get_yeargroupname($yid){
	$d_y=mysql_query("SELECT name FROM yeargroup WHERE id='$yid';");
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
 *
 *	@param integer $yid
 *	@return string
 */
function display_yeargroupname($yid){
	$name=get_yeargroupname($yid);
	return $name;
	}

/**
 * Just a convenient synonym for get_subjectname 
 *
 *	@param integer $bid
 *	@return string
 */
function display_subjectname($bid){
	$subjectname=get_subjectname($bid);
	return $subjectname;
	}

/**
 *
 *	@param string $tid
 *	@return array
 */
function get_teachername($tid){
	global $CFG;

	$d_teacher=mysql_query("SELECT forename, surname 
							FROM users WHERE username='$tid'");
	$teacher=mysql_fetch_array($d_teacher,MYSQL_NUM);

	if($CFG->teachername=='formal'){
		$teachername=$teacher[0][0].' '.$teacher[1];
		}
	elseif($CFG->teachername=='informal'){
		$teachername=$teacher[0];
		}
	else{
		$teachername=$teacher[0].' '.$teacher[1];
		}

	return $teachername;
	}

/** 
 * Returns the subject name for that bid
 *
 *	@param string $bid
 *	@return string
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
 *
 *	@param string $crid
 *	@return string
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
 *
 *	@param integer $secid
 *	@return string
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


/**
 *
 * Returns the section for the given id of a yeargroup or
 * formgroup.
 *
 * Default to secid=1 for whole school as a fail safe.
 *
 *	@param string $id
 *	@param string $type
 *	@return array
 */
function get_section($id,$type='year'){
	if($type=='form'){
		$fid=get_form_yeargroup($fid);
		}
	else{
		$yid=$id;
		}
	$d_s=mysql_query("SELECT section.id, section.name FROM section JOIN
				yeargroup ON yeargroup.section_id=section.id WHERE yeargroup.id='$yid';");
	if(mysql_num_rows($d_s)>0){
		$section=mysql_fetch_array($d_s,MYSQL_ASSOC);
		}
	else{
		$section=array('id'=>1,'name'=>'');
		}
	return $section;
	}

/**
 *
 *	@param string $cid
 *	@param boolean $strict
 *	@return array
 */
function listin_class($cid,$strict=false){

	/* If strict=true then students not on enrol will be filtered out, or at least those 
	   not in a yeargroup and therefore not in a form group for registration either! */
	if($strict){
		$d_student=mysql_query("SELECT a.student_id AS id, b.surname,
				b.middlenames, b.preferredforename,
				b.forename, b.yeargroup_id, b.form_id FROM cidsid a, student b 
				WHERE a.class_id='$cid' AND b.id=a.student_id AND b.yeargroup_id!='' ORDER BY b.surname;");
		}
	else{
		$d_student=mysql_query("SELECT a.student_id AS id, b.surname,
				b.middlenames, b.preferredforename,
				b.forename, b.yeargroup_id, b.form_id FROM cidsid a, student b 
				WHERE a.class_id='$cid' AND b.id=a.student_id ORDER BY b.surname;");
		}
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;

	}

/**
 *
 *	@param string $bid
 *	@param string $crid
 *	@param string $stage
 *	@return array
 */
function listin_subject_classes($bid,$crid,$stage){
	$d_student=mysql_query("SELECT student_id AS id FROM cidsid 
							JOIN class ON class.id=cidsid.class_id
							WHERE class.subject_id='$bid'
							AND class.course_id='$crid' AND class.stage='$stage';");
	$students=array();
	while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
		if($student['id']!=''){$students[]=$student;}
		}
	return $students;
	}

/**
 * Simply check if the status for a component (or strand or subject) is covered by the
 * the filter.
 *
 */
function check_component_status($status,$filter){
	$result=false;
	if($status=='N' or $status=='O' or $status=='V' or $status=='U'){
		if($filter=='A' and $status!='U'){$result=true;}
		elseif($filter=='AV' and ($status=='O' or $status=='V') and $status!='U'){$result=true;}
		elseif($filter==$status){$result=true;}
		}
	return $result;
	}
?>
