<?php
/**
 *			   					httpscripts/report_profile_print.php
 *
 */

require_once('../../scripts/http_head_options.php');


if(isset($_GET['id'])){$xmlid=$_GET['id'];}else{$xmlid=-1;}
if(isset($_POST['id'])){$xmlid=$_POST['id'];}
if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid='%';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$pid='%';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['stage'])){$stage=$_GET['stage'];}else{$stage='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_GET['classes'])){$classes=$_GET['classes'];}else{$classes='';}
if(isset($_POST['classes'])){$classes=$_POST['classes'];}
if(isset($_GET['eids'])){$eids=(array)$_GET['eids'];}else{$eids=array();}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}
if(isset($_GET['template'])){$template=$_GET['template'];}else{$template='';}
if(isset($_POST['template'])){$template=$_POST['template'];}
if(isset($_GET['year'])){$curryear=$_GET['year'];}else{$curryear='';}
if(isset($_POST['year'])){$curryear=$_POST['year'];}
if(isset($_GET['name'])){$profilename=$_GET['name'];}
if(isset($_POST['name'])){$profilename=$_POST['name'];}
if(isset($_GET['description'])){$description=$_GET['description'];}

//$template='tracking_summary_comparison';
$profile['transform']=$template;
//$xmlid=-1;

//trigger_error($bid.' :  '.$xmlid.' : '.$template,E_USER_WARNING);

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML['messages']=$result;
	$returnXML['Transform']=$profile['error_message'];
	$rootName='Error';
	}
else{
	/* Can either be passed a list of assessments to work with.. */
	if($xmlid==-1){
		foreach($eids as $eid){
			$AssDef=fetchAssessmentDefinition($eid);
			$AssDefs[]=$AssDef;
			}
		}
	/* Or a profile definition which means all assessments linked to
	 * the profile will be included. 
	 */
	else{
		$profile=get_assessment_profile($xmlid);
		$crid=$profile['course_id'];
		$profilename=$profile['name'];
		if($curryear==''){$curryear=get_curriculumyear($crid);}

		/*TODO*/
		$prevyear=$curryear-1;

		$allstages=list_course_stages($crid);
		foreach($allstages as $sin => $allstage){
			if($allstage['name']==$stage){$stageno=$sin-1;}
			}
		if($stageno>-1){$prevstage=$allstages[$stageno]['name'];}
		else{$prevstage='';}
		//trigger_error('PREV: '.$prevstage,E_USER_WARNING);
		/**/

		$cohort=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$curryear);
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>$prevstage,'year'=>$prevyear);
		/* Allows for alternative to the profile's default template */
		if($template!=''){$profile['transform']=$template;}

		/* Allows a subset of the profile's assessments to be included */
		if(sizeof($eids)>0){
			$AssDefs=array();
			foreach($eids as $eid){
				$AssDefs[]=fetchAssessmentDefinition($eid);
				}
			}
		else{
			$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
			}
		}

	/* TODO: make this a property of the profile 
	 * The tracking grid will only work with a single bid/pid ombination at the moment.
	 */
	if($profile['transform']=='tracking_grid'){
		//$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_summary_comparison'){
		//$pid='%';$bid='Inf';
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_jumps'){
		$bid='%';
		$pid='%';
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_barchart_difference'){
		$pid='%';
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_chart_cats'){
		$pid='%';
		$bid='%';
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		//$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		//$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_chart_pimpie'){
		if($profilename=="PIE"){$subject="Eng";}
		elseif($profilename=="PIM"){$subject="Mat";}
		$bid='';
		$pid='';
		$subject=trim($subject);
		$d_sub=mysql_query("SELECT subject_id FROM 
				component WHERE course_id='$crid' AND subject_id='$subject' AND id='';");
		if(mysql_num_rows($d_sub)==0){
			$d_com=mysql_query("SELECT subject_id FROM 
					component WHERE course_id='$crid' AND id='$subject';");
			if(mysql_num_rows($d_com)>0){
				/* If the subject is a component. */
				$bid=mysql_result($d_com,0);
				$d_com=mysql_query("SELECT subject_id FROM 
					component WHERE course_id='$crid' AND id='$bid';");
				if(mysql_num_rows($d_com)>0){
					/* Or it could be a strand. */
					$bid=mysql_result($d_com,0);
					}
				$pid=$subject;
				}
			elseif($subject=='G'){
				$bid='G';
				$pid='';
				}
			elseif($subject[0]=='#'){
				$bid=$subject;
				$pid='';
				}
			}
		else{
			$bid=$subject;
			$pid='';
			}
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>'%');
		//$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		//$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_sheet'){
		$pid='%';$bid='%';
		$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	elseif($profile['transform']=='tracking_assessment_sheet'){
		$pid='%';$bid='%';
		$AssDefs=array();
		/* A single sheet to span all courses. */
		$crids=array('GCSE','AS','A2','Foun','EngCrs');
		foreach($crids as $crid){
			$cohort=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$curryear);
			$more_AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
			$AssDefs=(array)array_merge($AssDefs,$more_AssDefs);
			}
		}
	elseif($profile['transform']=='tracking_gcse' or $profile['transform']=='tracking_us' or $profile['transform']=='tracking_ib'){
		$pid='%';
		$bid='%';
		}
	elseif($profile['transform']=='tracking_barchart_app'){
		//$bid='%';
		//$pid='Spk';
		}
	elseif(strpos($profile['transform'],'transcript')>0){
		$pid='%';
		$bid='%';
		$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
		$prev_AssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$AssDefs=(array)array_merge($AssDefs,$prev_AssDefs);
		}
	else{
		$pid='%';
		}


	/* Bands in ascending date order. */
  	$d_stats=mysql_query("SELECT DISTINCT * FROM statvalues JOIN stats ON stats.id=statvalues.stats_id 
				WHERE stats.course_id='$crid' AND stats.profile_name='$profilename'
				AND (statvalues.stage='$stage' OR statvalues.stage='$prevstage') 
				AND (statvalues.subject_id LIKE '$bid' OR statvalues.subject_id='%')
				AND (statvalues.component_id LIKE '$pid' OR statvalues.component_id='%') 
				ORDER BY statvalues.stage, statvalues.date ASC;");
	$bands=array();
	while($stat=mysql_fetch_array($d_stats,MYSQL_ASSOC)){
		$bands[$stat['date']]=$stat;
		}

	$Students=array();

	/* These are the tracking bands for target/below target/failing. */
	$asstable=array();
	foreach($AssDefs as $index=>$AssDef){
		if(sizeof($bands)>0){
			unset($bdate);
			foreach($bands as $date=>$band){
				if($AssDef['Deadline']['value']>$date or !isset($bdate)){$bdate=$date;}
				}
			/* If the assessment is out of range then set to the last band. */
			if(isset($bdate) and array_key_exists($bdate,$bands)){
				$assbands=array(array('name'=>'C','value'=>$bands[$bdate]['value3']),
								array('name'=>'B','value'=>$bands[$bdate]['value2']),
								array('name'=>'A','value'=>$bands[$bdate]['value1']));
				}
			}
		else{
			$assbands=array();
			}
		$AssDefs[$index]['assbands']=$assbands;
		}


	/* TODO: this assumes all are using same gradescheme */
	$restable=array();
	$grading_grades=$AssDefs[0]['GradingScheme']['grades'];
	$pairs=explode(';', $grading_grades);
	for($c=0;$c<sizeof($pairs);$c++){
		list($levelgrade,$level)=explode(':',$pairs[$c]);
		$restable['res'][]=array('label'=>''.$levelgrade,
								 'value'=>''.$level);
		}
	
	$Students['Student']=array();
	foreach($sids as $sid){
		$Student=(array)fetchStudent_short($sid);
		$Assessments['Assessment']=array();
		foreach($AssDefs as $AssDef){
			$Asses=(array)fetchAssessments_short($sid,$AssDef['id_db'],$bid,$pid);
			if(sizeof($Asses)==0 and $bid=='Mat'){
				$Asses=(array)fetchAssessments_short($sid,$AssDef['id_db'],'Jun',$bid);
				}
			if(sizeof($Asses)>0){
				$Assessments['Assessment']=array_merge($Assessments['Assessment'],$Asses);
				if(!array_key_exists($AssDef['id_db'],$asstable)){
					/* This will exclude assessments which have no scores. */
					$asstable['ass'][$AssDef['id_db']]=array('label'=>''.$AssDef['Description']['value'],
															 'printlabel'=>''.$AssDef['PrintLabel']['value'],
															 'date'=>''.display_date($AssDef['Deadline']['value']),
															 'year'=>''.$AssDef['Year']['value'],
															 'element'=>''.$AssDef['Element']['value'],
															 'id_db'=>''.$AssDef['id_db'],
															 'bands'=>$AssDef['assbands']);
					}
				}
			}

		$Student['Assessments']=xmlarray_indexed_check($Assessments,'Assessment');
		//$Student['Assessments']=$Assessments;
		$Students['Student'][]=$Student;
		}


	/**
	 * Totally ignore all of the above!!
	 */
	if($profile['transform']=='tracking_barchart_app'){

		$d_r=mysql_query("SELECT report.id FROM report JOIN assessment  
				ON (report.title=assessment.description AND report.course_id=assessment.course_id) 
				WHERE assessment.profile_name='$profilename' AND report.course_id='$crid';");
		$rids=array();
		while($r=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rids[]=$r['id'];
			}

		$Students['Student']=array();
		foreach($sids as $sid){
			$Student=(array)fetchStudent_short($sid);

			$Assessments=array();
			$Assessments['Assessment']=array();

			foreach($rids as $no => $rid){

				$lev=calculateProfileLevel($rid,$sid,'%',$pid);

				for($pno=1;$pno<6;$pno++){
					$points[$pno]=array('point'=>$pno,'id'=>$rid,'pid'=>$pid,'level'=>$lev['result'],'result'=>'','value'=>$lev["value$pno"]);
					if($lev["value$pno"]>80 and $points[$pno]['result']==''){$points[$pno]['result']=$lev['result'].'a';}
					elseif($lev["value$pno"]>60 and $points[$pno]['result']==''){$points[$pno]['result']=$lev['result'].'b';}
					elseif($lev["value$pno"]>30 and $points[$pno]['result']==''){$points[$pno]['result']=$lev['result'].'c';}
					}

				$Assessments['Assessment']=array_merge($Assessments['Assessment'],$points);
				}


			$Student['Assessments']=xmlarray_indexed_check($Assessments,'Assessment');
			//$Student['Assessments']=$Assessments;
			$Students['Student'][]=$Student;
			}

		}


	trigger_error($bid. ' '.$pid,E_USER_WARNING);

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']=$profile['transform'];
	$Students['Subject']['value_db']=$bid;
	$Students['Subject']['value']=get_subjectname($bid);
	$Students['Component']['value_db']=$pid;
	$Students['Component']['value']=get_subjectname($pid);
	if(isset($description)){
		$Students['Description']['value']=$description;
		}
	elseif($classes!=''){
		$Students['Description']['value']=$classes;
		}
	else{
		$Students['Description']['value']=$stage;
		}

	$Students['restable']=$restable;
	$Students['asstable']=$asstable;
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
