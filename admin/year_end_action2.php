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
 * be moved to alumni status.
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

  		$reenrol_assdefs=(array)fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		if(sizeof($reenrol_assdefs)>0){
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
			}
		else{
			/* TODO: if no reenrol defined */
			}

		//$result[]='Promoted year '.$yid.' to '.$nextyid;

		if($type=='alumni'){
			/* Now join the alumni community proper*/
			$students=(array)listin_community(array('id'=>$yearcomid));
			while(list($sindex,$student)=each($students)){
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
			$Students=array();
			reset($feeders);
			while(list($findex,$feeder)=each($feeders)){
				$Transfers=array();
				$Transfers=(array)feeder_fetch('transfer_students',$feeder,$postdata);
				/* NOTE the lowercase of the student index, is a product of xmlreader. */
				if(isset($Transfers['student']) and is_array($Transfers['student'])){
					trigger_error('TRANSFER: '.$yid.' '.sizeof($Transfers['student']),E_USER_WARNING);
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
					if(!isset($Comments['comment']) or !is_array($Comments['comment'])){
						$Comments['comment']=array();
						}
					/*TODO: Backgrounds
					$Backgrounds=(array)$Student['backgrounds'];unset($Student['backgrounds']);
					if(!isset($Backgrounds['background']) or !is_array($Backgrounds['background'])){
						$Backgrounds['background']=array();
						}
					elseif(!isset($Backgrounds['background'][0])){$temp=$Backgrounds['background'];$Backgrounds['background']=array();$Backgrounds['background'][]=$temp;}
					*/
					$Contacts=(array)$Student['contacts'];unset($Student['contacts']);
					if(!isset($Contacts[0])){$temp=$Contacts;$Contacts=array();$Contacts[]=$temp;unset($temp);}

					mysql_query("INSERT INTO student SET surname='';");
					$sid=mysql_insert_id();
					mysql_query("INSERT INTO info SET student_id='$sid';");
					while(list($key,$val)=each($Student)){
						if(isset($val['value']) and is_array($val) and isset($val['field_db'])){
							$field=$val['field_db'];
							$inval=clean_text($val['value']);
							if(isset($val['table_db']) and $val['table_db']=='student'){
								mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid';");
								}
							else{
								mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid';");
								}
							}
						}

					/* Transfer teacher comments*/
					while(list($key,$Comment)=each($Comments['comment'])){
						if(is_array($Comment)){
						  mysql_query("INSERT INTO comments SET student_id='$sid';");
						  $id=mysql_insert_id();
						  while(list($key,$val)=each($Comment)){
							if(is_array($val) and isset($val['value']) and 
							   isset($val['field_db'])){
								$field=$val['field_db'];
								if(isset($val['value_db'])){
									$inval=$val['value_db'];
									}
								else{
									$inval=$val['value'];
									}
								$inval=clean_text($inval);
								mysql_query("UPDATE comments SET $field='$inval' WHERE id='$id';");
								unset($inval);
								}
							}

						  $fixcat='';
						  $Category=$Comment['categories']['category'];
						  $catname=$Category['label'];
						  $rank=$Category['rating']['value'];
						  $d_cat=mysql_query("SELECT id FROM categorydef 
														WHERE name='$catname' AND type='con';");
						  if(mysql_num_rows($d_cat)>0){
							  $fixcatid=mysql_result($d_cat,0);
							  $fixcat=$fixcatid.':'.$rank.';';
							  }

						  mysql_query("UPDATE comments SET category='$fixcat' WHERE id='$id';");
						  mysql_query("UPDATE comments SET teacher_id='' WHERE id='$id';");
						  }
						}

					/* Do the contacts */
					while(list($cindex,$Contact)=each($Contacts)){
						if(isset($Contact['id_db']) and $Contact['id_db']!=-1){
							/*TODO: check for duplicate contacts */
							mysql_query("INSERT INTO guardian SET surname='';");
							$gid=mysql_insert_id();
							mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");

							/*All to get around problem with xmlreader!*/
							$Phones=(array)$Contact['phones'];
							if(!isset($Phones[0])){$temp=$Phones;$Phones=array();$Phones[]=$temp;}
							$Addresses=$Contact['addresses'];
							if(!isset($Addresses[0])){$temp=$Addresses;$Addresses=array();$Addresses[]=$temp;}

							while(list($key,$val)=each($Contact)){
								if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
									$field=$val['field_db'];
									if(isset($val['value_db'])){
										$inval=$val['value_db'];
										}
									else{
										$inval=$val['value'];
										}
									if($val['table_db']=='guardian'){
										mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'");
										}
									elseif($val['table_db']=='gidsid'){
										mysql_query("UPDATE gidsid SET $field='$inval'
											WHERE guardian_id='$gid' AND student_id='$sid'");
										}
									}
								}
							
							while(list($phoneno,$Phone)=each($Phones)){
								//Don't want the id_db from previous school
								$phoneid=-1;
								while(list($key,$val)=each($Phone)){
									if(isset($val['value']) and is_array($val) and isset($val['field_db'])){	
										$field=$val['field_db'];
										if(isset($val['value_db'])){
											$inval=$val['value_db'];
											}
										else{
											$inval=$val['value'];
											}
										if($phoneid=='-1' and $inval!=''){
											mysql_query("INSERT INTO phone SET some_id='$gid';");
											$phoneid=mysql_insert_id();
											}
										mysql_query("UPDATE phone SET $field='$inval'
												WHERE some_id='$gid' AND id='$phoneid';");
										}
									}
								}

							while(list($addressno,$Address)=each($Addresses)){
								//Don't want the id_db from previous school
								$aid=-1;
								while(list($key,$val)=each($Address)){
									if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
										$field=$val['field_db'];
										if(isset($val['value_db'])){
											$inval=$val['value_db'];
											}
										else{
											$inval=$val['value'];
											}
										if($inval!='' and $aid=='-1'){
											mysql_query("INSERT INTO address SET country='';");
											$aid=mysql_insert_id();
											mysql_query("INSERT INTO gidaid SET guardian_id='$gid', address_id='$aid';");
											}
										if($val['table_db']=='address' and isset($aid)){
											mysql_query("UPDATE address SET $field='$inval'	WHERE id='$aid';");
											}
										elseif($val['table_db']=='gidaid' and isset($aid)){
											mysql_query("UPDATE gidaid SET $field='$inval'
													WHERE guardian_id='$gid' AND address_id='$aid';");
											}
										}
									}
								}
							}
						/*End of Contacts*/
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
			$stages[$c2]['cohidnow']=$cohidnow;
			$nextstage='';
			if($c2!=(sizeof($stages)-1)){
				/* 
				 *  Promote to next stage of this course.... 
				 *       ....you could say the stage is set :-)
				 */
				$nextcohid=$stages[$c2+1]['cohidnow'];
				$nextstage=$stages[$c2+1]['id'];
				}
			elseif($nextpostcrid!='1000'){
				/* The last stage of the course are graduating to next course
				 *	   identified in nextpostcrid so grab the first stage only. 
				 */
				$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$nextpostcrid' AND year='$yearnow' AND
						season='S' ORDER BY stage ASC;");
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

			/*
			 * Move subject classes forward to the next stage
			 * preserving as much as possible. Where set numbers
			 * differ between stages this merge any extras into the
			 * last set found. Note who teaches what (tidcid) is not
			 * moved forward.
			 */
			if($nextstage!=''){
				$subjects=list_course_subjects($crid);
				while(list($sindex,$subject)=each($subjects)){
					$classes=(array)list_course_classes($crid,$subject['id'],$stage);
					$nextclasses=(array)list_course_classes($crid,$subject['id'],$nextstage);
					$noclass=sizeof($classes);
					$nextcid='';
					for($c4=0;$c4<$noclass;$c4++){
						$cid=$classes[$c4]['id'];
						if(isset($nextclasses[$c4])){$nextcid=$nextclasses[$c4]['id'];}
						if($nextcid!=''){
							mysql_query("UPDATE cidsid SET class_id='$nextcid' WHERE class_id='$cid';");
							}
						else{
							mysql_query("DELETE FROM cidsid WHERE class_id='$cid';");
							}
						}					
					}
				}
			else{
				/* All classes for the first stage of the course will
				 * always need to be assigned manually. 
				 */
				mysql_query("DELETE FROM cidsid USING cidsid INNER JOIN class 
									WHERE cidsid.class_id=class.id 
									 AND class.course_id='$crid' AND class.stage='$stage';");
				}


			}
		}

	/* Carried forward the assessment and reporting structure for the new
	 * academic year.
	 */

	$d_a=mysql_query("SELECT * FROM assessment WHERE year='$yeargone';");
	while($ass=mysql_fetch_array($d_a,MYSQL_NUM)){
		$dates=(array)split('-',$ass[19]);
		$dates[0]=$dates[0]+1;
		$creation=$dates[0].'-'.$dates[1].'-'.$dates[2];
		$dates=(array)split('-',$ass[20]);
		$dates[0]=$dates[0]+1;
		$deadline=$dates[0].'-'.$dates[1].'-'.$dates[2];
		$d_newa=mysql_query("INSERT INTO assessment (subject_id,component_id,stage,method,element,description,
				label,resultqualifier,resultstatus,outoftotal,derivation,statistics,
				grading_name,course_id,component_status,strand_status,year,season,creation,deadline,profile_name)
				VALUES ('$ass[1]','$ass[2]','$ass[3]','$ass[4]',
				'$ass[5]','$ass[6]','$ass[7]','$ass[8]','$ass[9]','$ass[10]','$ass[11]','$ass[12]',
				'$ass[13]','$ass[14]','$ass[15]','$ass[16]','$yearnow','$ass[18]',
				'$creation','$deadline','$ass[21]');");
		$newassrefs[$ass[0]]=mysql_insert_id();
		}


	/* Go for any reports in the past twelve months. */
	$enddate=$yeargone-1;
	$enddate=$enddate.'-'.date('m').'-'.date('d');

	$d_r=mysql_query("SELECT * FROM report WHERE date > '$enddate';");
	while($rep=mysql_fetch_array($d_r,MYSQL_NUM)){
		$dates=(array)split('-',$rep[2]);
		$dates[0]=$dates[0]+1;
		$date=$dates[0].'-'.$dates[1].'-'.$dates[2];
		$dates=(array)split('-',$rep[3]);
		$dates[0]=$dates[0]+1;
		$deadline=$dates[0].'-'.$dates[1].'-'.$dates[2];
		$d_newa=mysql_query("INSERT INTO report (title,date,deadline,comment,course_id,stage,
				component_status,addcomment,commentlength,commentcomp,addcategory,style,transform,rating_name)
				VALUES ('$rep[1]','$date','$deadline','$rep[4]',
				'$rep[5]','$rep[6]','$rep[7]','$rep[8]','$rep[9]','$rep[10]','$rep[11]','$rep[12]',
				'$rep[13]','$rep[14]');");

		$newrid=mysql_insert_id();
		$newrids[$rep[0]]=$newrid;
		mysql_query("INSERT INTO ridcatid (report_id,categorydef_id,subject_id) 
					SELECT '$newrid',categorydef_id,subject_id 
					FROM ridcatid WHERE report_id='$rep[0]';");
		$d_e=mysql_query("SELECT assessment_id FROM rideid WHERE report_id='$rep[0]';");
		while($rideid=mysql_fetch_array($d_e,MYSQL_NUM)){
			if(isset($newassrefs[$rideid[0]])){$neweid=$newassrefs[$rideid[0]];}
			else{$neweid=$rideid[0];}
			mysql_query("INSERT INTO rideid (report_id,assessment_id) VALUES ('$newrid','$neweid');");
			}
		}

	/* Go through all the new wrappers and update their references. */
	while(list($nindex,$wraprid)=each($newrids)){
		$d_r=mysql_query("SELECT categorydef_id FROM ridcatid WHERE report_id='$wraprid' AND subject_id='wrapper';");
		while($rep=mysql_fetch_array($d_r,MYSQL_NUM)){
			if(isset($newrids[$rep[0]])){$newrid=$newrids[$rep[0]];}
			else{$newrid=$rep[0];}
			mysql_query("UPDATE ridcatid SET categorydef_id='$newrid' 
				WHERE report_id='$wraprid' AND subject_id='wrapper' AND categorydef_id='$rep[0]';");
			}
		}

	/* Empty out the MarkBook of all collumns ready for a fresh start. */
	mysql_query("DELETE FROM score;");
	mysql_query("DELETE FROM midcid;");
	mysql_query("DELETE FROM mark;");
	mysql_query("DELETE FROM eidmid;");
	/* And freshen up the users' history. */
	mysql_query("DELETE FROM history;");
	mysql_query("UPDATE users SET logcount='0';");

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
