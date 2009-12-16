#! /usr/bin/php -q
<?php
/** 
 *                                                       ldap_enrol_users.php
 * 
 */
$book='admin';
$current='ldap_enrol_users.php';

/* The path is passed as a command line argument. */
function arguments($argv){
    $ARGS=array();
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');



/* Connect to LDAP server */
$ds=ldap_connect($CFG->ldapserver);

/* Make sure of right LDAP version is being used */
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

if($ds){
	/* Bind to LDAP DB */
	$userrdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	$bind_result=ldap_bind($ds, $userrdn, $CFG->ldappasswd);
	
	//trigger_error(' * $bind_result='.$bind_result, E_USER_WARNING);
	}	
else{
	trigger_error('Unable to connect to LDAP server', E_USER_ERROR);
	} 

if(!$bind_result){
	trigger_error('Could not bind to LDAP server', E_USER_ERROR);
	}

/**
 *	Step TE: Teacher Enrolment Process
 *	
 */


$enrolclasses=array();
$courses=list_courses();
foreach($courses as $course){
	$crid=$course['id'];
	$classes=list_course_classes($crid);
	foreach($classes as $class){
		/* prepare to get teacher enrolment data 
		 *   get subject/course/stage for a specific class
		 */
		$bid=$class['subject_id'];
		$newclass=array('course_id'=>$crid,
						'class_id'=>$class['id'],
						'subject_id'=>$bid,
						'stage'=>$class['stage']
						);
		$enrolclasses[]=$newclass;
		}
	}


$classins=array();
foreach($enrolclasses as $cindx => $class){
	/* remove commas etc. */
	$subjectv=str_replace(',',' ',get_subjectname($class['subject_id']));
	$coursev=str_replace(',',' ',get_coursename($class['course_id']));
	$stagev=str_replace(',',' ',$class['stage']);
	$subjectv=str_replace('-',' ',$subjectv);
	$coursev=str_replace('-',' ',$coursev);
	$stagev=str_replace('-',' ',$stagev);
	$cn=$subjectv.'- '.$coursev.' '.$stagev;
	$teachers=list_class_teachers($class['class_id']);
	foreach($teachers as $teacher){
	  $tid=$teacher['id'];
	  $classins[$cn][$tid]=array('teacher_id'=>$tid,'teacher_name'=>$teacher['name']);
		}
	}

foreach($classins as $cn => $teachers){
	$info=array();
	$info['gidnumber']='54321';
	$info['objectclass'][0]	='posixGroup';
	$info['objectclass'][1]	='top';
	$info['cn']=$cn;
	$info['memberUid']=array();
	/* format RDN (group key) */
	$coursedn='cn='.$cn.',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	foreach($teachers as $tid => $teacher){
	  $info['memberUid'][]=$CFG->clientid.$tid;
	  }

	/* lookup entry in the LDAP db */
	$cnn='cn='.$info['cn'];
	$sr=ldap_search($ds, $coursedn, $cnn);
	if($sr and ldap_count_entries($ds, $sr) > 0){
		/* course exists, delete it */
		$del_res=ldap_delete($ds,$coursedn);
		if(!$del_res){trigger_error('Could not delete LDAP entry: '.$coursedn, E_USER_WARNING);}
		}
	$add_res=ldap_add($ds, $coursedn, $info);
	if($add_res==false){
		$ldaperrno=ldap_errno($ds);
		trigger_error('Unable to insert entry into LDAP DB: ' .$coursedn, E_USER_WARNING);
		trigger_error('Doing: ' .$coursedn, E_USER_NOTICE);
		foreach($info['memberUid'] as $mx => $mem){
			trigger_error('Doing teachers: '. $mx.' '.$mem, E_USER_NOTICE);
			}
		}
	else{
		//trigger_error('Added: ' .$coursedn, E_USER_NOTICE);
		}
	}



/**
 *	Step: Student Enrolment Process
 *	
 */
$entries=0;
$courses=list_courses();
foreach($courses as $course){
	/* prepare to get the course subject */
	$subjects=list_course_subjects($course['id']);
	foreach($subjects as $subject){
		/* prepare to get course name */
		$cohorts=list_course_cohorts($course['id']);
		foreach($cohorts as $cohort){
			/* format cn with course subject/name/stage */
			if($cohort['stage']!='%'){ 
				if(strlen($cohort['stage'])>0){
					$subjectv=str_replace(',',' ',$subject['name']);						
					$coursev=str_replace(',',' ',$course['name']);						
					$stagev=str_replace(',',' ',$cohort['stage']);	
												
					$subjectv=str_replace('-',' ',$subjectv);						
					$coursev=str_replace('-',' ',$coursev);				    
					$stagev=str_replace('-',' ',$stagev);

					$info=array();
					$info['gidnumber']='54321';
					$info['objectclass'][0]	='posixGroup';
					$info['objectclass'][1]	='top';
					$info['cn']=$subjectv.'- '.$coursev.' '.$stagev;

					/* if this course does not exist in the LDAP Teacher Enrolment section, ignore it*/
					$coursedn='cn='.$info['cn'].',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;

					/* lookup course-with-Teacher entry in the LDAP db */
					$cn='cn='.$info['cn'];
					$sr=ldap_search($ds, $coursedn, $cn);
					if($sr and ldap_count_entries($ds, $sr) > 0){
						/* OK, entry exists, go on with Student Enrolment*/
						/* format RDN */
						$coursedn='cn='.$info['cn'].',ou=StdEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
						
						/* prepare to get the lot: student-memberUid */
						$students=listin_subject_classes($subject['id'],$course['id'],$cohort['stage']);
						$idx=0;
						$members=array();
						
						foreach($students as $student){
							/* assign epfusername*/
							$S=fetchStudent_singlefield($student['id'],'EPFUsername');
							if($S['EPFUsername']['value']!=''){
							  $members[$idx]=$S['EPFUsername']['value'];
							  $idx++;
							  }
							}
								
						/* remove duplicates and assign members */
						if(_empty($members)==false){
							sort($members,SORT_STRING);
							$info['memberUid'][0]=$members[0];
							$nx=0;
							for($mx=1; $mx<$idx; $mx++){
								if($members[$mx]!=$info['memberUid'][$nx]){
									$nx++;
									$info['memberUid'][$nx]=$members[$mx];
									} 
								} 
							}
								 
						/* lookup entry in the LDAP db */
						$cn='cn='.$info['cn'];
						
						$sr=ldap_search($ds, $coursedn, $cn);
						if($sr and ldap_count_entries($ds, $sr) > 0){
							$del_res=ldap_delete($ds,$coursedn);
							if(!$del_res){
								trigger_error('Could not delete entry: '.$coursedn, E_USER_WARNING);
								}
							}

                        /* When this course has no student assigned
                         * do not insert into ldap db and
                         * remove the same course from LDAP Teacher Assignment section
                         */
                        if(isset($info['memberUid'][0])){
                            $add_res=ldap_add($ds, $coursedn, $info);
                            if($add_res==false){
                                trigger_error('Unable to insert entry into LDAP DB: ' .$coursedn.' : ', E_USER_WARNING);
								}
                            $entries++;
							}
			else{
                            /* remove the same course from LDAP Teacher Assignment section*/
                            $coursedn='cn='.$info['cn'].',ou=TchEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
                            $del_res=ldap_delete($ds,$coursedn);
                            if(!$del_res){
			      //trigger_error('Could not delete entry: '.$coursedn, E_USER_WARNING);
								}
							}
						}
					} 
				} 
			} 
		} 
	}

trigger_error('LDAP enrolment: '.$entries.' courses have been processed', E_USER_WARNING);


/*
 * end options: 
 */ 

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');


/** 
 * check if an array is empty 
 *
 */
function _empty(){
	foreach(func_get_args() as $args){
	    if( !is_numeric($args) ){
			if( is_array($args) ){ // Is array?
				if( count($args, 1) < 1 ){ return true;}
				}
			elseif(!isset($args) || strlen(trim($args)) == 0){ return true;}
			}
		}
	return false;
	}
?>
