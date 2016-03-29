<?php
/**										fetch_skills.php
 *
 *	@package		Classis
 *	@author		marius@learningdata.ie
 *	@copyright	S T Johnson 2004-2016
 */


/*
 * Gets the statements from report_skill table given the report id and/or subject_id,stage
 *
 * @params integer $rid
 * @params string $bid
 * @params string $pid
 * @params string $stage
 * @return array
 */
function get_report_skill_statements($rid,$bid='%',$pid='',$stage='%',$exact=false){
	if($pid!='' and $pid!=' '){$bid=$pid;}
	if($exact==true){
		$d_statements=mysql_query("SELECT id, name, subtype, rating, rating_name, component_id, profile_id, subject_id,stage FROM report_skill 
						WHERE profile_id='$rid' AND (report_skill.stage='' OR report_skill.stage='%' 
						OR report_skill.stage LIKE '$stage') AND (subject_id LIKE '$bid%' or subject_id='%')
						AND profile_id!=0 ORDER BY rating ASC, id ASC;");
		}
	else{
		$d_statements=mysql_query("SELECT id, name, subtype, rating, rating_name, component_id, profile_id, subject_id,stage FROM report_skill 
						WHERE profile_id='$rid' AND (report_skill.stage='' OR report_skill.stage='%' 
						OR report_skill.stage LIKE '$stage') AND (subject_id='$bid' or subject_id='%')
						AND profile_id!=0 ORDER BY rating ASC, id ASC;");
		}
   	$statements=array();
	while($statement=mysql_fetch_array($d_statements,MYSQL_ASSOC)){
	   	$statements[]=$statement;
	   	}

	/*
	$d_r=mysql_query("SELECT title FROM report WHERE id='$rid'");
	$area=mysql_result($d_r,0);
	$d_s=mysql_query("SELECT id, stage, name, subtype, rating, rating_name, component_id, profile_id, subject_id FROM report_skill 
				WHERE report_skill.profile_id='$area'
				AND (report_skill.stage='' OR report_skill.stage='%' 
					OR report_skill.stage LIKE '$stage') AND profile_id!=0 
				AND (report_skill.subject_id='$bid' OR report_skill.subject_id='%') ORDER BY rating ASC;");
	while($statement=mysql_fetch_array($d_s,MYSQL_ASSOC)){
	   	$statements[]=$statement;
	   	}*/

	return $statements;
	}


/*
 * Adds or updates a new statement in the skill table
 *
 * @params integer $rid
 * @params string $inval, $bid, $instage
 * @params string $insubsubject
 * @params string $insublevel
 * @params string $gradingname
 * @params string $update
 * @params string $instid
 */
function add_report_skill_statement($inval,$bid,$rid,$pid,$instage,$insubsubject,$insublevel,$gradingname,$update=false,$instid=''){
			if(!$update){
				mysql_query("INSERT INTO report_skill SET name='$inval', 
					subject_id='$bid', subtype='$insubsubject',component_id='$bid', stage='$instage',
					profile_id='$rid', rating='$insublevel',rating_name='$gradingname';");
				}
			if($update){
				mysql_query("UPDATE report_skill SET name='$inval', stage='$instage', subtype='$insubsubject', rating='$insublevel',rating_name='$gradingname' WHERE id='$instid';");
				}
	}


/*
 * Deletes a statement given the rid and the statement id
 *
 * @params integer $instid
 * @params string $rid
 */
function delete_report_skill_statement($instid,$rid){
	mysql_query("DELETE FROM report_skill WHERE id='$instid';");
	}

/*
 */
function fetchSubjectReportSkills($sid,$rid,$pid){
	$d_skills=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id  
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid' AND rs.subject_id='$pid';");
	while($s=mysql_fetch_array($d_skills)){$skills[]=$s;}
	foreach($skills as $skill){
		$skid=$skid['skill_id'];
		$d_l=mysql_query("SELECT rsl.id,student_id,report_id,rsl.rating,comment,teacher_id,timestamp,subject_id,stage
								FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id 
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid'  
								AND rsl.skill_id='$skid' ORDER BY rsl.timestamp DESC LIMIT 1;");
		while($log=mysql_fetch_array($d_l)){
			$logs[]=$log;
			}
		}
	return $logs;
	}


/**
 *
 *  Checks to see if any report entries for a bid/pid combination 
 *  have been made for this sid in this report and calculates a colour coded percentage
 *
 */
function calculateProfileScore($rid,$sid,$bid,$pid,$stage='%'){
	$totalno=totalNumberSkills($rid,$pid,$stage);
	$cutoff_rating='1';

	$d_skids=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id  
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid' 
								AND rs.subject_id='$pid' AND (rs.stage='$stage' OR rs.stage='%');");
	while($s=mysql_fetch_array($d_skids)){$skids[]=$s;}
	$tot=0;
	$sum=0;
	$last=0;
	foreach($skids as $skid){
		$skill_id=$skid['skill_id'];
		/* For any old profiles not linked to an assessment then just display their tally. */
		$d_r=mysql_query("SELECT rsl.id,student_id,report_id,rsl.rating,comment,teacher_id,timestamp,subject_id,stage
								FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id 
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid'  
								AND rsl.skill_id='$skill_id' ORDER BY rsl.timestamp DESC LIMIT 1;");
		while($entry=mysql_fetch_array($d_r)){
			$value=$entry['rating'];
			$entrydate=$entry['timestamp'];
			$catid=$entry['skill_id'];
			if(strtotime($entrydate)>$last){
				$lastdate=$entrydate;
				$last=strtotime($lastdate);
				}

			if($value==-1){
				$tot+=1;
				}
			elseif($value==0){
				$tot+=2;
				}
			if($value==1){
				$tot+=3;
				}

			if($value>=$cutoff_rating){
				$sum++;
				}
			}
		}

	$ass=array();
	$ass['stage']=$stage;
	$ass['result']=round(100*($sum/($totalno)));
	$ass['outoftotal']=$totalno*3;
	$ass['value']=$tot;
	$ass['weight']=1;
	$ass['date']=$lastdate;
	$ass['class']='nolite';

	if($ass['result']>=85){$ass['class']='golite';}
	elseif($ass['result']>=60){$ass['class']='gomidlite';}
	elseif($ass['result']>=35){$ass['class']='pauselite';}
	elseif($ass['result']>=10){$ass['class']='midlite';}
	elseif($ass['result']<10 and $ass['result']>0){$ass['class']='outlite';}
	else{$ass['class']='nolite';}

	return $ass;
	}


/**
 *
 *  Checks to see if any report entries for a bid/pid combination 
 *  have been made for this sid in this report and calculates a colour coded percentage
 *
 */
function calculateProfileLevel($rid,$sid,$bid,$pid,$stage='%',$date=''){

	$d_r=mysql_query("SELECT title FROM report WHERE id='$rid'");
	$area=mysql_result($d_r,0);
	$d_c=mysql_query("SELECT COUNT(id) FROM report_skill 
				WHERE report_skill.profile_id='$rid' 
				AND (report_skill.subject_id='$pid' OR report_skill.subject_id='%') 
				AND (report_skill.stage='%' OR report_skill.stage='$stage');");
	$totalno=mysql_result($d_c,0);

	$d_skids=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log WHERE report_id='$rid' AND 
							student_id='$sid';");
	while($s=mysql_fetch_array($d_skids)){$skids[]=$s;}
	$ass=array();
	$tot1=0;
	$tot2=0;
	$tot3=0;
	$tot4=0;
	$tot5=0;

	if($date=='' or $date=='0000-00-00'){
		$date=date('Y-m-d');
		}
	list($y,$m,$d)=explode('-',$date);
	$stepsize=4;
	$step=0;
	$cutoff1=strtotime('now');
	$cutoff2=mktime(0,0,0,$m-($stepsize),$d,$y);
	$cutoff3=mktime(0,0,0,$m-($stepsize*2),$d,$y);
	$cutoff4=mktime(0,0,0,$m-($stepsize*3),$d,$y);
	$cutoff5=mktime(0,0,0,$m-($stepsize*4),$d,$y);
	foreach($skids as $skid){
		$skill_id=$skid['skill_id'];
		/* For any old profiles not linked to an assessment then just display their tally. */
		$d_r=mysql_query("SELECT * FROM report_skill_log WHERE report_id='$rid' AND 
								student_id='$sid' AND skill_id='$skill_id' ORDER BY timestamp DESC LIMIT 1;");
		while($entry=mysql_fetch_array($d_r)){
			$value=$entry['rating'];
			$entrydate=$entry['timestamp'];
			$catid=$entry['skill_id'];
				if($value>0){
					$entrytime=strtotime($entrydate);
					$ass['skids'][]=$entry;
					if($entrytime<=$cutoff1){
						$tot1+=$value;
						}
					if($entrytime<=$cutoff2){
						$tot2+=$value;
						}
					if($entrytime<=$cutoff3){
						$tot3+=$value;
						}
					if($entrytime<=$cutoff4){
						$tot4+=$value;
						}
					if($entrytime<=$cutoff5){
						$tot5+=$value;
						}
					}
				
			}
		}

	$ass['result']=$area;
	$ass['outoftotal']=$totalno;
	$ass['tot']=$tot1;
	$ass['value1']=round(100*$tot1/$totalno);
	$ass['value2']=round(100*$tot2/$totalno);
	$ass['value3']=round(100*$tot3/$totalno);
	$ass['value4']=round(100*$tot4/$totalno);
	$ass['value5']=round(100*$tot5/$totalno);
	$ass['weight']=1;
	$ass['date']='';
	$ass['class']='nolite';

	return $ass;
	}


/**
 *
 *  Checks to see if any report entries for a bid/pid combination 
 *  have been made for this sid in this report and calculates a colour coded percentage
 *
 */
function calculateELGScore($sid,$profid){

	if($profid==''){
		$title="title='ELG' OR title='EYFS2436' OR title='EYFS3648'";
		$d_report=mysql_query("SELECT id FROM report WHERE $title;");
		}
	else{
		$d_report=mysql_query("SELECT report_id AS id FROM ridcatid WHERE categorydef_id='$profid' AND subject_id='profile';");
		}

	$tot=0;
	$ass=array();
	while($report=mysql_fetch_array($d_report)){
		$rid=$report['id'];
		$d_skids=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log WHERE report_id='$rid' AND student_id='$sid';");
		while($s=mysql_fetch_array($d_skids)){$skids[]=$s;}
		foreach($skids as $skid){
			$skill_id=$skid['skill_id'];
			/* For any old profiles not linked to an assessment then just display their tally. */
			$d_r=mysql_query("SELECT * FROM report_skill_log WHERE report_id='$rid' AND 
									student_id='$sid' AND skill_id='$skill_id' ORDER BY timestamp DESC LIMIT 1;");
			while($entry=mysql_fetch_array($d_r)){
				$value=$entry['rating'];
				$entrydate=$entry['timestamp'];
				$catid=$entry['skill_id'];
				if($value>-100){
					if($value==-1){
						$tot+=1;
						}
					elseif($value==0){
						$tot+=2;
						}
					if($value==1){
						$tot+=3;
						}
					}
				}
			}

		$ass['result']=$tot;
		$ass['outoftotal']='';
		$ass['value']=$tot;
		$ass['weight']=1;
		$ass['date']='';
		$ass['class']='nolite';
		}

	return $ass;
	}

/*
 */
function get_student_skillFiles($Student,$rid,$catdefs){

	global $CFG;
	$Files=array();
	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}
	$filedisplay_url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php';

	$sid=$Student['id_db'];
	$catid_list='';
	$joiner='';
	foreach($catdefs as $catdef){
		if($catid_list!=''){$joiner="','";}
		$catid_list.=$joiner. $catdef['id'];
		}

	require_once('eportfolio_functions.php');
	$d_c=mysql_query("SELECT skill_id, comment FROM report_skill_log 
							WHERE skill_id IN ('$catid_list') AND report_id='$rid' AND student_id='$sid' GROUP BY skill_id;");

	if(mysql_num_rows($d_c)>0){
		while($c=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$files=(array)list_files($Student['EPFUsername']['value'],'assessment',$c['skill_id']);
			foreach($files as $file){
				$File=array();
				$fileparam_list='?fileid='.$file['id'].'&location='.$file['location'].'&filename='.$file['name'];
				$File['url']=$filedisplay_url.$fileparam_list;
				list($width, $height, $type, $attr) = getimagesize($CFG->eportfolio_dataroot.'/'.$file['location']);
				$File['height']=$height;
				$File['width']=$width;
				$Files[]=$File;
				}
			}
		}

	return $Files;
	}

/*
 *
 *
 */
function getLastRatings($sid,$rid){
	$d_skids=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log WHERE report_id='$rid' AND 
							student_id='$sid';");
	while($s=mysql_fetch_array($d_skids)){$skids[]=$s;}
	foreach($skids as $skid){
		$skill_id=$skid['skill_id'];
		/* For any old profiles not linked to an assessment then just display their tally. */
		$d_r=mysql_query("SELECT * FROM report_skill_log WHERE report_id='$rid' AND 
								student_id='$sid' AND skill_id='$skill_id' ORDER BY timestamp DESC LIMIT 1;");
		while($entry=mysql_fetch_array($d_r)){
			$logs[]=$entry;
			}
		}
	return $logs;
	}


/*
 *
 *
 */
function totalNumberSkills($rid,$pid,$stage){
	$d_c=mysql_query("SELECT COUNT(report_skill.id) 
						FROM report_skill JOIN report ON report.id=report_skill.profile_id 
						WHERE report.id='$rid' AND report_skill.subject_id='$pid' 
						AND (report_skill.stage='%' OR report_skill.stage='$stage');");
	$total=mysql_result($d_c,0);
	return $total;
	}


/**
 *
 *		Retrieves all report entries for one student in one subject
 *		All report info is pre-fetched in $reportdef['report'].
 *
 */
function fetchSkillLog($reportdef,$sid,$bid,$pid,$skilltype='skill'){

	$Skills=array();
	$Student=fetchStudent_short($sid);
	$rid=$reportdef['report']['id'];
	$Skill['subject']=$bid;
	$Skill['component']=$pid;
	$teachers=array();

	/* These are the check box ratings. */
	if($reportdef['report']['addcategory']=='yes'){
		$ratingname=get_report_ratingname($reportdef,$bid);
		$statements=get_report_skill_statements($rid,$bid,$pid);
		$results=getLastRatings($sid,$rid);
		foreach($results as $result){
			$entry['results'][$result['skill_id']]['rating']=$result['rating'];
			$entry['results'][$result['skill_id']]['skill_id']=$result['skill_id'];
			$entry['results'][$result['skill_id']]['timestamp']=$result['timestamp'];
			$teachers[$result['teacher_id']]=$result['teacher_id'];
			}
		$Skills=(array)fetchSkills($Student,$entry['results'],$statements,$ratingname,$skilltype);
		if($skilltype=='skill' or $skilltype==''){$Skill['Skills']=$Skills;}
		else{$Skill['Categories']=$Skills;}
		}

	foreach($teachers as $teacher){
		$teachername=get_teachername($teacher);
		$Skill['Teacher']=array('id_db'=>''.$enttid, 
							'value'=>''.$teachername);
		}

	if($reportdef['report']['course_id']!='wrapper'){
		$catdefs=get_report_skill_statements($rid,$bid,$pid);
		$Files=(array)get_student_skillFiles($Student,$rid,$catdefs);
		$Skill['Files']=$Files;
		}

	$Skills['Comment'][]=$Skill;
	return $Skills;
	}

/**
 *
 *	Simply checks to see if any report entries for a bid/pid combination 
 *  have been made for this sid in this report. The number of report entries 
 *  are returned.
 *
 */
function checkSkillLog($rid,$sid,$bid,$pid){
	$no=0;
	$d_skills=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id  
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid' AND rs.subject_id='$pid';");
	while($s=mysql_fetch_array($d_skills)){$skills[]=$s;}
	foreach($skills as $skill){
		$skid=$skid['skill_id'];
		$d_l=mysql_query("SELECT rsl.id,student_id,report_id,rsl.rating,comment,teacher_id,timestamp,subject_id,stage
								FROM report_skill_log AS rsl  JOIN report_skill AS rs 
								ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id 
								WHERE rsl.report_id='$rid' AND rsl.student_id='$sid'  
								AND rsl.skill_id='$skid' ORDER BY rsl.timestamp DESC LIMIT 1;");
		$no=mysql_numrows($d_reportentry)+$no;
		}
	return $no;
	}


/**
 *
 *
 *
 */
function fetchProfileStatements($profile_name,$bid,$pid,$sid,$cutoff_date){
	$Student=fetchStudent_short($sid);
	$Statements=array();
	/* This has to iterate over all strands, here called the profilepids,
	 * for this component $pid. 
	 */

	if(!isset($cutoff_rating)){$cutoff_rating=-100;}
	if(!isset($cutoff_level)){$cutoff_level=-100;}
	if(!isset($cutoff_date)){$cutoff_date=strtotime('0000-00-00');}
	if(!isset($cutoff_statno)){$cutoff_statno=1000;}

	if($profile_name=='FS Steps'){

		/* OLD and to be replaced.... */
		$profilepids=(array)list_subject_components($pid,'FS');
		$profilepids[]=array('id'=>$pid,'name'=>'');
		$Statements=array();
		while(list($pidindex,$component)=each($profilepids)){
			$profilepid=$component['id'];
			/* This cutoff rating is just a hack to work with the FS profile*/
			/*TODO properly!*/
			/*This ensures only Reception statements are used for Reception classes*/
			if($Student['YearGroup']['value']=='-1'){$cutoff_rating=0;}
			if($Student['YearGroup']['value']=='0'){$cutoff_rating=3;}
			$d_eidsid=mysql_query("SELECT assessment.description, assessment.id FROM eidsid 
				JOIN assessment ON assessment.id=eidsid.assessment_id WHERE
				eidsid.student_id='$sid' AND eidsid.subject_id='$bid'
				AND eidsid.component_id='$profilepid' AND
				assessment.profile_name='$profile_name' AND
				eidsid.date > '$cutoff_date' AND eidsid.value > '$cutoff_rating';");
			while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
				$topic=$eidsid['description'];
				$d_mark=mysql_query("SELECT comment FROM mark JOIN eidmid ON mark.id=eidmid.mark_id 
							WHERE mark.component_id='$profilepid' AND mark.def_name='$profile_name' AND topic='$topic';");
				if(mysql_num_rows($d_mark)>0){
					$statement=array('statement_text'=>mysql_result($d_mark,0),
								 'counter'=>0,
								 'author'=>'Classis',
								 'rating_fraction'=>1);
					$Statements[]=fetchStatement($statement,1);
					}
				}
			}
	  }
  else{

		/*TODO: have to pass these values for each report. */
	  if(strpos($profile_name,'APP')!==false){
		/*Only displaying those above which are secure. */
		$cutoff_rating=-100;
		/* limit to 6 per area (gives 6 most recent regardless of the level)*/
		$cutoff_level=-100;
		$cutoff_statno=200;
		$profilepids=(array)list_subject_components($pid,'KS1');
		}
	  elseif(strpos($profile_name,'EY')!==false){
		/*Only displaying those above which are secure. */
		$cutoff_rating=1;
		/* limit to 6 per area (gives 6 most recent regardless of the level)*/
		$cutoff_statno=2;
		$profilepids=(array)list_subject_components($pid,'FS');
		}
	  elseif(strpos($profile_name,'DM')!==false){
		/*Only displaying those above which are secure. */
		$cutoff_rating=0;
		/* limit to 6 per area (gives 6 most recent regardless of the level)*/
		$cutoff_statno=2;
		$profilepids=(array)list_subject_components($pid,'FS');
		}
	  elseif(strpos($profile_name,'Goals')!==false){
		/*Only displaying those above which are secure. */
		$cutoff_rating=1;
		/* limit to 6 per area (gives 6 most recent regardless of the level)*/
		$cutoff_statno=1;
		$profilepids=(array)list_subject_components($pid,'FS');
		}

	  $profilepids[]=array('id'=>$pid,'name'=>'');
	  $Statements=array();
	  //trigger_error($profile_name.' '. $pid,E_USER_WARNING);
	  foreach($profilepids as $component){
		  $profilepid=$component['id'];
		  $d_skills=mysql_query("SELECT DISTINCT skill_id FROM report_skill_log AS rsl
		  						 JOIN report_skill AS rs
								 ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id
								 WHERE rsl.report_id=rs.profile_id AND rsl.student_id='$sid'
								 AND rs.subject_id LIKE '%$pid%' GROUP BY rsl.report_id;");
		  while($s=mysql_fetch_array($d_skills)){$skills[]=$s;}
		  foreach($skills as $skill){
			$skid=$skill['skill_id'];
			$d_cat=mysql_query("SELECT rsl.id,rsl.student_id,rsl.report_id,rsl.rating,comment,teacher_id,timestamp,subject_id,stage
									 FROM report_skill_log AS rsl JOIN report_skill AS rs
									 ON rsl.report_id=rs.profile_id AND rsl.skill_id=rs.id
									 WHERE rsl.student_id='$sid' AND rsl.skill_id='$skid'
									 ORDER BY rsl.timestamp DESC LIMIT 1;");
			  $ratings=array();
			  $statno=0;
			  $stat_dates=array();
			  $Statements_new=array();
			  while($cat=mysql_fetch_array($d_cat,MYSQL_ASSOC)){
				  //$catdefs=(array)get_report_categories($cat['report_id'],$bid,$profilepid);
				  $reportdef['report']['id']=$cat['report_id'];
				  $rid=$cat['report_id'];
				  $ratingname=get_report_ratingname($reportdef,$bid);
				  $sts=get_report_skill_statements($rid,$pid,'','',true);
				  $results=getLastRatings($sid,$rid);
				  foreach($results as $result){
					$entry['results'][$result['skill_id']]['rating']=$result['rating'];
					$entry['results'][$result['skill_id']]['skill_id']=$result['skill_id'];
					$entry['results'][$result['skill_id']]['timestamp']=$result['timestamp'];
					}
				  $Skills=(array)fetchSkills($Student,$entry['results'],$sts,$ratingname);
				  if(isset($Skills['Skill'])){
					  foreach($Skills['Skill'] as $Skill){
						  if($Skill['value']>=$cutoff_rating and $Skill['level']>=$cutoff_level 
							 and ($Skill['date']=='' or strtotime($Skill['date'])>=$cutoff_date)){
									 $statno++;
									 $stat_dates[]=strtotime($Skill['date']);
									 $statement=array('statement_text'=>$Skill['label'],
													  'counter'=>0,
													  'author'=>'Classis',
													  'rating_fraction'=>1);
									 $Statements_new[]=fetchStatement($statement,1);
								}
							}
					  }
				  }
			  }
			  /* sort by date and then limit to the cutoff_statno most recent */
			  array_multisort($stat_dates,SORT_DESC,$Statements_new);
			  if($statno>$cutoff_statno){
				  $Statements=array_merge(array_slice($Statements_new,0,$cutoff_statno,true),$Statements);
				  }
			  else{
				  $Statements=array_merge($Statements_new,$Statements);
				  }

		  }

	  }
	return $Statements_new;
	}


/**
 * 
 *
 */
function fetchStatement($statement,$nolevels){
	$Statement=array();
	$Statement['Value']=$statement['statement_text'];
	$Statement['Counter']=$statement['counter'];
	$Statement['Author']=$statement['author'];
	$Statement['Ability']=$statement['rating_fraction']*$nolevels;
	return $Statement;
	}


/**
 *
 * @param array $Statement
 * @param array $Student
 * @return array
 */
function personaliseStatement($Statement,$Student){
	$text=$Statement['Value'];
	if($Student['Gender']['value']=='M'){
		$possessive='his';//~
		$pronoun='he';//^
		$objectpronoun='him';//*
		}
	else{
		$possessive='her';
		$pronoun='she';
		$objectpronoun='her';
		}

	if($Student['PreferredForename']['value']!=''){$forename=$Student['PreferredForename']['value'];}
	else{$forename=$Student['Forename']['value'];}
	$text=str_replace('~',$possessive,$text);
	$text=str_replace('^',$pronoun,$text);
	$text=str_replace('*',$objectpronoun,$text);
	$text=ucfirst($text);
	$text=str_replace('#',$forename,$text);
	$Statement['Value']=$text;

	return $Statement;
	}




/**
 *
 *
 */
function fetchSkills($Student,$results,$statements,$ratingname,$skilltype='skill'){

	$log=array();
	$Skills=array();
	$Skills['ratingname']=$ratingname;
	foreach($results as $result){
		$log['ratings'][$result['skill_id']]=$result['rating'];
		$log['dates'][$result['skill_id']]=$result['timestamp'];
		}

	foreach($statements as $st){
		$Skill=array();
		$skid=$st['id'];
		/* TODO: Use subtype and comment and rating to decorate extra info. */
		$Skill=array('label'=>''.$st['name'],'id_db'=>''.$skid,'value'=>'','date'=>'','level'=>''.$st['rating']);

		/* Only pass catgories which have had a value set. */
		/* TODO: Apply other filters for date and value */
		if(isset($log['ratings'][$skid])){
			$Skill['value']=''.$log['ratings'][$skid];
			$Skill['date']=''.$log['dates'][$skid];
			$Statement=array('Value'=>''.$Skill['label']);
			$Statement=personaliseStatement($Statement,$Student);
			$Skill['label']=''.$Statement['Value'];
			if($skilltype=='skill' or $skilltype==''){
				$Skills['Skill'][]=$Skill;
				}
			else{
				$Skills['Category'][]=$Skill;
				}
			}
		}

	return $Skills;
	}


/**
 *
 * Used to calculate a numerical score based on the profile statements
 * checked.  The result is stored in an assessment linked to the
 * profile for this purpose only. The link depends on the assessment
 * being named the same as the profile report and indeed as the
 * othertype value for the statements.
 *
 * The cut_off rating is used to only count those statements above
 * that value, hard-set hewre to 1 to only count level status=achieved.
 *
 * @param integer $rid
 * @param integer $sid
 * @param string $bid
 * @param string $pid
 * @param string $cat
 * @param string $catdefs
 * @param string $rating_name
 *
 * @return boolean
 */
function update_profile_score($rid,$sid,$bid,$pid,$cat,$catdefs,$rating_name){

	$Student=fetchStudent_short($sid);
	$score=array('result'=>'','value'=>0, 'date'=>'0000-00-00');
	$cutoff_rating='1';

	$eid=get_profile_eid($rid);

	$statno=0;
	$lowvalue=0;
	$Categories=(array)fetchCategories($Student,$cat,$catdefs,$rating_name);
	if(isset($Categories['Category'])){
		foreach($Categories['Category'] as $Category){
			/* Only count positive scores (cutoff_rating fixed at 1) as part of the total. */
			if($Category['value']>=$cutoff_rating){
				$score['value']++;
				}
			elseif($Category['value']<$cutoff_rating){
				$lowvalue++;
				}
			/* Grab the date of the most recent category changed. */
			if(strtotime($Category['date'])>strtotime($score['date'])){$score['date']=$Category['date'];}
			$statno++;
			}
		}

	$catno=sizeof($catdefs);
	if($statno>0 and $eid>-1){
		$score['result']=round(100*($score['value']/$catno));	
		if($score['result']==''){
			$score['result']=$lowvalue;
			$score['value']=0;
			}
		update_assessment_score($eid,$sid,$bid,$pid,$score);
		$result=true;
		}
	else{
		$result=false;
		}

	return $result;
	}

/**
 *	Looks up the grade equivalent of the numerical score.
 *  If $score is empty then an empty $grade string is returned.	
 *  The numerical equivalents for the grades (levels in the grading
 *	scheme) must have integer values.
 *
 *	@param float $score
 *	@param array $grading_grades
 *	@return string
 */
function ratingToGrade($score,$grading_grades){
	/*
	Looks up the grade equivalent of the numerical score.
	If $score is empty then an empty $grade string is returned.	
	The numerical equivalents for the grades (levels in the grading
	scheme) must have integer values.
	*/
	if(is_numeric($score) and $grading_grades!=''){
		$pairs=explode(';', $grading_grades);
		//trigger_error($grading_grades,E_USER_WARNING);
	    $score=round($score);
		$high=sizeof($pairs);
		for($c=0;$c<sizeof($pairs);$c++){
			list($levelgrade,$level)=explode(':',$pairs[$c]);
			if($score>=$level){
				$lowgrade=$levelgrade;
				$lowlevel=$level;
				$high=$c+1;
				}
			}
		$grade=$lowgrade;
		if($high<$c){
			list($highgrade, $highlevel)=explode(':',$pairs[$high]);
			if(($highlevel-$score)<=($score-$lowlevel)){$grade=$highgrade;}
			}
		}
	else{$grade='';}
	return $grade;
	}

/**
 * Used to get the sids from report_skill_log table give a date.
 *
 * @param date $date
 *
 * @return array $sids
 */
function getStatementsSids($date="0000-00-00"){
	$sids=array();
	if($date=="0000-00-00" or $date==""){$date=date('Y-m-d');}
	$d_s=mysql_query("SELECT student_id FROM report_skill_log WHERE timestamp<='$date' GROUP BY student_id;");
	while($sid=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$sids[]=$sid['student_id'];
		}
	return $sids;
	}

/**
 * Used to calculate the total score for all the components.
 *
 * @param integer $sid
 * @param array $rids
 * @param date $assessment_startdate
 * @param date $assessment_date
 * @param string $component
 *
 * @return integer $total
 */
function getStatementsResultsTotals($sid,$rids,$assessment_startdate="",$assessment_date="",$component=""){
	$total=0;
	$results=getStatementsResults($sid,$rids,$assessment_startdate,$assessment_date,$component);
	foreach($results as $component=>$component_total){
		$total+=$component_total;
		}
	return $total;
	}


/**
 * Used to calculate the total scores for each component.
 *
 * @param integer $sid
 * @param array $rids
 * @param date $assessment_startdate
 * @param date $assessment_date
 * @param string $component
 * @param array $profiles
 *
 * @return array $results
 */
function getStatementsResults($sid,$rids,$assessment_startdate="",$assessment_date="",$component="",$profiles=array('ELG','EYFS3648','EYFS2436')){
	$results=array();
	$comp='fs';
	if($assessment_date==""){$assessment_date=date('Y-m-d');}
	if($assessment_startdate==""){$assessment_startdate="0000-00-00";}
	foreach($rids as $rid){
		if($profiles[0]=="APP Framework"){
			$d_r=mysql_query("SELECT title FROM report WHERE title LIKE 'L%' AND id='$rid';");
			$area=mysql_result($d_r,0);
			$comp='app';$score="";
			}
		else{$d_r=mysql_query("SELECT id FROM report WHERE title='ELG' OR title='EYFS3648' OR title='EYFS2436' AND id='$rid';");}
		if(mysql_num_rows($d_r)>0){
			$d_s=mysql_query("SELECT s.skill_id FROM (SELECT skill_id FROM report_skill_log WHERE student_id='$sid' AND report_id='$rid' AND timestamp>'$assessment_startdate' AND timestamp<='$assessment_date' GROUP BY skill_id ORDER BY timestamp DESC) s GROUP BY s.skill_id;");
			while($skill=mysql_fetch_array($d_s,MYSQL_ASSOC)){
				$skid=$skill['skill_id'];
				$d_p=mysql_query("SELECT subject_id FROM report_skill WHERE id='$skid';");
				$component_subject=mysql_result($d_p,0,'subject_id');
				$d_c=mysql_query("SELECT subject_id FROM component WHERE id='$component_subject';");
				$component_profile=mysql_result($d_c,0,'subject_id');
				if($component==$component_profile or $component=="" and $comp=='fs'){
					$skill_result=getStatementResult($sid,$skid,$rid,$assessment_startdate,$assessment_date);
					$results[$component_profile]+=$skill_result['result'];
					}
				else{
					if(!isset($totalno[$component_subject])){
						$tot[$component_subject]=0;
						$totalno[$component_subject]=1;
						}
					$skill_result=getStatementResult($sid,$skid,$rid,$assessment_startdate,$assessment_date);
					$results[$component_subject]['result']+=$skill_result['result'];
					if($skill_result['rating']>0){
						$tot[$component_subject]+=$skill_result['rating'];
						}
					$val=round(100*$tot[$component_subject]/$totalno[$component_subject]);
					if($val>80){$score=$area.'a';}
					elseif($val>60){$score=$area.'b';}
					elseif($val>30){$score=$area.'c';}
					$results[$component_subject]['value']=$score;
					$totalno[$component_subject]++;
					}
				}
			}
		}
	return $results;
	}

/**
 * Used to calculate the score for a single statement.
 *
 * @param integer $sid
 * @param integer $skid
 * @param integer $rid
 * @param date $assessment_startdate
 * @param date $assessment_date
 *
 * @return integer $result
 */
function getStatementResult($sid,$skid,$rid,$assessment_startdate="",$assessment_date=""){
	$result=array();
	if($assessment_date==""){$assessment_date=date('Y-m-d');}
	if($assessment_startdate==""){$assessment_startdate="0000-00-00";}
	$d_r=mysql_query("SELECT title,rating_name FROM report WHERE id='$rid';");
	$profile=mysql_result($d_r,0,'title');
	$ratingname=mysql_result($d_r,0,'rating_name');
	$d_s=mysql_query("SELECT rating FROM report_skill_log WHERE student_id='$sid' AND skill_id='$skid' AND report_id='$rid' AND timestamp>'$assessment_startdate' AND timestamp<='$assessment_date' ORDER BY timestamp DESC LIMIT 1;");
	if(mysql_num_rows($d_s)>0){
		$rating=mysql_result($d_s,0,'rating');
		$d_rn=mysql_query("SELECT name,value,result FROM rating WHERE name LIKE '$ratingname';");
		$result['rating']=$rating;
		if(mysql_num_rows($d_rn)>0){
			while($rn=mysql_fetch_array($d_rn,MYSQL_ASSOC)){
				if($rn['value']==$rating){$result['result']=$rn['result'];}
				}
			}
		else{
			if($rating==-1){
				$result['result']+=1;
				}
			elseif($rating==0){
				$result['result']+=2;
				}
			elseif($rating==1){
				$result['result']+=3;
				}
			}
		}
	return $result;
	}

?>
