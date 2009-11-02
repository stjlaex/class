#! /usr/bin/php -q
<?php
/* 
 *                                                       ldap_enrol_users.php
 * 
 */
$book='admin';
$current='ldap_enrol_users.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
    $ARGS = array();
    foreach ($argv as $arg) {
		if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');
/**/

/*
 * head options: 
 */ 

echo (date("j F Y, H:i:s") . " ClaSS to LDAP enrolment. \n");
trigger_error(date("j F Y, H:i:s") . ' ClaSS to LDAP enrolment', E_USER_WARNING);

/* 
 * Core tasks: 
 */

/* Connect to LDAP server */
$ds = ldap_connect($CFG->ldapserver);

/* Make sure of right LDAP version is being used */
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ds) {
	/* Bind to LDAP DB */
	$userrdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	$bind_result = ldap_bind($ds, $userrdn, $CFG->ldappasswd);
	
	//trigger_error(' * $bind_result='.$bind_result, E_USER_WARNING);
	
	if ($bind_result) {

		/**
		 *	Step TE: Teacher Enrolment Process
		 *	
		 */

		//$entries=0.0;
		$idx=0;
		$teachenroldata = array();
		$courses=list_courses();
		foreach ($courses as $course) {
			echo' '.$course['id']."\n";
			trigger_error(' --Cou* '.$course['id'].'-'.$course['name'], E_USER_WARNING);
			$classes=list_course_classes($course['id']);
			foreach($classes as $class) {
				/* prepare to get teacher enrolment data */
				/* get subject/course/stage for a specific class*/
				$clsid=$class['id'];
				$classes_d=array();				
				$zsql="SELECT id, subject_id, course_id, stage FROM class WHERE id='".$clsid."' ORDER BY id";
				$sqlresult=mysql_query($zsql);
			   	while($class_d=mysql_fetch_array($sqlresult,MYSQL_ASSOC)){
					$classes_d[]=$class_d;
				}
				foreach($classes_d as $class_d) {
					$teachenroldata[$idx]['course_id']=$course['id'];
					$teachenroldata[$idx]['class_id']=$class['id'];
					$teachenroldata[$idx]['subject_id']=$class_d['subject_id'];
					$teachenroldata[$idx]['stage']=$class_d['stage'];
					$idx++;
				}
				trigger_error('-SCS* '	.$teachenroldata[$idx]['class_id'].'-'
										.$teachenroldata[$idx]['subject_id'].'-'
										.$teachenroldata[$idx]['course_id'].'-'
										.$teachenroldata[$idx]['course_name'].'-'
										.$teachenroldata[$idx]['stage'], E_USER_WARNING);
			}
			$idx++;
		}

		/* display all teachers */ 
		$ted=array();
		$idx=0;
		$tpc=array();
		$tpcx=0;
		foreach($teachenroldata as $ted) {
			trigger_error('-TED* '	.$idx.'-'
									.$ted['class_id'].'-'
									.$ted['subject_id'].'-'
									.$ted['course_id'].'-'
									.$ted['stage'], E_USER_WARNING);
			$teachers=list_class_teachers($ted['class_id']);
			foreach($teachers as $teacher) {	
				trigger_error('-TPC1* '.$tpcx.'-'.$teacher['id'], E_USER_WARNING);
				$tpc[$tpcx]['teacher_id']=$teacher['id'];
				$tpc[$tpcx]['teacher_name']=$teacher['name'];
				$tpc[$tpcx]['class_id']=$ted['class_id'];
				$tpc[$tpcx]['subject_id']=$ted['subject_id'];
				$tpc[$tpcx]['subject_name']=get_subjectname($ted['subject_id']);
				$tpc[$tpcx]['course_id']=$ted['course_id'];
				$tpc[$tpcx]['course_name']=get_coursename($ted['course_id']);
				$tpc[$tpcx]['stage']=$ted['stage'];
				$tpcx++;
			}
			$idx++;
		}

		/* sort multi-dimensional array */
		array_multisort($tpc['subject_name'],SORT_ASC, SORT_STRING,
						$tpc['course_name'],SORT_ASC, SORT_STRING,
						$tpc['stage'],SORT_ASC, SORT_STRING);

		/* process start: go through the whole array*/

		/* display the tpc array */
		$tpcx=0;
		/* teacher counter per class */
		$nx=0;
		/* 'There_is_a_group' switch */
		$there_is_a_group=false;
		/* entries (groups) inserted into the LDAP during this process*/
		$entries=0.0;
		foreach($tpc as $tpcitem) {
			trigger_error('-TPC2* '	.$tpcx.'-'
									.$tpcitem['teacher_id'].'-'
									.$tpcitem['teacher_name'].'-'
									.$tpcitem['class_id'].'-'
									.$tpcitem['subject_id'].'-'
									.$tpcitem['subject_name'].'-'
									.$tpcitem['course_id'].'-'
									.$tpcitem['course_name'].'-'
									.$tpcitem['stage']
									, E_USER_WARNING);
			
			/* if there isn't any group ... */
			if ($there_is_a_group==false) {
				/* create a new group (subject/course/stage)*/
				$there_is_a_group=true;
				
				$info = array();
				$info['gidnumber']			='54321';
				$info['objectclass'][0]	='posixGroup';
				$info['objectclass'][1]	='top';
				/* remove commas */
				$subjectv=str_replace(',',' ',$tpcitem['subject_name']);						
				$coursev=str_replace(',',' ',$tpcitem['course_name']);						
				$stagev=str_replace(',',' ',$tpcitem['stage']);	
								
				$subjectv=str_replace('-',' ',$subjectv);						
				$coursev=str_replace('-',' ',$coursev);						
				$stagev=str_replace('-',' ',$stagev);						

				/* group */
				$info['cn']=$subjectv.'- '.$coursev.' '.$stagev;
				/* format RDN (group key) */
				$coursedn='cn='.$info['cn'].',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
				/* prepare to get the first teacher (first item in a group) */
				//$info['memberUid'][0]=$tpcitem['teacher_id'];
				$info['memberUid'][0]=$CFG->clientid.$tpcitem['teacher_id'];
				$nx++;
				/* OK, get next item. Is there any other teacher? */				
				
			} else {	
				/* remove commas */						
				$subjectv=str_replace(',',' ',$tpcitem['subject_name']);						
				$coursev=str_replace(',',' ',$tpcitem['course_name']);						
				$stagev=str_replace(',',' ',$tpcitem['stage']);	

				$subjectv=str_replace('-',' ',$subjectv);						
				$coursev=str_replace('-',' ',$coursev);						
				$stagev=str_replace('-',' ',$stagev);						

				/* Is there any other teacher for the same group? */
				if (($subjectv.'- '.$coursev.' '.$stagev)==$info['cn']) {
					/* yes, add teacher to group */
					//$info['memberUid'][$nx]=$tpcitem['teacher_id'];				
					$info['memberUid'][$nx]=$CFG->clientid.$tpcitem['teacher_id'];				
					$nx++;
				} else {
					/* no, write the group */ 

					/* --- */
					/* lookup entry in the LDAP db */
					$cn='cn='.$info['cn'];
					$sr=ldap_search($ds, $coursedn, $cn);
					$ldaperrno=ldap_errno($ds);
					if (($ldaperrno!=0) & ($ldaperrno!=32)) {
					    trigger_error(' *** ldap_errno  : '.$ldaperrno, E_USER_WARNING);
					    trigger_error(' *** ldap_err2str: '.ldap_err2str($ldaperrno), E_USER_WARNING);
					    trigger_error(' *** $sr         : '.$sr.' | ', E_USER_WARNING);
				    }
					if (ldap_count_entries($ds, $sr) > 0) {
					  // entry exists, delete it
					  $del_res=ldap_delete($ds,$coursedn);
					  if (!$del_res) {
					    trigger_error(' *** could not delete entry: '.$coursedn, E_USER_WARNING);
					  } 
					} else {
						//echo '* entry does no exist *'."\n";
					} 
					$add_res=ldap_add($ds, $coursedn, $info);
					if ($add_res==false) {
					  trigger_error(' *** Unable to insert entry into LDAP DB: ' .$coursedn, E_USER_WARNING);
					} 
					/* entry counter */
					$entries++;
					/* --- */

					/* initialize variables */
					$nx=0;
					/* create a new group (subject/course/stage)*/
					$there_is_a_group=true;
					
					$info = array();
					$info['gidnumber']			='54321';
					$info['objectclass'][0]	='posixGroup';
					$info['objectclass'][1]	='top';

					/* group */
					$info['cn']=$subjectv.'- '.$coursev.' '.$stagev;
					/* format RDN (group key) */
					$coursedn='cn='.$info['cn'].',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
					/* get the first teacher (first item in group) */
					//$info['memberUid'][0]=$tpcitem['teacher_id'];
					$info['memberUid'][0]=$CFG->clientid.$tpcitem['teacher_id'];
					$nx++;
					/* OK, get next item in multi-dimensional array. Is there any other teacher? */				
				}				
			}
			$tpcx++;
		}
		/* insert the latest group into ldap db*/
		if ($there_is_a_group==true) {
			/* it's true, write the group */ 

			/* --- */
			/* lookup entry in the LDAP db */
			$cn='cn='.$info['cn'];
			$sr=ldap_search($ds, $coursedn, $cn);
			$ldaperrno=ldap_errno($ds);
			if (($ldaperrno!=0) & ($ldaperrno!=32)) {
			    trigger_error(' *** ldap_errno  : '.$ldaperrno, E_USER_WARNING);
			    trigger_error(' *** ldap_err2str: '.ldap_err2str($ldaperrno), E_USER_WARNING);
			    trigger_error(' *** $sr         : '.$sr.' | ', E_USER_WARNING);
		    }
		    
			if (ldap_count_entries($ds, $sr) > 0) {
			  // entry exists, delete it
			  $del_res=ldap_delete($ds,$coursedn);
			  if (!$del_res) {
			    trigger_error(' *** could not delete entry: '.$coursedn, E_USER_WARNING);
			  } 
			} else {
				//echo '* entry does no exist *'."\n";
			} 
			$add_res=ldap_add($ds, $coursedn, $info);
			if ($add_res==false) {
			  trigger_error(' *** Unable to insert entry into LDAP DB: ' .$coursedn, E_USER_WARNING);
			} 
			/* entry counter */
			$entries++;
			/* --- */		
		}


		/**
		 *	Step: Student Enrolment Process
		 *	
		 */
		$entries=0.0;
		$courses=list_courses();
		foreach ($courses as $course) {
			$info = array();
			$info['gidnumber']			='54321';
			$info['objectclass'][0]	='posixGroup';
			$info['objectclass'][1]	='top';
			/* prepare to get the course subject */
			$subjects=list_course_subjects($course['id']);
			foreach($subjects as $subject) {
				/* prepare to get course name */
				$cohorts=list_course_cohorts($course['id']);
				foreach ($cohorts as $cohort) {
					/* format cn with course subject/name/stage */
					if ($cohort['stage']!='%') { 
						if (strlen($cohort['stage'])>0) {
							$subjectv=str_replace(',',' ',$subject['name']);						
							$coursev=str_replace(',',' ',$course['name']);						
							$cohortv=str_replace(',',' ',$cohort['stage']);	
												
							$subjectv=str_replace('-',' ',$subjectv);						
							$coursev=str_replace('-',' ',$coursev);						
							$cohortv=str_replace('-',' ',$cohortv);						

							$info['cn']=$subjectv.'- '.$coursev.' '.$cohortv;
							
							/* if this course does not exist in the LDAP Teacher Enrolment section, ignore it*/
							$coursedn='cn='.$info['cn'].',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
							
							/* lookup course-with-Teacher entry in the LDAP db */
							$cn='cn='.$info['cn'];
							$sr=ldap_search($ds, $coursedn, $cn);
							trigger_error(' *** $sr         : '.$sr.' | ', E_USER_WARNING);
							if (($ldaperrno!=0) & ($ldaperrno!=32)) {
							    trigger_error(' * ldap_errno  : '.$ldaperrno, E_USER_WARNING);
							    trigger_error(' * ldap_err2str: '.ldap_err2str($ldaperrno), E_USER_WARNING);
							    trigger_error(' * $sr         : '.$sr.' | ', E_USER_WARNING);
							}
							$ldaperrno=ldap_errno($ds);
							if (ldap_count_entries($ds, $sr) > 0) {
								/* OK, entry exists, go on with Student Enrolment*/
								
								/* format RDN */
								$coursedn='cn='.$info['cn'].',ou=StdEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;

								/* prepare to get the lot: student-memberUid */
								$students=listin_subject_classes($subject['id'],$course['id'],$cohort['stage']);
								$idx=0;
								$members=array();

								foreach ($students as $student) {
									/* assign epfusername*/
									$members[$idx]=get_epfusername($student['id']);
									$idx++;
								}
								
								/* remove duplicates and assign members */
								if (_empty($members)==false) {
								  sort($members,SORT_STRING);
								  $info['memberUid'][0]=$members[0];
								  $nx=0;
								  for($mx=1; $mx<$idx; $mx++) {
									if ($members[$mx]!=$info['memberUid'][$nx]) {
									  $nx++;
									  $info['memberUid'][$nx]=$members[$mx];
									} 
								  } 
								}
								 
								/* lookup entry in the LDAP db */
								$cn='cn='.$info['cn'];
							    trigger_error(' *** coursedn: '.$coursedn.' | ', E_USER_WARNING);
							    trigger_error(' *** cn: '.$cn.' | ', E_USER_WARNING);
								
								$sr=ldap_search($ds, $coursedn, $cn);
								$ldaperrno=ldap_errno($ds);
								if (($ldaperrno!=0) & ($ldaperrno!=32)) {
								    trigger_error(' *** ldap_errno  : '.$ldaperrno, E_USER_WARNING);
								    trigger_error(' *** ldap_err2str: '.ldap_err2str($ldaperrno), E_USER_WARNING);
								    trigger_error(' *** $sr         : '.$sr.' | ', E_USER_WARNING);
							    }
							    
								if (ldap_count_entries($ds, $sr) > 0) {
								  // entry exists, delete it
								  $del_res=ldap_delete($ds,$coursedn);
								  if (!$del_res) {
								    trigger_error(' *** could not delete entry: '.$coursedn, E_USER_WARNING);
								  } 
								} else {
									//echo '* entry does no exist *'."\n";
								} 
								$add_res=ldap_add($ds, $coursedn, $info);

								if ($add_res==false) {
								  trigger_error(' *** Unable to insert entry into LDAP DB: ' .$coursedn, E_USER_WARNING);
								} 
								
								/* entry counter */
								$entries++;
							}
						} 
					} 
				} 
			} 
		} 
		trigger_error(' * '.$entries.' entries have been processed', E_USER_WARNING);


	} else {
		trigger_error('could not bind to the server', E_USER_WARNING);
		die;
	} 
} else {
	trigger_error('Unable to connect to LDAP server', E_USER_WARNING);
	die;
} 


/*
 * end options: 
 */ 

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');


/** 
 * check if an array is empty 
 *
 */
function _empty() {
	foreach(func_get_args() as $args) {
	    if( !is_numeric($args) ) {
		  if( is_array($args) ) { // Is array?
			if( count($args, 1) < 1 ) return true;
		  }
		  elseif(!isset($args) || strlen(trim($args)) == 0)
			return true;
	    }
	}
	return false;
}

?>	
