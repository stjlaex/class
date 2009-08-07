#! /usr/bin/php -q
<?php
/* ldap_user_enrol.php
 * 
 */
 
/*
 * head options: 
 */ 
echo (date("j F Y, H:i:s") . " ClaSS to LDAP enrolment. \n");


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
	if ($bind_result) {

		/**
		 *	Step 1: Process all courses with memberUid from ClaSS
		 *	
		 */
		$entries=0.0;
		$courses=list_courses();
		foreach ($courses as $courseval) {
			$info = array();
			$info['gidnumber']			='54321';
			$info['objectclass'][0]	='posixGroup';
			$info['objectclass'][1]	='top';
			/* prepare to get the course subject */
			$subjects=list_course_subjects($courseval['id']);
			foreach($subjects as $subjectval) {
				/* prepare to get course name */
				$cohorts=list_course_cohorts($courseval['id']);
				foreach ($cohorts as $cohortval) {
					/* format cn with course subject/name/stage */
					if ($cohortval['stage']!='%') { 
						if (strlen($cohortval['stage'])>0) {
							$subjectv=str_replace(',',' ',$subjectval['name']);						
							$coursev=str_replace(',',' ',$courseval['name']);						
							$cohortv=str_replace(',',' ',$cohortval['stage']);						
							$info['cn']=$subjectv.' '.$coursev.' '.$cohortv;
							/* format RDN */
							$coursedn='cn='.$info['cn'].',ou=StdEnrol'.',ou='.$CFG->clientid.',dc='.$CFG->ldapdc1.',dc='.$CFG->ldapdc2;
							/* prepare to get the lot: student-memberUid */
							$cohortstudents=listin_cohort($cohortval['id']);
							$idx=0;
							$members=array();
							foreach ($cohortstudents as $cohortstudentval) {
								/* assign epfusername*/
								$members[$idx]=get_epfusername($cohortstudentval['id']);
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
							$sr=ldap_search($ds, $coursedn, $cn);
							if (ldap_count_entries($ds, $sr) > 0) {
							  // entry exists, delete it
							  $del_res=ldap_delete($ds,$coursedn);
							  if (!$del_res) {
								echo 'could not delete entry: '.$coursedn."\n";
								echo $entries.' entries have been processed'."\n";
								print_r($info);
								die;
							  }
							} else {
								//echo '* entry does no exist *'."\n";
							}

							$add_res=ldap_add($ds, $coursedn, $info);

							if ($add_res==false) {
							  echo 'Unable to insert entry into LDAP DB: ' .$coursedn. "\n";
							  echo $entries.' entries have been processed'."\n";
							  print_r($info);
							  die;
							}
							/* entry counter */
							if (fmod($entries,50.0)==0.0) {
							  if ($entries>=50){
							  //echo '/'.$entries;
								echo '.';
							  }
							$entries++;
							}
						}
					}
				}
			}
		}
	} else {
		echo 'could not bind to the server'."\n";
		die;
	}
} else {
	echo 'could not connect to the server'."\n";
	die;
}

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

<<<<<<< HEAD:admin/httpscripts/ldap_user_enrol.php
>>>>>>> 647f9cc71546b206a8d283af275ca31b86e984a7:admin/httpscripts/ldap_user_enrol.php
?>
