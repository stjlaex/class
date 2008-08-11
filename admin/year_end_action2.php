<?php 
/**    								year_end_action2.php
 *
 */

$action='';
$choice='';

include('scripts/sub_action.php');

require_once('lib/curl_calls.php');

	/* Promote students to chosen pastoral groups*/
	$years=array();
	$c=0;
	$d_yeargroup=mysql_query("SELECT id, sequence, section_id, name FROM
							yeargroup ORDER BY sequence ASC;");
	while($years[]=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$yid=$years[$c]['id'];
		$d_form=mysql_query("SELECT id FROM form WHERE
							yeargroup_id='$yid' ORDER BY id DESC;");
		$years[$c]['fids']=array();
		while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
			$years[$c]['fids'][]=$form['id'];
			}
		$c++;
		}

	for($c=(sizeof($years)-2);$c>-1;$c--){
		$yid=$years[$c]['id'];
		$nextpostyid=$_POST[$yid];
		if($nextpostyid=='1000'){
			$nextyid=date('Y');
			$type='alumni';
			}
		else{
			$nextyid=$nextpostyid;
			$type='year';
			if($nextyid==''){$nextyid=date('Y');}
			}
		$community=array('type'=>'year','name'=>$yid);
		$communitynext=array('type'=>$type,'name'=>$nextyid);
		update_community($community,$communitynext);

		while(list($index,$fid)=each($years[$c]['fids'])){
			if($nextpostyid!='1000'){
				if(isset($years[$c+1]['fids'][$index])){$nextfid=$years[$c+1]['fids'][$index];}
				else{$nextfid=$fid.'-'.date('Y').'-'.date('m');}
				$type='form';
				}
			else{
				$nextfid=$fid.'-form-'.date('Y').'-'.date('m');
				$type='alumni';
				}
			$community=array('type'=>'form','name'=>$fid);
			$communitynext=array('type'=>$type,'name'=>$nextfid);
			update_community($community,$communitynext);

			mysql_query("UPDATE student SET form_id='$nextfid' WHERE form_id='$fid';");
			$result[]='Promoted form '.$fid.' to '.$nextfid;
			}

		mysql_query("UPDATE student SET yeargroup_id='$nextyid' WHERE yeargroup_id='$yid';");

			$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
			$reenrol_eid=$reenrol_assdefs[0]['id_db'];
			$pairs=explode (';', $reenrol_assdefs[0]['GradingScheme']['grades']);
			$grades=array();
			/*The first reenrol grade is for confirmed reenrolment and
			nothing to do, all students flagged with something else
			are going to be unenrolled - they could be transfer to
			other schools or leavers or whatever.*/
			$leavercom=array('id'=>'','type'=>'alumni', 
									 'name'=>'P:'.$yid,'year'=>date('Y'));
			for($c3=1;$c3<sizeof($pairs);$c3++){
				list($grade['result'], $grade['value'])=split(':',$pair[$c3]);
				$sids=(array)list_reenrol_sids($yearcomid,$reenrol_eid,$grade['result']);
				while(list($sindex,$sid)=each($sids)){
					$oldcommunities=join_community($sid,$leavercom);
					}
				}


		$result[]='Promoted year '.$yid.' to '.$nextyid;

		/* Ignore graduating years (type=alumni) as no new students joining them! */
		if($type!='alumni'){
			$communitynext=array('type'=>'year','name'=>$nextyid);

			/* First transfers from feeder schools for the new year group. */
			$postdata=array();
			$postdata['enrolyear']=get_curriculumyear()+1;
			$postdata['yid']=$yid;
			$Students=array();
			reset($CFG->feeders);
			while(list($findex,$feeder)=each($CFG->feeders)){
				$Transfers=array();
				$Transfers=(array)feeder_fetch('transfer_students',$feeder,$postdata);
				/*NOTE the lowercase of the student index, a product of xmlreader*/
				if(isset($Transfers['student']) and is_array($Transfers['student'])){
					$result[]='TRANSFER: '.$yid.' '.sizeof($Transfers['student']);
					while(list($tindex,$Student)=each($Transfers['student'])){
						if(isset($Student['surname']) and is_array($Student['surname'])){
							$surname=$Student['surname']['value'];
							trigger_error('TRANSFER '.$nextyid.' : '.$surname,E_USER_WARNING);
							$Student['surname']['value']='TRANSFER'.$surname;
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
					join_community($sid,$communitynext);
					}
				}

			/* Now students newly accepted by enrolments. */


			$reenrol_assdefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
			$reenrol_eid=$reenrol_assdefs[0]['id_db'];
			$sids=(array)list_reenrol_sids($yearcomid,$reenrol_eid,'C');
			while(list($sindex,$sid)=each($sids)){
				}



			}
		}

		
	/* Promote students to next stage of course or graduate to chosen next course. */
	$courses=array();
	$c=0;
	$d_course=mysql_query("SELECT id, sequence, section_id, name FROM
							course ORDER BY sequence DESC");
	while($courses[]=mysql_fetch_array($d_course,MYSQL_ASSOC)){
		$crid=$courses[$c]['id'];
		$season='S';/*currently restricted to a single season value*/
		$yearnow=get_curriculumyear($crid);
		/*currently sequence of the stages for a course depends solely
			upon their alphanumeric order - so best have a numeric ending*/
		/*will fail if the stages have changed between years!!!*/
		$d_stage=mysql_query("SELECT stage FROM cohort WHERE
				course_id='$crid' AND year='$yearnow' AND
				season='$season' AND stage!='END' ORDER BY stage DESC;");
		$courses[$c]['stages']=array();
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$courses[$c]['stages'][]=array('stage'=>$stage['stage'],'newcohid'=>'');
			}
		$c++;
		}

	for($c=0;$c<sizeof($courses);$c++){
		$crid=$courses[$c]['id'];
		if(isset($_POST["$crid"])){$nextpostcrid=$_POST["$crid"];}
		   else{$nextpostcrid='';}

		$season='S';/*currently restricted to a single season value*/
		$yearnow=get_curriculumyear($crid);
		$yeargone=$yearnow-1;
		$stages=$courses[$c]['stages'];
		for($c2=0;$c2<sizeof($stages);$c2++){
			$stage=$stages[$c2]['stage'];
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yeargone);
			$cohidgone=update_cohort($cohort);
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
			$stages[$c2]['cohidnow']=update_cohort($cohort);
			if($c2==0 and $nextpostcrid!='1000'){
				/*last stage of course are graduating to next course*/
				$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$nextpostcrid' AND year='$yearnow' AND
						season='$season' AND stage!='END' ORDER BY stage ASC");
				$nextcohid=mysql_result($d_cohort,0,0);

				}
			elseif($nextpostcrid!='1000'){
				/*just promote to next stage of this course*/
				$nextcohid=$stages[$c2-1]['cohidnow'];
				}
			else{
				/*last stage is graduating and leaving*/
				$nextcohid='';
				}


			/*go through each community of students who were studying
			this stage and promote them*/
			$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid 
											WHERE cohort_id='$cohidgone'");
			while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
				$comid=$cohidcomid['community_id'];
				if($nextcohid!=''){
					mysql_query("INSERT INTO cohidcomid SET
								cohort_id='$nextcohid', community_id='$comid'");
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
