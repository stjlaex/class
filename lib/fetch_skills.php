<?php
/**										fetch_skills.php
 *
 *	@package		Classis
 *	@author		marius@learningdata.ie
 *	@copyright	S T Johnson 2004-2014
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
						AND profile_id!=0 ORDER BY rating ASC;");
		}
	else{
		$d_statements=mysql_query("SELECT id, name, subtype, rating, rating_name, component_id, profile_id, subject_id,stage FROM report_skill 
						WHERE profile_id='$rid' AND (report_skill.stage='' OR report_skill.stage='%' 
						OR report_skill.stage LIKE '$stage') AND (subject_id='$bid' or subject_id='%')
						AND profile_id!=0 ORDER BY rating ASC;");
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
 * Used to migrate the statements from categorydef table to report_skill
 */
function migrate_statements(){
	$catdefs=array();
	/*Does the statements first*/
	/*All cat type*/
	//mysql_query("TRUNCATE report_skill;");
	//mysql_query("TRUNCATE report_skill_log;");

	$d_categorydef=mysql_query("SELECT id, name, subtype, rating,  
				 rating_name, subject_id, stage, othertype FROM categorydef 
				WHERE type='cat' AND subject_id!='';");
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
		$d_report=mysql_query("SELECT id,title FROM report WHERE title='".$catdef['othertype']."';");
		/*adds the report id instead of title in the array*/
		if($catdef['othertype']!=''){
			while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
				//$reports[]=$report;
				if($report['title']==$catdef['othertype']){$catdef['othertype']=$report['id'];}
				}
			}
		$d_ridcatid=mysql_query("SELECT report_id, categorydef_id, subject_id FROM ridcatid WHERE categorydef_id='".$catdef['id']."';");
		/*gets the report id from ritcatid table and adds it in the array*/
		if($catdef['othertype']==''){
			while($ridcatid=mysql_fetch_array($d_ridcatid,MYSQL_ASSOC)){
				//$ridcatids[]=$ridcatid;
				if($ridcatid['subject_id']==$catdef['subject_id']){$catdef['othertype']=$ridcatid['report_id'];}
				}
			}
		$catdefs[]=$catdef;
		}
	/*Inserts all statements into report_skill table*/
	foreach($catdefs as $catdef){
		$id=$catdef['id'];
		/*for single quotes in sql*/
		$name=addslashes($catdef['name']);
		$subject_id=$catdef['subject_id'];
		$subtype=$catdef['subtype'];
		$component_id=$catdef['subject_id'];
		$stage=$catdef['stage'];
		$profile_id=$catdef['othertype'];
		$rating=$catdef['rating'];
		$rating_name=addslashes($catdef['rating_name']);
		mysql_query("INSERT INTO report_skill SET id='$id', name='$name', 
					subject_id='$subject_id', subtype='$subtype',component_id='$component_id', stage='$stage',
					profile_id='$profile_id', rating='$rating',rating_name='$rating_name';");
		}
	
	/*Removes statements from ridcatid and categorydef tables*/
	/*foreach($catdefs as $catdef){
		$id=$catdef['id'];
		$name=addslashes($catdef['name']);
		$subject_id=$catdef['subject_id'];
		$subtype=$catdef['subtype'];
		$component_id=$catdef['subject_id'];
		$stage=$catdef['stage'];
		$profile_id=$catdef['othertype'];
		$rating=$catdef['rating'];
		$rating_name=addslashes($catdef['rating_name']);
		mysql_query("INSERT INTO report_skill SET id='$id', name='$name', 
					subject_id='$subject_id', subtype='$subtype',component_id='$component_id', stage='$stage',
					profile_id='$profile_id', rating='$rating',rating_name='$rating_name';");
		}*/
	}

/*
 * Used to migrate the statements results to report_skill_log
 */
function migrate_statements_results(){
	/*Does the reportentry ratings*/
	/*Selects only the ratings entries*/
	$d_reportentries=mysql_query("SELECT * FROM reportentry WHERE category!='';");
	while($reportentry=mysql_fetch_array($d_reportentries,MYSQL_ASSOC)){
		$entries[]=$reportentry;
		}
	/*Inserts all results into report_skill_log table*/
	$i=0;
	foreach($entries as $entry){
		$student_id=$entry['student_id'];
		$report_id=$entry['report_id'];
		$comment=$entry['comment'];
		$teacher_id=$entry['teacher_id'];
		$srds=explode(';',$entry['category']);
		foreach($srds as $srd){
			$elements=explode(':',$srd);
			$skid=$elements[0];
			$rating=$elements[1];
			$timestamp=$elements[2];
			if($skid!=0 and $skid!=''){
				mysql_query("INSERT INTO report_skill_log SET report_id='$report_id', 
							 student_id='$student_id', skill_id='$skid', rating='$rating',
							 comment='$comment', teacher_id='$teacher_id',timestamp='$timestamp';");
				}
			}
		}
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
								 'author'=>'ClaSS',
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
													  'author'=>'ClaSS',
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
 * Used to update the score for the terms totals assessments.
 * 
 * @param array $sids
 */
function updateTermTotals($sids,$year="",$profile=""){
	if($profile=="app"){$values=getTermsValues($year,array("APP Framework"));$cstatus="AV";$profile_name="APP Framework";}
	else{$values=getTermsValues($year);$cstatus="V";$profile_name="";}
	$terms=$values['terms'];
	$components=$values['components'];
	$year=$values['year'];
	$rids=$values['rids'];
	$dates=$values['dates'];
	$lastdate=$dates['firstentry'];
	$d_a=mysql_query("SELECT id FROM assessment WHERE element='TT1' AND year='$year' AND profile_name='$profile_name';");
	if(mysql_num_rows($d_a)>0){
		foreach($sids as $sid){
			$t=0;
			$startdate="0000-00-00";
			$lastyear_entry=getStatementsResultsTotals($sid,$rids,$startdate,$lastdate);
			/*TODO: update all only the first time, then update the current term only*/
			foreach($terms as $date=>$term){
				//if($dates['term'+t]<date('Y-m-d')){
					foreach($components as $crid=>$course){
						foreach($course as $component){
							if($component!='Total' and $component!='Progress'){
								$component_name="";
								$component_status=$cstatus;
								}
							else{
								$component_name=" - ".$component;
								if($profile=="app" and $component=="Progress"){
									$component_status=$cstatus;
									}
								else{
									$component_status='None';
									}
								}
							$result=0;
							$rs=array();
							$description=$term.$component_name;
							$d_a=mysql_query("SELECT id FROM assessment WHERE description='$description' AND year='$year' AND course_id='$crid' AND deadline='$date' AND component_status='$component_status';");
							$eid=mysql_result($d_a,0,'id');
							if($profile=="app"){
								$compcheck=" AND (component_id='$component' OR component_id='') ";
								if($component=='Total'){$description=$term.' - Total';}
								elseif($component=='Progress'){$description=$term.' - Progress';$compcheck=" AND component_id!='' ";}
								else{$description=$term;}
								$d_m=mysql_query("SELECT mark.id,component_id FROM (SELECT class_id FROM cidsid JOIN class ON class.id=cidsid.class_id JOIN cohort ON cohort.id=class.cohort_id WHERE student_id='$sid' AND year='$year' AND course_id='$crid' ORDER BY class_id DESC) as c JOIN midcid ON c.class_id=midcid.class_id JOIN mark ON midcid.mark_id=mark.id WHERE topic='$description' $compcheck ORDER BY entrydate, id ASC;");
								}
							else{
								$d_m=mysql_query("SELECT mark.id FROM (SELECT class_id FROM cidsid WHERE student_id='$sid' ORDER BY class_id DESC LIMIT 1) as c JOIN midcid ON c.class_id=midcid.class_id JOIN mark ON midcid.mark_id=mark.id WHERE topic='$description' AND (component_id='$component' OR component_id='') ORDER BY entrydate, id ASC LIMIT 1;");
								}
							while($mark=mysql_fetch_array($d_m,MYSQL_ASSOC)){
								$mid=$mark['id'];
								$component_id=$mark['component_id'];
								if($profile=="app"){
									if($component!='Total' and $component!='Progress'  and $component==$component_id){
										$rs=getStatementsResults($sid,$rids,$startdate,$date,$component,array("APP Framework"));
										$score=$rs[$component];
										$total[$sid][$term]['result']+=$score['result'];
										$total[$sid][$term]['values'][$component]=$score['value'];
										}
									elseif($component=='Progress'){
										foreach($total[$sid][$term]['values'] as $c=>$level){
											if($level[2]=='a'){$sl=0;}
											elseif($level[2]=='b'){$sl=1;}
											elseif($level[2]=='c'){$sl=2;}
											if($level!=""){$l[$term][$c]=($level[1]*3)-$sl;}
											}
										$score['value']=$l[$term][$component_id]-$l['Term '.(substr($term, -1)-1)][$component_id];
										$score['result']=$l[$term][$component_id]-$l['Term '.(substr($term, -1)-1)][$component_id];
										}
									elseif($component=='Total'){
										$score['value']=$total[$sid][$term]['result'];
										$score['result']=$total[$sid][$term]['result'];
										}
									$value=$score['result'];
									$result=$score['value'];
									}
								else{
									if($component!='Total' and $component!='Progress'){
										$rs=getStatementsResults($sid,$rids,$startdate,$date,$component);
										$result=$rs[$component];
										$value=$result;
										}
									elseif($component=='Progress'){
										if($t>1){
											$previoustotal_key=$t-2;
											$currenttotal_key=$t-1;
											if(isset($totals[$currenttotal_key])){$currenttotal=$totals[$currenttotal_key];}else{$currenttotal=0;}
											if(isset($totals[$previoustotal_key])){$previoustotal=$totals[$previoustotal_key];}else{$previoustotal=0;}
											$result=$currenttotal-$previoustotal;
											}
										else{
											$currenttotal=$totals[0];
											$result=$currenttotal-$lastyear_entry;
											}
										}
									elseif($component=='Total'){
										$result=getStatementsResultsTotals($sid,$rids,$startdate,$date);
										$totals[$t]=$result;
										$t++;
										}
									}
								$score=array('type'=>'value','result'=>$result,'value'=>$value,'date'=>$date);
								update_assessment_score($eid,$sid,'',$component,$score);
								update_mark_score($mid,$sid,$score);
								}
							}
						}
					//}
				}
			}
		}
	}

/**
 * Gets the terms dates, the rids for profiles, the components and the total/progress assessments.
 */
function getTermsValues($year="",$profiles=array('ELG','EYFS3648','EYFS2436')){
	$values=array();
	if($year==""){$year=get_curriculumyear();}
	if($profiles[0]=="APP Framework"){
		$d_r=mysql_query("SELECT * FROM course JOIN report ON report.course_id=course.id WHERE course.id LIKE 'KS%' AND report.title LIKE 'L%';");
		while($report=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rids[]=$report['id'];
			}
		$d_c=mysql_query("SELECT id,course_id FROM component WHERE id!='' AND course_id LIKE 'KS%' AND status IN ('V', 'O') AND year='$year';");
		while($component=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$component_id=$component['id'];
			$course_id=$component['course_id'];
			$components[$course_id][$component_id]=$component_id;
			}
		$components['KS1']['Progress']="Progress";
		$components['KS1']['Total']="Total";
		$components['KS2']['Progress']="Progress";
		$components['KS2']['Total']="Total";
		}
	elseif($profiles[0]=="ELG" or $profiles[0]=="EYFS3648" or $profiles[0]=="EYFS2436"){
		$d_r=mysql_query("SELECT id FROM report WHERE title='ELG' OR title='EYFS3648' OR title='EYFS2436';");
		while($report=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rids[]=$report['id'];
			}
		$d_c=mysql_query("SELECT c.subject_id,c.id FROM report_skill as s JOIN component as c ON s.subject_id=c.id WHERE s.subject_id!='%' AND s.subject_id!='' AND year='$year' AND course_id='FS' GROUP BY c.subject_id;");
		while($component=mysql_fetch_array($d_c,MYSQL_ASSOC)){
			$strand_id=$component['id'];
			$component_id=$component['subject_id'];
			$d_cc=mysql_query("SELECT id FROM component WHERE id='$component_id' and year='$year';");
			if(mysql_num_rows($d_cc)==0){$component_id=$strand_id;}
			$components['FS'][$component_id]=$component_id;
			}
		$components['FS']['Total']="Total";
		$components['FS']['Progress']="Progress";
		}
	$preyear=$year-1;
	$dates['firstentry']=$preyear."-09-01";
	$dates['term1']=$year."-01-10";
	$dates['term2']=$year."-04-21";
	$dates['term3']=$year."-07-31";
	$terms=array($dates['term1']=>"Term 1",$dates['term2']=>"Term 2",$dates['term3']=>"Term 3");
	$values['rids']=$rids;
	$values['components']=$components;
	$values['terms']=$terms;
	$values['dates']=$dates;
	$values['year']=$year;
	return $values;
	}

/**
 * Used to create the assessments for each term totals.
 */
function createTermStatementsAssessments($year="",$current=true,$profile=""){
	if($year==""){$year=get_curriculumyear();}
	if($current){$current_date=date("Y-m-d");}
	if($profile=="app"){$values=getTermsValues($year,array("APP Framework"));$cstatus="AV";$profile_name="APP Framework";}
	else{$values=getTermsValues($year);$cstatus="V";$profile_name="";}
	$terms=$values['terms'];
	$components=$values['components'];
	$year=$values['year'];
	/*TODO: Use subject General or Early Years and create assessement in the first days of each term*/
	foreach($terms as $date=>$term){
		//if($current and $date>=$current_date){break;}
		foreach($components as $crid=>$course){
			foreach($course as $component){
				if($component!='Total' and $component!='Progress'){
					$component_name="";
					$component_status=$cstatus;
					}
				else{
					$component_name=" - ".$component;
					if($profile=="app" and $component=="Progress"){
						$component_status=$cstatus;
						}
					else{
						$component_status='None';
						}
					}
				$description=$term.$component_name;
				$label="TT".substr($term, -1).$component_name;
				$element="TT".substr($term, -1);
				$d_a=mysql_query("SELECT id FROM assessment WHERE description='$description' AND year='$year' AND course_id='$crid' AND deadline='$date' AND component_status='$component_status';");
				if(mysql_num_rows($d_a)==0){
					mysql_query("INSERT INTO assessment (stage, year, subject_id, description, label,profile_name,
												 resultstatus, element, course_id, deadline,component_status,lock_level) 
										VALUES ('%', '$year', '%', '$description', '$label','$profile_name',
												'R', '$element','$crid', '$date', '$component_status',2);");
					$eid=mysql_insert_id();
					generate_assessment_columns($eid);
					}
				}
			}
		}
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
 * Gets the last year results for statements given and array of student ids.
 *
 * @param array $sids
 * @return array $results [index=sid]
 *
 */
function getStatementsEntryResult($sids){
	$results=array();
	$values=getTermsValues();
	$rids=$values['rids'];
	$lastdate=$dates['firstentry'];
	foreach($sids as $sid){
		$results[$sid]=getStatementsResultsTotals($sid,$rids,$startdate,$lastdate);
		}
	return $results;
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
