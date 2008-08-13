<?php 
/**    								year_end_action2.php
 *
 */

$action='';
$choice='';

include('scripts/sub_action.php');

require_once('lib/curl_calls.php');

if(isset($CFG->feeders) and is_array($CFG->feeders)){$feeders=(array)$CFG->feeders;}
else{$feeders=array();}

$todate=date('Y-m-d');
$currentyear=get_curriculumyear();
$enrolyear=$currentyear+1;

/** 
 * Two steps: (1) Promote students to next (chosen) pastoral groups; 
 * (2) Promote students to next stage in course or graduate to
 * next (chosen) course. Without a group to graduate to they will
 * moved to alumni status.
 */

/***** (1) PASTORAL GROUPS *****/

	$yeargroups=(array)list_yeargroups();
	for($c=0;$c<sizeof($yeargroups);$c++){
		$yeargroups[$c]['forms']=(array)list_formgroups($yeargroups[$c]['id']);
		}

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

		/* Rename the year community. */
		$community=array('type'=>'year','name'=>$yid);
		$communitynext=array('type'=>'year','name'=>$nextyid,'detail'=>'');
		$yearcomid=update_community($community,$communitynext);
		$yearcommunity=array('id'=>$yearcomid,'type'=>'year','name'=>$nextyid);
		$leavercom=array('id'=>'','type'=>'alumni', 
							 'name'=>'P:'.$yid,'year'=>$currentyear);
		while(list($index,$form)=each($yeargroups[$c]['forms'])){
			$fid=$form['id'];
			if($nextpostyid!='1000'){
				if(isset($yeargroups[$c+1]['forms'][$index])){
					$nextfid=$yeargroups[$c+1]['forms'][$index]['id'];
					}
				else{
					$nextfid=$fid.'-'.date('Y').'-'.date('m');
					}
				$type='form';
				}
			else{
				$nextfid=$fid.'-alumni-'.date('Y').'-'.date('m');
				$type='alumni';
				}

			$community=array('type'=>'form','name'=>$fid);
			$communitynext=array('type'=>$type,'name'=>$nextfid);
			update_community($community,$communitynext);
			mysql_query("UPDATE student SET form_id='$nextfid' WHERE form_id='$fid';");
			}

		mysql_query("UPDATE student SET yeargroup_id='$nextyid' WHERE yeargroup_id='$yid';");

  		$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		$reenrol_eid=$reenrol_assdefs[0]['id_db'];
		$pairs=(array)explode (';', $reenrol_assdefs[0]['GradingScheme']['grades']);
		/* The first reenrol grade is for confirmed reenrolment and
		the last for repeats so nothing to do for those here, all
		students flagged with something else are going to be
		unenrolled - they could be transfers to other schools or
		leavers or whatever. 
		*/
		for($c3=1;$c3<sizeof($pairs);$c3++){
			list($grade, $value)=split(':',$pairs[$c3]);
			if(strlen($grade)>3){$leavergrade=substr($grade,0,3);}
			else{$leavergrade=$grade;}
			$sids=array();
			$sids=(array)list_reenrol_sids($yearcomid,$reenrol_eid,$leavergrade);
			while(list($sindex,$sid)=each($sids)){
				join_community($sid,$leavercom);
				}
			
			}
		$result[]='Promoted year '.$yid.' to '.$nextyid;

		if($type=='alumni'){
			/* Now join the alumni community proper*/
			$students=(array)listin_community(array('id'=>$yearcomid));
			while(list($sindex,$student)=each($students)){
				join_community($student['id'],$leavercom);
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
			$Students=array();
			reset($feeders);
			while(list($findex,$feeder)=each($feeders)){
				$Transfers=array();
				$Transfers=(array)feeder_fetch('transfer_students',$feeder,$postdata);
				/*NOTE the lowercase of the student index, a product of xmlreader*/
				if(isset($Transfers['student']) and is_array($Transfers['student'])){
					$result[]='TRANSFER: '.$yid.' '.sizeof($Transfers['student']);
					while(list($tindex,$Student)=each($Transfers['student'])){
						if(isset($Student['surname']) and is_array($Student['surname'])){
							$previousschool='Transfered from '. $feeder. 
									' (started there '. $Student['entrydate']['value'].') ';
							$Student['entrydate']['value']=$todate;
							$Student['enrolmentnotes']['value']=$previousschool. 
									' ' . $Student['enrolmentnotes']['value'];
							$Students[]=$Student;
							}
						}
					}
				}

			if(is_array($Students) and sizeof($Students)>0){
				while(list($index,$Student)=each($Students)){
					$Comments=(array)$Student['comments'];unset($Student['comments']);
					if(!isset($Comments['comment']) 
					   or !is_array($Comments['comment'])){
						$Comments['comment']=array();
						}

					/*TODO: Transfer backgrounds
					$Backgrounds=$Student['backgrounds'];unset($Student['backgrounds']);
					*/

					mysql_query("INSERT INTO student SET surname='';");
					$sid=mysql_insert_id();
					mysql_query("INSERT INTO info SET student_id='$sid';");
					while(list($key,$val)=each($Student)){
						if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
							$field=$val['field_db'];
							$inname=$field;
							$inval=clean_text($val['value']);
							if(isset($val['table_db']) and $val['table_db']=='student'){
								mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid';");
								}
							else{
								mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid';");
								}
							}
						}

					while(list($key,$Comment)=each($Comments['comment'])){
						if(is_array($Comment)){
						  mysql_query("INSERT INTO comments SET student_id='$sid';");
						  $id=mysql_insert_id();
						  while(list($key,$val)=each($Comment)){
							if(is_array($val) and isset($val['value']) and 
							   isset($val['field_db'])){
								$field=$val['field_db'];
								$inname=$field;
								if(isset($val['value_db'])){
									$inval=$val['value_db'];
									}
								else{
									$inval=$val['value'];
									}
								$inval=clean_text($inval);
								mysql_query("UPDATE comments 
										SET $field='$inval'	WHERE id='$id';");
								unset($inval);
								}
							}
						  mysql_query("UPDATE comments 
										SET teacher_id='' WHERE id='$id';");
						  }
						}
					join_community($sid,$yearcommunity);
					}
				}

			/* Now students newly accepted by enrolments. */
			$acceptedcom=array('id'=>'','type'=>'accepted', 
					   'name'=>'AC'.':'.$nextyid,'year'=>$enrolyear);
			//$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
			//$reenrol_eid=$reenrol_assdefs[0]['id_db'];
			$students=(array)listin_community($acceptedcom);
			while(list($sindex,$student)=each($students)){
				join_community($student['id'],$yearcommunity);
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
   	while(list($sindex,$student)=each($students)){
		join_community($student['id'],$yearcommunity);
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
			upon their alphanumeric order - so best have a numeric ending*/
		$courses[$c]['stages']=(array)list_course_stages($crid);
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
			$stages[$c2]['cohidnow']=$cohidnow;
			if($c2!=(sizeof($stages)-1)){
				/*just promote to next stage of this course*/
				$nextcohid=$stages[$c2+1]['cohidnow'];
				}
			elseif($nextpostcrid!='1000'){
				   /* The last stage of the course are graduating to next course
						identified in nextpostcrid*/
				$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$nextpostcrid' AND year='$yearnow' AND
						season='S' AND stage!='END' ORDER BY stage ASC;");
				$nextcohid=mysql_result($d_cohort,0,0);
				}
			else{
				/*last stage is graduating and leaving*/
				$nextcohid='';
				}


			/* Go through each community of students who were studying
				this stage (ie. cohidgone) and promote them to nextcohid. */
			$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid 
											WHERE cohort_id='$cohidgone';");
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
			}
		}


	mysql_query("DELETE FROM cidsid");
	mysql_query("DELETE FROM score");
	mysql_query("DELETE FROM mark");
	mysql_query("DELETE FROM midcid");
	mysql_query("DELETE FROM eidmid");
	mysql_query("DELETE FROM history");
	mysql_query("UPDATE users SET logcount='0'");

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
