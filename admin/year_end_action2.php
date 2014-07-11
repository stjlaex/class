<?php 
/**    								year_end_action2.php
 *
 */

$action='';
$choice='';

include('scripts/sub_action.php');

require_once('lib/curl_calls.php');

if(isset($_POST['rolloverteachers']) and $_POST['rolloverteachers']!=''){$rolloverteachers=$_POST['rolloverteachers'];}else{$rolloverteachers='no';}
if(isset($CFG->feeders) and is_array($CFG->feeders)){$feeders=(array)$CFG->feeders;}
else{$feeders=array();}

$todate=date('Y-m-d');
$currentyear=get_curriculumyear();
$enrolyear=$currentyear+1;
$reenrol_assdefs=(array)fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
if(sizeof($reenrol_assdefs)>0){
	$reenrol_eid=$reenrol_assdefs[0]['id_db'];
	}

mysql_query("INSERT INTO community (name,type,year,capacity,detail) VALUES
				('$enrolyear', 'new', '0000', '0', '')");
$newcomid=mysql_insert_id();
$newcommunity=array('id'=>$newcomid,'type'=>'new','name'=>$enrolyear);
$newpermissions=array();

/** 
 * Two steps: (1) Promote students to next (chosen) pastoral groups; 
 * (2) Promote students to next stage in course or graduate to
 * next (chosen) course. Without a group to graduate to they will
 * be moved to alumni status.
 */

/***** (1) PASTORAL GROUPS *****/

	$yeargroups=(array)list_yeargroups();
	for($c=0;$c<sizeof($yeargroups);$c++){
		$yeargroups[$c]['forms']=(array)list_formgroups($yeargroups[$c]['id']);
		}


	/* Important to count down in reverse order. */
	for($c=(sizeof($yeargroups)-1);$c>-1;$c--){
		$yid=$yeargroups[$c]['id'];
		$nextpostyid=$_POST[$yid];
		if($nextpostyid=='1000'){
			$nextyid=$currentyear;
			$type='alumni';
			}
		else{
			$nextyid=$nextpostyid;
			$type='year';
			if($nextyid==''){$nextyid=$currentyear;}
			}

		if(isset($repeatsids) and sizeof($repeatsids)>0){
			/* Repeats: just rejoin their existing year group. */
			$oldcom=array('type'=>'year','name'=>$yid);
			foreach($repeatsids as $sid){
				join_community($sid,$oldcom);
				$score=array('result'=>'C','value'=>'0','date'=>$todate);
				update_assessment_score($reenrol_eid,$sid,'G','',$score);
				//trigger_error('REPEAT: '.$yid.' '.$sid,E_USER_WARNING);
				}
			}
		unset($repeatsids);


		/* Rename the year community. */
		$community=array('type'=>'year','name'=>$yid);
		$communitynext=array('type'=>'year','name'=>$nextyid,'detail'=>'');
		$yearcomid=update_community($community,$communitynext);
		$yearcommunity=array('id'=>$yearcomid,'type'=>'year','name'=>$nextyid);
		$leavercom=array('id'=>'', 'type'=>'alumni', 'name'=>'P:'.$yid, 'year'=>$currentyear);
		foreach($yeargroups[$c]['forms'] as $findex => $form){
			$fid=$form['name'];
			if($rolloverteachers=='no'){
				$gid=$form['gid'];
				$users=(array)list_group_users_perms($gid);
				foreach($users as $uid=>$user){
					$newpermissions[]=array('name'=>$fid,'gid'=>$gid,'uid'=>$uid);
					$result[]='NewGroup: '.$gid.', UID: '.$uid. ', Form:'.$fid;
					}
				}
			if($nextpostyid!='1000'){
				if(isset($yeargroups[$c+1]['forms'][$findex])){
					$nextfid=$yeargroups[$c+1]['forms'][$findex]['name'];
					}
				else{
					$nextfid=$nextyid.'-'.$fid;
					}
				$type='form';
				}
			else{
				$nextfid=$fid.'-alumni-'.date('Y').'-'.date('m');
				$type='alumni';
				}

			$community=array('type'=>'form','name'=>$fid);
			$communitynext=array('type'=>$type,'name'=>$nextfid,'yeargroup_id'=>$nextyid);
			update_community($community,$communitynext);
			mysql_query("UPDATE student SET form_id='$nextfid' WHERE form_id='$fid';");
			}


		/* Ensure the first yeargroup has the same number of forms as the
		 * precious year ready to receive new students.
		*/
		if($c==0){
			foreach($yeargroups[$c]['forms'] as $ffindex => $form){
				$community=array('type'=>'form','name'=>$form['name'],'yeargroup_id'=>$yid);
				update_community($community);
				}
			}

		mysql_query("UPDATE student SET yeargroup_id='$nextyid' WHERE yeargroup_id='$yid';");


		if(isset($reenrol_eid)){
			$pairs=(array)explode (';', $reenrol_assdefs[0]['GradingScheme']['grades']);
			/* The first reenrol grade is for confirmed reenrolment
			 * and the second for pending so nothing to do for those
			 * here, all students flagged with something else are
			 * going to be unenrolled - they could be transfers to
			 * other schools or leavers or whatever. The special case
			 * being for repeats (R).
			 */
			for($c3=2;$c3<sizeof($pairs);$c3++){
				list($grade, $value)=explode(':',$pairs[$c3]);
				if(strlen($grade)>3){$leavergrade=substr($grade,0,3);}
				else{$leavergrade=$grade;}
				$sids=array();
				$sids=(array)list_reenrol_sids($yearcomid,$reenrol_eid,$leavergrade);
				if($leavergrade=='R'){
					$repeatsids=$sids;
					}
				if($leavergrade!='C' and $leavergrade!='P' and $leavergrade!='R'){
					foreach($sids as $sid){
						join_community($sid,$leavercom);
						}
					}
				}
			}
		else{
			/* TODO: if no reenrol defined */
			/* Currently everyone just moves forward as default. */
			}

		$result[]='Promoted year '.$yid.' to '.$nextyid;


		if($type=='alumni'){
			/* Now join the alumni community proper*/
			$students=(array)listin_community(array('id'=>$yearcomid));
			foreach($students as $student){
				$sid=$student['id'];
				join_community($sid,$leavercom);
				mysql_query("UPDATE info SET leavingdate='$todate' WHERE student_id='$sid';");
				}
			}
		else{
			/* First transfers from feeder schools for the new year group. */
			/* Ignore graduating years (type=alumni) as no new students joining them! */
			$postdata=array();
			$postdata['enrolyear']=$enrolyear;
			$postdata['currentyear']=$currentyear;
			$postdata['yid']=$yid;
			$transfer_Students=array();
			foreach($feeders as $feeder){
				$Transfers=array();
				if($feeder!=''){$Transfers=(array)feeder_fetch('transfer_students',$feeder,$postdata);}
				/* NOTE the lowercase of the student index, is a product of xmlreader. */
				//trigger_error($feeder.' : '.$yid.' '.sizeof($Transfers['student']),E_USER_WARNING);
				if(isset($Transfers['student']) and is_array($Transfers['student'])){
					$result[]='TRANSFER FROM: '.$feeder.': '.$yid.' '.sizeof($Transfers['student']);
					foreach($Transfers['student'] as $Student){
						if(isset($Student['surname']) and is_array($Student['surname'])){
							$previousschool='Transfered from '. $feeder. 
									' (started there '. $Student['entrydate']['value'].') ';
							$Student['entrydate']['value']=$todate;
							$Student['leavingdate']['value']='';
							$Student['enrolmentnotes']['value']=$previousschool. 
									' ' . $Student['enrolmentnotes']['value'];
							$transfer_Students[]=$Student;
							}
						}
					}
				}
			if(is_array($transfer_Students) and sizeof($transfer_Students)>0){
				foreach($transfer_Students as $Student){
					$sid=import_student($Student);
					$tyid=$Student['yeargroup']['value'];
					$d_com=mysql_query("SELECT id FROM community WHERE type='transfer' AND name='$tyid';");
					if(mysql_num_rows($d_com)==0){
						mysql_query("INSERT INTO community (name,type,year,capacity,detail) VALUES
							('$tyid', 'transfer', '$enrolyear', '0', '');");
						$transfercomid=mysql_insert_id();
						}
					else{
						$transfercomid=mysql_result($d_com,0,'id');
						}
					$transfercommunity=array('id'=>$transfercomid,'type'=>'transfer','name'=>$tyid);
					join_community($sid,$yearcommunity);
					join_community($sid,$newcommunity);
					join_community($sid,$transfercommunity);
					mysql_query("UPDATE info SET appmethod='transfer' WHERE student_id='$sid';");
					mysql_query("UPDATE student SET yeargroup_id='".($tyid+1)."' WHERE id='$sid';");
					unset($Student);
					unset($sid);
					}
				}

			/* Now students newly accepted by enrolments. */
			$acceptedcom=array('id'=>'','type'=>'accepted', 
					   'name'=>'AC'.':'.$nextyid,'year'=>$enrolyear);
			//$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
			//$reenrol_eid=$reenrol_assdefs[0]['id_db'];
			$students=(array)listin_community($acceptedcom);
			foreach($students as $student){
				join_community($student['id'],$yearcommunity);
				join_community($student['id'],$newcommunity);
				}
			}
		}

	/* Now students newly accepted by enrolments into the first year
		group ie. the last $yid just finished above. */
	$yearcomid=update_community(array('type'=>'year','name'=>$yid));
	$yearcommunity=array('id'=>$yearcomid,'type'=>'year','name'=>$yid);
	$acceptedcom=array('id'=>'','type'=>'accepted', 
					   'name'=>'AC'.':'.$yid,'year'=>$enrolyear);
	$students=(array)listin_community($acceptedcom);
	foreach($students as $student){
		join_community($student['id'],$yearcommunity);
		join_community($student['id'],$newcommunity);
		}
	/* Now repeats for the first year */
	if(isset($repeatsids) and sizeof($repeatsids)>0){
		$oldcom=array('type'=>'year','name'=>$yid);
		foreach($repeatsids as $sid){
			join_community($sid,$oldcom);
			$score=array('result'=>'C','value'=>'0','date'=>$todate);
			update_assessment_score($reenrol_eid,$sid,'G','',$score);
			//trigger_error('REPEAT: '.$yid.' '.$sid,E_USER_WARNING);
			}
		}


/***** (2) COHORTS AND COURSES*****/

/* Promote students to next stage of the course or graduate to chosen next course. */

	$yeargone=$currentyear;
	$yearnow=$yeargone+1;
	set_curriculumyear($yearnow);
	$result[]='Curriculum year moved forward from '. display_curriculumyear($yeargone).' to '.
						display_curriculumyear($yearnow);

	$courses=(array)list_courses();
	for($c=sizeof($courses)-1;$c>-1;$c--){
		$crid=$courses[$c]['id'];
		/* Currently sequence of the stages for a course depends solely
		 * upon their alphanumeric order - so best have a numeric ending.
		 */
		$courses[$c]['stages']=(array)list_course_stages($crid,$yeargone);
		}

	for($c=sizeof($courses)-1;$c>-1;$c--){
		$crid=$courses[$c]['id'];
		if(isset($_POST["$crid"])){$nextpostcrid=$_POST["$crid"];}
		else{$nextpostcrid='';}

		$stages=$courses[$c]['stages'];
		for($c2=sizeof($stages)-1;$c2>-1;$c2--){
			$stage=$stages[$c2]['id'];
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yeargone);
			$cohidgone=update_cohort($cohort);
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
			$cohidnow=update_cohort($cohort);

			/* Copy teaching classes from year gone forward for new year. */
			mysql_query("INSERT INTO class (name, detail, subject_id, cohort_id) 
		   				SELECT name, detail, subject_id, '$cohidnow' FROM class WHERE cohort_id='$cohidgone';");

			$stages[$c2]['cohidnow']=$cohidnow;
			$nextstage='';
			$nextcoursestage='';
			if($c2!=(sizeof($stages)-1)){
				/** 
				 *  Promote to next stage of this course.... 
				 *       ....you could say the stage is set :-)
				 */
				$nextcohid=$stages[$c2+1]['cohidnow'];
				$nextstage=$stages[$c2+1]['id'];
				}
			elseif($nextpostcrid!='1000'){
				/**
				 *  The last stage of the course are graduating to next course
				 *  identified in nextpostcrid so grab the first stage only. 
				 */
				$d_cohort=mysql_query("SELECT id, stage FROM cohort WHERE
						course_id='$nextpostcrid' AND year='$yearnow' AND
						season='S' ORDER BY stage ASC;");
				$nextcohid=mysql_result($d_cohort,0,0);
				$nextcoursestage=mysql_result($d_cohort,0,1);
				//trigger_error($nextcohid.' :::: '.$nextcoursestage,E_USER_WARNING);
				}
			else{
				/*last stage is graduating and leaving*/
				$nextcohid='';
				//trigger_error($nextcohid.' :::: '.$nextcoursestage,E_USER_WARNING);
				}


			/**
			 * Go through each community of students who were studying
			 * this stage (ie. cohidgone) and promote them to nextcohid. 
			 */
			$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid WHERE cohort_id='$cohidgone';");
			while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
				$comid=$cohidcomid['community_id'];
				if($nextcohid!=''){
					mysql_query("INSERT INTO cohidcomid SET
								cohort_id='$nextcohid', community_id='$comid';");
					}
				else{
					$result[]='Community '.$comid.' graduated to leave.';
					}
				}

			/*
			 * Move subject classes forward to the next stage
			 * preserving as much as possible. Where set numbers
			 * differ between stages then merge any extras into the
			 * last set found. 
			 *
			 * Note who teaches what (tidcid) is not moved forward.
			 */
			$subjects=list_course_subjects($crid,'A',$yeargone);
			foreach($subjects as $subject){
				$classes=(array)list_course_classes($crid,$subject['id'],$stage,$yeargone);

				if($nextstage!=''){
					$nextclasses=(array)list_course_classes($crid,$subject['id'],$nextstage,$yearnow);
					}
				else{
					/* All classes for the first stage of the course will
					 * always need to be assigned manually. 
					 */
					$nextclasses=(array)list_course_classes($nextpostcrid,$subject['id'],$nextcoursestage,$yearnow);
					}

				foreach($classes as $class){
					$cid=$class['id'];
					$cname=$class['name'];

					/* Move students forward to a next class for this subject. */
					$nextclass=array_shift($nextclasses);
					if(!is_null($nextclass)){
						$nextcid=$nextclass['id'];
						mysql_query("INSERT INTO cidsid (class_id, student_id) SELECT '$nextcid', student_id 
											FROM cidsid JOIN student ON student.id=student_id WHERE class_id='$cid' AND yeargroup_id!='NULL';");
						}

					/* Assign teachers to */
					$teachers=(array)list_class_teachers($cid);
					foreach($teachers as $teacher){
						$teacherid=$teacher['id'];
						$d_c=mysql_query("SELECT id FROM class WHERE name='$cname' AND cohort_id='$cohidnow';");
						$newcid=mysql_result($d_c,0);
						mysql_query("INSERT INTO tidcid (teacher_id, class_id) SELECT '$teacherid', class.id FROM class 
									WHERE class.name='$cname' AND class.cohort_id='$cohidnow';");
						}
					}
				}
			}
		}



	/**
	 * Carry forward the curriculum structure.
	 */
	mysql_query("INSERT INTO component (id, course_id,subject_id,status,sequence,weight,year) 
					SELECT id, course_id,subject_id,status,sequence,weight,'$enrolyear' 
					FROM component WHERE year='$currentyear';");

	/**
	 * Carry forward the assessment and reporting structure for the new
	 * academic year.
	 */

	$d_a=mysql_query("SELECT id,subject_id,component_id,stage,method,element,description,
				label,resultqualifier,resultstatus,outoftotal,derivation,statistics,
				grading_name,course_id,component_status,strand_status,year,season,creation,deadline,profile_name 
				FROM assessment WHERE year='$yeargone';");
	while($ass=mysql_fetch_array($d_a,MYSQL_ASSOC)){
		$d_r=mysql_query("SELECT id FROM report WHERE title='".$ass['description']."' AND course_id='".$ass['course_id']."';");
		if(mysql_num_rows($d_r)>0){
			/* Reports which are really skills profiles need to be
			   treated differently - these just roll forward year on
			   year instead of being generated afresh. 
			*/
			$newassrefs[$ass['id']]=$ass['id'];
			$profile_rid=mysql_result($d_r,0);
			mysql_query("UPDATE report SET year='$yearnow' WHERE id='$profile_rid';");
			mysql_query("UPDATE assessment SET year='$yearnow' WHERE id='".$ass['id']."';");
			$result[]='Assessment '.$ass['id'].' updated';
			$AssDef=generate_assessment_columns($ass['id'],'yes');
			}
		else{
			$dates=(array)explode('-',$ass['creation']);
			$dates[0]=$dates[0]+1;
			$creation=$dates[0].'-'.$dates[1].'-'.$dates[2];
			$dates=(array)explode('-',$ass['deadline']);
			$dates[0]=$dates[0]+1;
			$deadline=$dates[0].'-'.$dates[1].'-'.$dates[2];
			$d_newa=mysql_query("INSERT INTO assessment (subject_id, component_id, stage, method, element, description,
				label, resultqualifier, resultstatus, outoftotal, derivation, statistics,
				grading_name, course_id, component_status, strand_status, year, season, creation, deadline, profile_name, lock_level)
				VALUES ('".$ass['subject_id']."','".$ass['component_id']."','".$ass['stage']."','".$ass['method']."',
				'".$ass['element']."','".$ass['description']."','".$ass['label']."','".$ass['resultqualifier']."','".$ass['resultstatus']."','".$ass['outoftotal']."','".$ass['derivation']."','".$ass['statistics']."',
				'".$ass['grading_name']."','".$ass['course_id']."','".$ass['component_status']."','".$ass['strand_status']."','$yearnow','".$ass['season']."',
				'$creation','$deadline','".$ass['profilename']."','0');");
			$newassrefs[$ass['id']]=mysql_insert_id();
			$result[]='New assessment '.$newassrefs[$ass['id']];
			//$AssDef=generate_assessment_columns($newassrefs[$ass['id']],'yes');
			}
		}

	$d_r=mysql_query("SELECT id, title, date, attendancestartdate, deadline, comment, course_id, stage, subject_status,
				component_status, addcomment, commentlength, commentcomp, addcategory, addphotos, style, transform, rating_name, year 
				FROM report WHERE year='$yeargone';");
	while($rep=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$dates=(array)explode('-',$rep['date']);
			$dates[0]=$dates[0]+1;
			$date=$dates[0].'-'.$dates[1].'-'.$dates[2];
			$dates=(array)explode('-',$rep['attendancestartdate']);
			$dates[0]=$dates[0]+1;
			$deadline=$dates[0].'-'.$dates[1].'-'.$dates[2];
			$dates=(array)explode('-',$rep['']);
			$dates[0]=$dates[0]+1;
			$attendancestartdate=$dates[0].'-'.$dates[1].'-'.$dates[2];
			$d_newa=mysql_query("INSERT INTO report (title, date, attendancestartdate, deadline, comment, course_id, stage, subject_status,
				component_status, addcomment, commentlength, commentcomp, addcategory, addphotos, style, transform, rating_name, year)
				VALUES ('".$rep['title']."','$date','$attendancestartdate','$deadline','".$rep['comment']."',
				'".$rep['course_id']."','".$rep['stage']."','".$rep['subject_status']."','".$rep['component_status']."','".$rep['addcomment']."','".$rep['commentlength']."','".$rep['commentcomp']."','".$rep['addcategory']."','".$rep['addphotos']."',
				'".$rep['style']."','".$rep['transform']."','".$rep[rating_name]."','$yearnow');");
			$newrid=mysql_insert_id();
			$newrids[$rep['id']]=$newrid;
			mysql_query("INSERT INTO ridcatid (report_id,categorydef_id,subject_id) 
					SELECT '$newrid',categorydef_id,subject_id 
					FROM ridcatid WHERE report_id='".$rep['id']."';");
			$d_e=mysql_query("SELECT assessment_id FROM rideid WHERE report_id='".$rep['id']."';");
			while($rideid=mysql_fetch_array($d_e,MYSQL_ASSOC)){
				if(isset($newassrefs[$rideid['assessment_id']])){$neweid=$newassrefs[$rideid['assessment_id']];}
				else{$neweid=$rideid['assessment_id'];}
				mysql_query("INSERT INTO rideid (report_id,assessment_id) VALUES ('$newrid','$neweid');");
				}
			$result[]='Report '.$newrid.' updated';
		}


	/* Go through all the new wrappers and update their references. */
	foreach($newrids as $wraprid){
		$d_r=mysql_query("SELECT categorydef_id FROM ridcatid WHERE report_id='$wraprid' AND subject_id='wrapper';");
		while($rep=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			if(isset($newrids[$rep['categorydef_id']])){$newrid=$newrids[$rep['categorydef_id']];}
			else{$newrid=$rep['categorydef_id'];}
			mysql_query("UPDATE ridcatid SET categorydef_id='$newrid' 
				WHERE report_id='$wraprid' AND subject_id='wrapper' AND categorydef_id='".$rep['categorydef_id']."';");
			$result[]='Wrapper '.$newrid.' updated';
			}
		}

	if($rolloverteachers=='no' and count($newpermissions)>0){
		foreach($newpermissions as $group){
			$uid=$group['uid'];
			$name=$group['name'];
			$oldgid=$group['gid'];
			$oldperms=array('r'=>0,'w'=>0,'x'=>0,'e'=>0);
			update_staff_perms($uid,$oldgid,$oldperms);
			$d_g=mysql_query("SELECT groups.gid FROM community JOIN groups ON groups.community_id=community.id WHERE community.name='$name' AND community.type='form' AND community.year='0000';");
			$gid=mysql_result($d_g,0,'gid');
			$newperms=array('r'=>1,'w'=>1,'x'=>1,'e'=>1);
			update_staff_perms($uid,$gid,$newperms);

			$result[]='Group: '.$gid.', UID: '.$uid. ', Form:'.$name;
			}
		}

	/* Freshen up the users' history. */
	mysql_query("DELETE FROM history;");
	mysql_query("UPDATE users SET logcount='0';");


	include('scripts/results.php');
	include('scripts/redirect.php');
?>
