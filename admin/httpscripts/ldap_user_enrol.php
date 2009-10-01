#! /usr/bin/php -q
<?php
/* 
 *                                                       ldap_user_enrol.php
 * 
 */
$book='admin';
$current='ldap_user_enrol.php';

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


$ds=false;

if(isset($CFG->ldapserver) and $CFG->ldapserver!=''){
	/* Connect to LDAP server */
	$ds = ldap_connect($CFG->ldapserver);

	/* Make sure of right LDAP version is being used */
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	}


if ($ds) {
	/* Bind to LDAP DB */
	$userrdn='cn='.$CFG->ldapuser.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
	$bind_result = ldap_bind($ds, $userrdn, $CFG->ldappasswd);
	
	//trigger_error(' * $bind_result='.$bind_result, E_USER_WARNING);
	
	if ($bind_result) {

		/**
		 *	Step: Teacher Enrolment Process
		 *	
		 */
		trigger_error(' *** Step: TchEnrol *** ', E_USER_WARNING);
		require_once('ldap_user_enrol_step_te.php');


		/**
		 *	Step: Student Enrolment Process
		 *	
		 */
		$entries=0.0;
		$courses=list_courses();
		foreach ($courses as $course) {
			$info = array();
			$info['gidnumber'] ='54321';
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
									$EPFUsername=fetchStudent_singlefield($sid,'EPFUsername');
									if($EPFUsername['EPFUsername']['value']!=''){
										$members[$idx]=$EPFUsername['EPFUsername']['value'];
										$idx++;
										}
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
									} 
								else {
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

		} 
	else {
		trigger_error('could not bind to the server', E_USER_WARNING);
		die;
		} 
	} 
else {
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
