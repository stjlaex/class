<?php	

/**											fetch_assessment.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since		
 */


/**	
 *
 *	Retrieves all assessment information about one student using only their sid.
 *	Returns the data in an array $Assessments.
 *
 *	@param string $num
 *	@param integer $sigfigs
 *	@param string $dec
 *	@param boolean $noround
 *	@return
 */
function sigfigs($number,$sigfigs,$dec='.',$noround=false) {
    if(!$noround){
        $sigfigs++;
		}
    for($sfi=0;$sfi<=strlen($number) && $sfdone<$sigfigs;$sfi++){
        $temp=substr($number,$sfi,1);
        if ($temp!='0' && $temp!=$dec) {
            $after1stsf=true;
			}
        if((($temp!='0') && ($temp!=$dec)) || ($after1stsf && ($temp!=$dec))){
            $sfdone++;
			}
        if($temp=='.'){
            $temp=$dec;
			}
        $output.=$temp;
		}
    if(substr($output,0,1)==$dec){
        $output='0'.$output;
		}
    if(!$noround){
        $splitbydp=explode($dec,$output);
        $numdps=strlen($splitbydp[1]);
        $output=round($output, ($numdps-1));
		}
    return $output;
	}


/**
 *
 *
 *	@param float $score
 *	@param string $scoretotal
 *	@param integer $levels
 *	@return array
 */
function scoreToLevel($score,$scoretotal='',$levels){
	/*	Returns formated $percent, and floating point $cent*/
	list($out,$percent,$cent)=scoreToPercent($score,$scoretotal);
	if($cent==-100){$cent=$score;}
	$pairs=explode(';',$levels);
	for($c=0;$c<sizeof($pairs);$c++){
		list($level_grade, $level)=explode(":",$pairs[$c]);
		if($cent>=$level){$grade=$level_grade;}
		}
	if(!isset($grade)){$grade='';$cent=-100;}
	return array($grade,$cent);
	}

/**
 *
 *
 *	@param float $score
 *	@param string $scoretotal
 *	@return array
 */
function scoreToPercent($score,$scoretotal='100'){
	/*	Returns formated $percent, and floating point $cent
			and the full works in $display
	*/
	if(is_numeric($score)){
		if($scoretotal>0){
			$cent=($score/$scoretotal)*100;
//			$cent=round($cent,1);
			$percent=sprintf("% 01.1f%%",$cent);
//			$percent=sprintf("% 2d%%",$cent);
			}
		}
	if(isset($percent)){
		$display=$percent.' ('.number_format($score,0,'.','').')';
		}
	else{$display='';$percent='';$cent=-100;}
	return array($display,$percent,$cent);
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
function scoreToGrade($score,$grading_grades){
	/*
	Looks up the grade equivalent of the numerical score.
	If $score is empty then an empty $grade string is returned.	
	The numerical equivalents for the grades (levels in the grading
	scheme) must have integer values.
	*/
	if(is_numeric($score)){
		$pairs=explode(';', $grading_grades);
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
 * 	Looks up the numerical equivalent of a grade. 
 *	If the grade is an empty string then empty score is returned. 
 *
 *	@param float $score
 *	@param array $grading_grades
 *	@return string
 */
function gradeToScore($grade,$grading_grades){
    $score='';
	$pairs=explode(';', $grading_grades);
	if($grade!=''){
		for($c=0; $c<sizeof($pairs); $c++){
			list($levelgrade, $level)=explode(':',$pairs[$c]);
			if($grade==$levelgrade){$score=$level;}	
			}
		}
	return $score;
	}

/**
 *
 *	@param integer $eid
 *	@return array
 */
function fetchAssessmentDefinition($eid){
   	$AssDef=array();
  	$AssDef['id_db']=$eid;
   	$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid';");
	if(mysql_numrows($d_ass)==0){$AssDef['exists']='false';}
	else{$AssDef['exists']='true';}
	$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
	//$ass=nullCorrect($ass);

	$d_mid=mysql_query("SELECT mark_id FROM eidmid WHERE assessment_id='$eid';");
	$markcount=mysql_numrows($d_mid);
	$d_score=mysql_query("SELECT student_id FROM score
		JOIN eidmid ON eidmid.mark_id=score.mark_id WHERE eidmid.assessment_id='$eid';");
	$scorecount=mysql_numrows($d_score);
	$d_eidsid=mysql_query("SELECT student_id FROM eidsid
				   		WHERE assessment_id='$eid' AND student_id!='0'");
	$archivecount=mysql_numrows($d_eidsid);

   	$AssDef['Course']=array('label'=>'Course',
							'table_db'=>'assessment', 
							'field_db'=>'course_id',
							'type_db'=>'varchar(10)', 
							'value'=>''.$ass['course_id']);
   	$AssDef['Subject']=array('label'=>'Subject',
							 'table_db'=>'assessment', 
							 'field_db'=>'subject_id',
							 'type_db'=>'varchar(10)', 
							 'value'=>''.$ass['subject_id']);
   	$AssDef['Component']=array('label'=>'Component',
							   'table_db'=>'asssessment', 
							   'field_db'=>'component_id',
							   'type_db'=>'varchar(10)', 
							   'value'=>''.$ass['component_id']);
   	$AssDef['Stage']=array('label'=>'Stage',
						   'table_db'=>'assessment', 
						   'field_db'=>'stage',
						   'type_db'=>'char(3)', 
						   'value'=>''.$ass['stage']);
	/*
   	$AssDef['Method']=array('label'=>'Method',
							'table_db' =>'assessment', 
							'field_db'=>'assessment',
							'type_db'=>'char(3)', 
							'value'=>''.$ass['method']);
	*/
	$AssDef['Statistics']=array('label'=>'Statistics',
								'table_db'=>'assessment', 
								'field_db'=>'statistics',
								'type_db'=>'', 
								'value'=>''.$ass['statistics']);

	$gena=''.$ass['grading_name'];
	if($gena!='' and $gena!=' '){
		$d_g=mysql_query("SELECT grades FROM grading WHERE name='$gena';");
		if(mysql_num_rows($d_g)>0){$grading_grades=mysql_result($d_g,0);}
		else{$grading_grades='';}
		}
	else{$grading_grades='';}


	$AssDef['GradingScheme']=array('label'=>'Grading Scheme',
								   'table_db'=>'assessment', 
								   'field_db'=>'grading_name',
								   'type_db'=>'varchar(20)', 
								   'value'=>''.$gena, 
								   'grades'=>''.$grading_grades);
   	$AssDef['Element']=array('label'=>'Element',
							 'table_db'=>'assessment', 
							 'field_db'=>'element',
							 'type_db'=>'char(3)', 
							 'value'=>''.$ass['element']);
   	$AssDef['Description']=array('label'=>'Description', 
								 'table_db'=>'assessment', 
								 'field_db'=>'description',
								 'type_db'=>'varchar(60)', 
								 'value'=>''.$ass['description']);
   	$AssDef['PrintLabel']=array('label'=>'Print Label',
								'table_db'=>'assessment', 
								'field_db'=>'label',
								'type_db'=>'varchar(12)', 
								'value'=>''.$ass['label']);
	/*
   	$AssDef['ResultQualifier']=array('label'=>'Result Qualifier',
									 'table_db'=>'assessment', 
									 'field_db'=>'resultqualifier',
									 'type_db'=>'char(2)', 
									 'value'=>''.$ass['resultqualifier']);
	*/
   	$AssDef['ResultStatus']=array('label'=>'Result Status',
								  'table_db'=>'assessment', 
								  'field_db'=>'resultstatus',
								  'type_db'=>'enum', 
								  'value'=>''.$ass['resultstatus']);
   	$AssDef['OutOfTotal']=array('label'=>'Out of Total',
								'table_db'=>'assessment', 
								'field_db'=>'outoftotal',
								'type_db'=>'smallint', 
								'value'=>''.$ass['outoftotal']);
   	$AssDef['Derivation']=array('label'=>'Derivation',
								'table_db'=>'assessment', 
								'field_db'=>'derivation',
								'type_db'=>'varchar(60)', 
								'value'=>''.$ass['derivation']);
   	$AssDef['ComponentStatus']=array('label'=>'Component Status', 
									 'table_db'=>'assessment', 
									 'field_db'=>'component_status',
									 'type_db'=>'enum', 
									 'value'=>''.$ass['component_status']);
   	$AssDef['StrandStatus']=array('label'=>'Strand Status', 
								  'table_db'=>'assessment', 
								  'field_db'=>'strand_status',
								  'type_db'=>'enum', 
								  'value'=>''.$ass['strand_status']);
   	$AssDef['Year']=array('label'=>'Year', 
						  'table_db'=>'assessment', 
						  'field_db'=>'year',
						  'type_db'=>'year', 
						  'value'=>''.$ass['year']);
   	$AssDef['Season']=array('label'=>'Season', 
							'table_db'=>'assessment', 
							'field_db'=>'season',
							'type_db'=>'enum', 
							'value'=>''.$ass['season']);
   	$AssDef['Deadline']=array('label'=>'Deadlineforentry', 
							  'table_db'=>'assessment', 
							  'field_db'=>'deadline',
							  'type_db'=>'date', 
							  'value'=>''.$ass['deadline']);
   	$AssDef['Creation']=array('label'=>'Creation', 
							  'table_db'=>'assessment', 
							  'field_db'=>'creation',
							  'type_db'=>'date', 
							  'value'=>''.$ass['creation']);
   	$AssDef['MarkCount']=array('label'=>'Markcolumns', 
							   'table_db' =>'', 'field_db'=>'',
							   'type_db'=>'', 'value'=>''.$markcount);
   	$AssDef['ScoreCount']=array('label'=>'Markscores', 
								'table_db' =>'', 'field_db'=>'',
								'type_db'=>'', 'value'=>''.$scorecount);
   	$AssDef['ArchiveCount']=array('label'=>'Archivescores', 
								  'table_db' =>'', 'field_db'=>'',
								  'type_db'=>'', 'value'=>''.$archivecount);
	return $AssDef;
   	}


/**
 *
 * Retrieve every assessment score for one sid, either for all 
 * assessments that this student has scores for or more likely for
 * just one assessment specified by eid. 
 *
 *	@param integer $sid
 *	@param string $eid
 *	@return array
 */
function fetchAssessments($sid,$eid='%'){
	$Assessments=array();

/*
	Assessments is an xml-compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Assessment['xmltag']=array('label'=>'Display label','table_db'=>'', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type',
				'value'=>from database);

	The table from which the values are pulled are generally
	identifiable by the array in which they are stored (eg. address,
	student etc.) but table_db is avaiable if needed.


   	$Assessment['']=array('label'=>'','table_db'=>'', 'field_db'=>'',
					'type_db'=>'', 'value'=>$['']);
*/

   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid' AND assessment_id LIKE '$eid';");
	$asses=array();
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid['assessment_id'];
		if(!isset($asses[$eid])){
			$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid';");
			$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
			$ass=nullCorrect($ass);
			$asses[$eid]=$ass;
			}
		else{
			$ass=$asses[$eid];
			}

		$Assessment['id_db']=$ass['id'];
	   	$Assessment['Stage']=array('label'=>'Stage','table_db' =>
					'assessment', 'field_db'=>'stage',
					'type_db'=>'char(3)', 'value'=>$ass['stage']);
	   	$Assessment['Course']=array('label'=>'Course','table_db' =>
					'assessment', 'field_db'=>'course_id',
					'type_db'=>'varchar(10)', 'value'=>$ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('label'=>'Subject','table_db'
					=> 'assessment', 'field_db'=>'subject_id',
					'type_db'=>'varchar(10)', 'value'=>$subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('label'=>'Subject Component','table_db'
					=> 'mark', 'field_db'=>'component_id',
					'type_db'=>'varchar(10)', 'value'=>''.$component);
	   	$Assessment['Method']=array('label'=>'Method','table_db' =>
					'assessment', 'field_db'=>'method',
					'type_db'=>'char(3)', 'value'=>$ass['method']);
	   	$Assessment['GradingScheme']=array('label'=>'Grading Scheme','table_db' =>
					'assessment', 'field_db'=>'grading_name',
					'type_db'=>'varchar(20)', 'value'=>$ass['grading_name']);
	   	$Assessment['Element']=array('label' =>
					'Element','table_db'=>'assessment', 'field_db'=>'element',
					'type_db'=>'char(3)', 'value'=>$ass['element']);
	   	$Assessment['Description']=array('label' =>
					'Description','table_db'=>'assessment', 'field_db'=>'description',
					'type_db'=>'varchar(60)', 'value'=>$ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'type_db'=>'year', 'field_db'=>'year', 'value'=>$ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'Print Label','table_db'=>'assessment', 'field_db'=>'label',
					'type_db'=>'varchar(12)', 'value'=>$ass['label']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db'=>'assessment', 'field_db'=>'resultqualifier',
					'type_db'=>'char(2)', 'value'=>$ass['resultqualifier']);
	   	$Assessment['OutOfTotal']=array('label'=>'Total','table_db'
					=> 'assessment', 'field_db'=>'outoftotal',
					'type_db'=>'smallint(5)', 'value'=>$ass['outoftotal']);
	   	$Assessment['Derivation']=array('label' =>
					'Derivation','table_db'=>'assessment', 
					'field_db'=>'derivation', 'type_db'=>'varchar(60)', 
					'value'=>$ass['derivation']);
	   	$Assessment['Season']=array('label'=>'Season','table_db' =>
					'assessment', 'field_db'=>'season',
					'type_db'=>'enum', 'value'=>$ass['season']);
	   	$Assessment['Date']=array('label'=>'Date','table_db' =>
					'date', 'field_db'=>'eidmid',
					'type_db'=>'date', 'value'=>$eidsid['date']);
	   	$Assessment['Year']=array('label'=>'Year', 'table_db' =>
					'assessment', 'field_db'=>'year',
					'type_db'=>'year', 'value'=>$ass['year']);
		if($eidsid['resultstatus']!=''){
		   	$Assessment['ResultStatus']=array('label'=>'Status','table_db'
					=> 'eidsid', 'field_db'=>'resultstatus',
					'type_db'=>'enum', 'value'=>$eidsid['resultstatus']);
	   		}
		else{
		   	$Assessment['ResultStatus']=array('label'=>'Status','table_db'
					=> 'assessment', 'field_db'=>'resultstatus',
					'type_db'=>'enum', 'value'=>$ass['resultstatus']);
			}
	   	$Assessment['ExamBoard']=array('label'=>'Board','table_db'
					=> 'eidmid', 'field_db'=>'examboard',
					'type_db'=>'char(3)', 'value'=>$eidsid['examboard']);
	   	$Assessment['ExamBoardSyllabusID']=array('label' =>
					'Syllabus','table_db'=>'eidmid', 
					'field_db'=>'examsyallabus',
					'type_db'=>'char(3)', 'value'=>$eidsid['examsyllabus']);
	   	$Assessment['Result']=array('label'=>'Result','table_db'
					=> 'eidsid', 'field_db'=>'result', 
					'type_db'=>'', 'value'=>$eidsid['result']);
	   	$Assessment['Value']=array('label'=>'Result value','table_db'
					=> 'eidsid', 'field_db'=>'value', 
					'type_db'=>'', 'value'=>$eidsid['value']);
		$Assessment=nullCorrect($Assessment);
		if($eidsid['weight']=='2'){
			$Assessments[]=$Assessment;
	   		}
		$Assessments[]=$Assessment;
		}

	return $Assessments;
	}


/**
 *
 *
 *	@param integer $sid
 *	@param string $eid
 *	@param string $bid
 *	@param string $pid
 *	@return array
 */
function fetchAssessments_short($sid,$eid='%',$bid='%',$pid='%'){
	if($pid==' '){$pid='%';}
	$Assessments=array();
   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid' AND assessment_id LIKE '$eid' AND
				subject_id LIKE '$bid' AND component_id LIKE '$pid';");
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid['assessment_id'];
		$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid';");
		$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass=nullCorrect($ass);
		$Assessment['id_db']=$ass['id'];
	   	$Assessment['Course']=array('value'=>''.$ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('value'=>''.$subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('value'=>''.$component);
	   	$Assessment['Component']=array('value'=>''.get_subjectname($component));
	   	$Assessment['PrintLabel']=array('value'=>''.$ass['label']);
	   	$Assessment['Element']=array('value'=>''.$ass['element']);
	   	$Assessment['Result']=array('value'=>''.$eidsid['result']);
		$Assessment['Result']=nullCorrect($Assessment['Result']);
	   	$Assessment['Value']=array('value' =>''.$eidsid['value']);
		$Assessment=nullCorrect($Assessment);
		if($eidsid['weight']=='2'){
			$Assessments[]=$Assessment;
	   		}
		$Assessments[]=$Assessment;
		}
	return $Assessments;
	}


/**
 *
 * Special assessment definitions for the enrolment process. Will
 * check for assdefs for all cohorts associated with the yeargroup
 * community. If no association between yeargroup and cohort is needed
 * and this is just a one off then leave $com blank. The $stage is
 * either 'E' for assdefs used before being accepted or 'RE' for
 * reenrolment of current students each academic year.
 *
 * @param array $com
 * @param string $stage
 * @param string $enrolyear
 * @return array
 */
function fetch_enrolmentAssessmentDefinitions($com='',$stage='E',$enrolyear='0000'){
	$AssDefs=array();
	$crids=array();

	if($com==''){$crids[]='%';}
	else{
		list($enrolstatus,$yid)=explode(':',$com['name']);
		$yearcommunity=array('id'=>'','type'=>'year','name'=>$yid);
		$cohorts=list_community_cohorts($yearcommunity);
		while(list($index,$cohort)=each($cohorts)){
			$crids[]=$cohort['course_id'];
			}
		}

	while(list($index,$crid)=each($crids)){
		//$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$enrolyear);
		$d_assessment=mysql_query("SELECT id FROM assessment
			   WHERE (course_id LIKE '$crid' OR course_id='%') AND 
				stage='$stage' AND year='$enrolyear' AND profile_name='' AND resultstatus!='S' 
				ORDER BY course_id;");
		while($ass=mysql_fetch_array($d_assessment, MYSQL_ASSOC)){
			$AssDefs[]=fetchAssessmentDefinition($ass['id']);
			}
		}

	//TODO: allow enrolment assessments linked to the cohort they are joining?
	//$AssDefs=fetch_cohortAssessmentDefinitions($cohort);
	//trigger_error('chort:'.sizeof($AssDefs).' '.$crid,E_USER_WARNING);

	return $AssDefs;
	}

/**
 * This needs the appropriate eid obtained from above for RE. Not generally of use
 * outside the enrolments matrix. Returns the number of students with
 * their eid set to one of the two possible result states.
 *
 * @param integer $comid
 * @param string $reenrol_eid
 * @param string $result1
 * @param string $result2
 * @return integer
 */
function count_reenrol_no($comid,$reenrol_eid,$result1,$result2=''){
	$todate=date('Y-m-d');
	if($result2!=''){
		$resultstring='(result=\''.$result1.'\' OR result=\''.$result2.'\')';
		}
	else{
		$resultstring='result=\''.$result1.'\'';
		}
	$d_noc=mysql_query("SELECT COUNT(eidsid.student_id) FROM
						eidsid JOIN comidsid ON
					eidsid.student_id=comidsid.student_id WHERE comidsid.community_id='$comid'
					AND (comidsid.leavingdate>'$todate' OR 
					comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
					AND (comidsid.joiningdate<='$todate' OR 
					comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL) 
					AND assessment_id='$reenrol_eid' AND $resultstring;");

	if(mysql_num_rows($d_noc)>0){
		$no=mysql_result($d_noc,0);
		}
	else{
		$no=0;
		}

	return $no;
	}

/**
 * This needs the appropriate eid obtained from above for RE. Not generally of use
 * outside the enrolments matrix. Returns the number of students with
 * their eid set to one of the two possible result states.
 *
 * @param integer $comid
 * @param string $reenrol_eid
 * @param string $result1
 * @param string $result2
 * @return integer
 */
function list_reenrol_sids($comid,$reenrol_eid,$result1,$result2=''){
	$todate=date('Y-m-d');
	if($result2!=''){
		$resultstring='(result=\''.$result1.'\' OR result=\''.$result2.'\')';
		}
	else{
		$resultstring='result=\''.$result1.'\'';
		}
	$d_noc=mysql_query("SELECT eidsid.student_id AS id FROM
						eidsid JOIN comidsid ON
					eidsid.student_id=comidsid.student_id WHERE comidsid.community_id='$comid'
					AND (comidsid.leavingdate>'$todate' OR 
					comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL) 
					AND (comidsid.joiningdate<='$todate' OR 
					comidsid.joiningdate='0000-00-00' OR comidsid.joiningdate IS NULL) 
					AND assessment_id='$reenrol_eid' AND $resultstring;");
	$sids=array();
	if(mysql_num_rows($d_noc)>0){
		while($student=mysql_fetch_array($d_noc,MYSQL_ASSOC)){
			$sids[]=$student['id'];
			}
		}

	return $sids;
	}


/**
 * Returns all assdefs of relevance to a cohort. It will not fetch 
 * assessment defs which refer to statistics (resultstatus=S)
 * or which are used for enrolments (stage=RE, stage=E). 
 *
 * TODO: season is currently fixed to S! 
 *
 * @param array $cohort
 * @param string $profid
 * @return array
 */
function fetch_cohortAssessmentDefinitions($cohort,$profid=''){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	$year=$cohort['year'];
	$season='S';
	$AssDefs=array();
	if($profid!='' and $profid!='%'){
		$profile=get_assessment_profile($profid);
		$profile_name=$profile['name'];
		}
	else{
		$profile_name=$profid;
		}

	$d_assessment=mysql_query("SELECT id FROM assessment
			   WHERE (course_id LIKE '$crid' OR course_id='%') AND 
				(stage LIKE '$stage' OR stage='%') AND 
				year LIKE '$year' AND profile_name LIKE '$profile_name' AND
				resultstatus!='S' AND  stage!='RE' AND stage!='E' 
				ORDER BY year DESC, deadline DESC, element ASC;");
   	while($ass=mysql_fetch_array($d_assessment, MYSQL_ASSOC)){
		$AssDefs[]=fetchAssessmentDefinition($ass['id']);
		}
	return $AssDefs;
	}


/**
 *
 */
function update_derivation($eid,$der){
	$AssDef=fetchAssessmentDefinition($eid);
	$older=$AssDef['Derivation']['value'];
	$crid=$AssDef['Course']['value'];
	$assyear=$AssDef['Year']['value'];
	$assstage=$AssDef['Stage']['value'];
	if($older!=$der){
		/*identify the assessments with elements and store in derivation*/
		list($operation,$elements)=parse_derivation($der);
		/*must specify type=A for all assessments*/
		mysql_query("DELETE FROM derivation WHERE
							resultid='$eid' AND type='A'");
		while(list($index,$element)=each($elements)){
			$d_ass=mysql_query("SELECT id FROM assessment WHERE
						course_id='$crid' AND element='$element' AND year='$assyear'");
			while($ass=mysql_fetch_array($d_ass,MYSQL_ASSOC)){
				$elementeid=$ass['id'];
				mysql_query("INSERT INTO derivation (resultid,
					operandid, type, element) VALUES ('$eid','$elementeid','A','$element')");
				}
			}

		mysql_query("UPDATE assessment SET derivation='$der' WHERE id='$eid'");
		$AssDef['Derivation']['value']=$der;

		$steps=(array)derive_accumulator_steps($der,$eid);
		$cohorts=(array)list_course_cohorts($crid,$assyear);
		$students=array();

		if($steps[0]['operation']!='RANK'){
			/*ranking is done different and only on request*/
			if($older==' ' or $older==''){
				/*this is the first time computing a derivation for this*/
				while(list($index,$cohort)=each($cohorts)){
					if($assstage=='%' or $cohort['stage']==$assstage){
						$cohortstudents=(array)listin_cohort($cohort);
						/* compile all students who relate to this assessment*/
						$students=array_merge($students,$cohortstudents);
						}
					}
				}
			else{
				/*easier beacuase the eidsid values already exist*/
				$d_eidsid=mysql_query("SELECT DISTINCT student_id AS id FROM eidsid WHERE
						assessment_id LIKE '$eid'");
				while($student=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
					$students[]=$student;
					}
				}

			while(list($index,$student)=each($students)){
				$result=derive_student_score($student['id'],$AssDef,$steps);
				//trigger_error('Student '.$student['id'].' score '.$result ,E_USER_WARNING);
				}
			}
		}
	}

/**
 *
 */
function compute_assessment_ranking($AssDef,$steps,$cohorts){
	$todate=date('Y-m-d');
	$eid=$AssDef['id_db'];
	$crid=$AssDef['Course']['value'];
	while(list($index,$cohort)=each($cohorts)){
		$cohid=$cohort['id'];
		mysql_query("CREATE TEMPORARY TABLE
			   			cohortstudent$cohid (SELECT DISTINCT student_id FROM comidsid 
			   			JOIN cohidcomid ON comidsid.community_id=cohidcomid.community_id
			   			WHERE cohidcomid.cohort_id='$cohid' AND
			   			(comidsid.joiningdate<='$todate' OR comidsid.joiningdate IS NULL)
			   			AND (comidsid.leavingdate>'$todate' OR 
			   			comidsid.leavingdate='0000-00-00' OR comidsid.leavingdate IS NULL))");
		}

	//only working for a unique element to assessment relation!!!
	$operandid=$steps[0]['operandids'][0];
	$subjects=list_course_subjects($crid);
	foreach($subjects as $subject){
		$rankbid=$subject['id'];
		$d_comp=mysql_query("SELECT component.id AS id FROM
					subject JOIN component ON component.id=subject.id WHERE 
					component.subject_id='$rankbid' AND  component.course_id='$crid'");
		$rankpids=array();
		while($component=mysql_fetch_array($d_comp,MYSQL_ASSOC)){
			$rankpids[]=$component['id'];
			}
		$rankpids[]='';
		while(list($index,$rankpid)=each($rankpids)){
			reset($cohorts);
			while(list($index,$cohort)=each($cohorts)){
				$cohid=$cohort['id'];
				/* this will rank within a stage*/
				/*$d_r=mysql_query("SELECT b.student_id, b.value FROM cohortstudent$cohid a,
							eidsid b WHERE b.student_id=a.student_id AND
							b.assessment_id='$operandid' AND b.subject_id='$rankbid' AND
							b.component_id='$rankpid' ORDER BY b.value DESC");
				*/

				/*this will rank within a teaching class?*/
				$stage=$cohort['stage'];
				$d_c=mysql_query("SELECT DISTINCT id FROM class
					WHERE course_id='$crid' AND subject_id='$rankbid' AND stage='$stage'");
				while($class=mysql_fetch_array($d_c,MYSQL_ASSOC)){
					$cid=$class['id'];
					$cids[]=$cid;
					mysql_query("CREATE TEMPORARY TABLE classstudent 
						(SELECT DISTINCT student_id FROM
			   			cidsid WHERE class_id='$cid')");
					$d_r=mysql_query("SELECT b.student_id, b.value FROM classstudent a,
							eidsid b WHERE b.student_id=a.student_id AND
							b.assessment_id='$operandid' AND b.subject_id='$rankbid' AND
							b.component_id='$rankpid' ORDER BY b.value DESC");
					mysql_query("DROP TABLE classstudent");

					$index=0;
					$preval=-10000;
					while($r=mysql_fetch_array($d_r,MYSQL_ASSOC)){
						$index++;
						$ranksid=$r['student_id'];
						if($preval!=$r['value']){$rankindex=$index;}/*ties get the same rank*/
						$score=array('result'=>$rankindex,'value'=>$rankindex);
						update_assessment_score($eid,$ranksid,$rankbid,$rankpid,$score);
						$preval=$r['value'];
						}
					}
				}
			}
		}
	/* tidy up */
	reset($cohorts);
	while(list($index,$cohort)=each($cohorts)){
		$cohid=$cohort['id'];
		mysql_query("DROP TABLE cohortstudent$cohid");
		}
	}

/** 
 * each step has an operator and an array of operandids (eids pointed 
 * to by element) which may hold a value for that operand 
 *
 * @param string $der
 * @param integer $resultid
 * @return array
 */
function derive_accumulator_steps($der,$resultid){
	list($operation,$elements)=parse_derivation($der);
	$steps=array();
	while(list($index,$element)=each($elements)){
		$step=array();
		if($operation=='SUM' or $operation=='AVE'){
			$step['op']='+';
			}
		elseif($operation=='DIF' and $index>0){
			$step['op']='-';
			}
		elseif($operation=='DIF' and $index==0){
			$step['op']='+';
			}
		elseif($operation=='RANK'){
			/*if a rank, then $steps is not going to be used by the accumulators*/
			/*and this may not be neccessary?*/
			$step['op']='R';
			}
		$step['element']=$element;//may not needed but...
		$step['operation']=$operation;

		/* list all possible eids associated which could hold this*/
		/*						operands value*/
		$d_op=mysql_query("SELECT operandid FROM derivation WHERE
			resultid='$resultid' AND type='A' AND element='$element'");
		$operandids=array();
		while($op=mysql_fetch_array($d_op,MYSQL_ASSOC)){
			$operandids[]=$op['operandid'];
			}
		$step['operandids']=$operandids;
		$steps[]=$step;
		//trigger_error('Steps '.sizeof($steps).' argument '.$steps[0]['op'],E_USER_WARNING);
		}
	return $steps;
	}


/**
 * takes the string $der which is the derivation field of an 
 * assessment and returns the operation (the characters before the 
 * first open bracket) and the elements as an array which are the colon seperated 
 * contents of the brackets
 *
 * @param string $der
 * @return array
 */
function parse_derivation($der){
	$open=strpos($der,'(');
	$operation=substr($der,0,$open);
	$argument=substr($der,$open+1);
	$argument=trim($argument,' )');
	$elements=(array)explode(':',$argument);
	//trigger_error('Function '.$operation.' argument '.$elements[0],E_USER_WARNING);
	return array($operation,$elements);
	}

/**
 *
 */
function derive_student_score($sid,$AssDef,$steps=''){
	/*both resultid and operandid are eids, simply being careful in naming */
								/*to avoid confusion! */
	$resultid=$AssDef['id_db'];
	$grading_grades=$AssDef['GradingScheme']['grades'];
	if($steps==''){
		$der=$AssDef['Derivation']['value'];
		$steps=(array)derive_accumulator_steps($der,$resultid);
		}

	$accumulators=compute_accumulators($sid,$AssDef,$steps);
	reset($accumulators);

	if(sizeof($steps)==1){
		/*only a rank has a single step*/
		/*empty the accumulators so nothing else is done*/
		$accumulators=array();
		}
	while(list($bid,$componentaccs)=each($accumulators)){
		reset($componentaccs);
		while(list($pid,$acc)=each($componentaccs)){
			if($pid==' '){$pid='';}
			if($grading_grades!=''){
				$value=$acc['value']/$acc['count'];
				$value=round($value,2);
				$res=scoreToGrade($value,$grading_grades);
				}
			else{
				$res=round($acc['value']);
				}
			//if($bid=='Art'){trigger_error('Subject '.$bid.' '.$pid.' accvalue '.$value,E_USER_WARNING);}
			$score=array('result'=>$res,'value'=>$value);
			update_assessment_score($resultid,$sid,$bid,$pid,$score);
			}
		}
	}

/** 
 * Used to iterate over the $steps for a derivation for one $sid, 
 * every bid-pid combination for this $AssDef is covered and the 
 * results return in $accumulators, passing an already active 
 * $accumulator allows for iterating across many $sids and is the 
 * method used for overall statistics (ie. averages) for an assessment.
 * Should only be called for derivations of type=M,A or S but not R
 *
 * @param integer $sid
 * @param array $AssDef
 * @param array $steps
 * @param array $accumulators
 * 
 */
function compute_accumulators($sid,$AssDef,$steps,$accumulators=''){
	if($accumulators==''){$accumulators=array();}
	/*the general accumulator is reset for each fresh call*/
	$accumulators['G']['value']='';
	$accumulators['G']['count']='';

	while(list($index,$step)=each($steps)){
		$op=$step['op'];
		$operandid=$step['operandids'][0];//temporarily only does one!!!
		//trigger_error('Step '.$index.' '.$op.' : '.$val.' '.$operandid,E_USER_WARNING);
		if($operandid!=''){$Assessments=(array)fetchAssessments_short($sid,$operandid);}
		while(list($assno,$Assessment)=each($Assessments)){
			$bid=$Assessment['Subject']['value'];
			$pid=$Assessment['SubjectComponent']['value'];
			if($pid==''){$pid=' ';$rankpid='';}/*cause of nullCorrect*/
			else{$rankpid=$pid;}
			if(!isset($accumulators[$bid][$pid]['value'])){
				$accumulators[$bid][$pid]['value']=0;
				$accumulators[$bid][$pid]['count']=0;
				}
			/*This is the subject-component average*/
			$opline=$accumulators[$bid][$pid]['value']. $op. $Assessment['Value']['value'];
			eval("\$opline = $opline;");
			$accumulators[$bid][$pid]['value']=$opline;
			$accumulators[$bid][$pid]['count']++;

			/* This is the 'General' cross-curricular average*/
			/* it is not preserved across sids, so can't be used for*/
			/* an overall overall average*/
			$opline=$accumulators['G']['value']. $op. $Assessment['Value']['value'];
			eval("\$opline = $opline;");
			$accumulators['G']['value']=$opline;
			$accumulators['G']['count']++;
			}
		}
	return $accumulators;
	}

/**
 * Should always be used when writing to the eidisd table. The $score 
 * being recorded is an array with both result and value set, with 
 * optionally a date. 
 */
function update_assessment_score($eid,$sid,$bid,$pid,$score){
	$res=$score['result'];
	$val=$score['value'];
	if(isset($score['date'])){$date=$score['date'];}else{$date='';}

	/* Check if this is really an update of eidsid and if not insert a
		new record. If the result is blank then simply delete the old
		record. */
	$d_eidsid=mysql_query("SELECT id, result FROM eidsid
				WHERE subject_id='$bid' AND component_id='$pid' 
				AND assessment_id='$eid' AND student_id='$sid';");
	if(mysql_num_rows($d_eidsid)==0 and $res!=''){
		mysql_query("INSERT INTO eidsid (assessment_id,
					student_id, subject_id, component_id, result, value, date) 
					VALUES ('$eid','$sid','$bid','$pid','$res','$val','$date');");
		}
	else{
		$oldscore=mysql_fetch_array($d_eidsid,MYSQL_ASSOC);
		$id=$oldscore['id'];
		if($res==''){
			mysql_query("DELETE FROM eidsid WHERE id='$id' LIMIT 1;");
			}
		elseif($oldscore['result']!=$res){
			mysql_query("UPDATE eidsid SET result='$res',
				 value='$val', date='$date' WHERE id='$id';");
			}
		}

	/* Now check to see if this score is an operand in any derivations*/
	/* not needed if sid=0 (meaning just statistics being updated).*/
	if($sid>0){
		$d_der=mysql_query("SELECT resultid FROM derivation
				WHERE type='A' AND operandid='$eid'");
		while($der=mysql_fetch_array($d_der,MYSQL_ASSOC)){
			$resultid=$der['resultid'];
			$AssDef=fetchAssessmentDefinition($resultid);
			$result=derive_student_score($sid,$AssDef);
			//trigger_error('Updated assessment score for '.$sid.'-'.$resultid ,E_USER_WARNING);
			}
		}
	}

/**
 * Should always be used when writing to the score table. The $score
 * being recorded is an array with both result and value.
 */
function update_mark_score($mid,$sid,$score){
	if($mid!=-1 and $mid!=''){
		$res=$score['result'];
		$val=$score['value'];
		if($val==''){
			mysql_query("DELETE FROM score WHERE
						mark_id='$mid' AND student_id='$sid' LIMIT 1");
			}
		elseif(isset($score['type'])){
			$field=$score['type'];/*either grade or value*/
			if(mysql_query("INSERT INTO score ($field,
					 mark_id, student_id) VALUES
					('$val',  '$mid', '$sid')")){}
			else{mysql_query("UPDATE score SET
					$field='$val' WHERE mark_id='$mid' AND student_id='$sid'");}
			}
		}
	}

/**
 * This tries to find the mid (if one exists otherwise -1) associated 
 * with an assessment for a distinct $crid/$bid/$pid combination. And 
 * its not easy and only hopefully unique! WARNING!
 *
 * @param integer $eid
 * @param string $crid
 * @param string $bid
 * @param string $pid
 * @return array
 */
function get_assessment_mid($eid,$crid,$bid,$pid=''){
	if(mysql_query("CREATE TEMPORARY TABLE assmids (SELECT DISTINCT mark_id FROM eidmid 
				JOIN mark ON mark.id=eidmid.mark_id WHERE mark.assessment='yes' AND
				mark.component_id='$pid' AND eidmid.assessment_id='$eid');")){}
	else{print 'Failed!<br />'; $error=mysql_error(); print $error.'<br />';}
	if(mysql_query("CREATE TEMPORARY TABLE classmids (SELECT
				mark_id FROM midcid JOIN class
				ON class.id=midcid.class_id WHERE class.subject_id='$bid' 
				AND class.course_id='$crid');")){}
	else{print 'Failed!<br />'; $error=mysql_error(); print $error.'<br />';}
	$d_marks=mysql_query("SELECT DISTINCT assmids.mark_id FROM assmids JOIN
						classmids ON classmids.mark_id=assmids.mark_id;");
	if(mysql_num_rows($d_marks)>0){$mid=mysql_result($d_marks,0);}
	else{$mid=-1;}
	mysql_query("DROP TABLE assmids;");
	mysql_query("DROP TABLE classmids;");
	return $mid;
	}

/**
 *
 * @param integer $hwid
 * @returjn array
 */
function fetchHomeworkDefinition($hwid){
   	$Def=array();
  	$Def['id_db']=$hwid;
   	$d_hw=mysql_query("SELECT * FROM homework WHERE id='$hwid';");
	if(mysql_numrows($d_hw)==0){$Def['exists']='false';}
	else{$Def['exists']='true';}
	$hw=mysql_fetch_array($d_hw,MYSQL_ASSOC);
	//$hw=nullCorrect($hw);
   	$Def['Course']=array('label'=>'course',
						 // 'table_db'=>'homework', 
						 'field_db'=>'course_id',
						 'type_db'=>'varchar(10)', 
						 'value'=>''.$hw['course_id']);
   	$Def['Subject']=array('label'=>'subject',
						  // 'table_db'=>'homework', 
						  'field_db'=>'subject_id',
						  'type_db'=>'varchar(10)', 
						  'value'=>''.$hw['subject_id']);
   	$Def['Component']=array('label'=>'component',
							// 'table_db'=>'asssessment', 
							'field_db'=>'component_id',
							'type_db'=>'varchar(10)', 
							'value'=>''.$hw['component_id']);
   	$Def['Stage']=array('label'=>'stage',
						// 'table_db'=>'homework', 
						'field_db'=>'stage',
						'type_db'=>'char(3)', 
						'value'=>''.$hw['stage']);
   	$Def['Title']=array('label'=>'title',
						'table_db'=>'homework', 
						'field_db'=>'title',
						'inputtype'=> 'required',
						'type_db'=>'varchar(120)', 
						'value'=>''.$hw['title']);
   	$Def['Description']=array('label'=>'description',
							  'table_db'=>'homework', 
							  'field_db'=>'description',
							  'type_db'=>'text', 
							  'inputtype'=> 'required',
							  'value'=>''.$hw['description']);
   	$Def['References']=array('label'=>'references', 
							 'table_db'=>'homework', 
							 'field_db'=>'refs',
							 'type_db'=>'text', 
							 'value'=>''.$hw['refs']);
	/*TODO: Put this in catdef table somewhere.*/
	if($hw['def_name']==''){$hw['def_name']='HW Quality';}
   	$Def['Markdef']=array('label'=>'marktype', 
						  // 'table'=>'homework', 
						  'field_db'=>'def_name',
						  'type_db'=>'varchar(20)', 
						  'value'=>''.$hw['def_name']);
   	$Def['Author']=array('label'=>'author',
						 // 'table_db'=>'homework', 
						 'field_db'=>'author',
						 'type_db'=>'varchar(14)', 
						 'value'=>''.$hw['author']);
	return $Def;
   	}

/**
 * Returns a profile definition given a profile's id.
 *
 * @param integer $profid
 * @return array
 */
function get_assessment_profile($profid){
	if($profid!=''){
		$d_pro=mysql_query("SELECT id, name, subtype AS component_status, rating_name, course_id,
				   	subject_id, comment AS transform FROM categorydef WHERE type='pro' AND id='$profid';");
		$profile=mysql_fetch_array($d_pro,MYSQL_ASSOC);
		}
	else{
		$profile=array('id'=>-1,'name'=>'');
		}
	return $profile; 
	}

/**
 * Returns an array of all assessment profiles for a single course.
 *
 * Only allows for one profile in use per crid/bid combination
 * at once.  Using the subtype of categorydef to hold the
 * component status with values of None, N or V same as for
 * assessments.  Use the rating_name to indicate the type of
 * summary coumn (average,sum or tally).  Grades are going to
 * have to be averaged.
 *
 *
 * @param string $crid
 * @param string $bid
 * @return array
 */
function list_assessment_profiles($crid,$bid='%'){
	$profiles=array();
	if($crid!=''){
		$d_pro=mysql_query("SELECT id, name, subtype AS component_status, rating_name, course_id,
						subject_id, comment AS transform FROM categorydef WHERE type='pro'
						AND (subject_id LIKE '$bid' OR subject_id='%') AND course_id='$crid' ORDER
						BY subject_id, name;");
		while($profile=mysql_fetch_array($d_pro,MYSQL_ASSOC)){
			$profiles[]=$profile;
			}
		}
	return $profiles;
	}
/*		$d_profile=mysql_query("SELECT name, subtype, rating_name FROM
				categorydef WHERE type='pro' AND
				course_id='$profile_crid' AND (subject_id='$profile_bid' OR subject_id='%');");
		$profile=mysql_fetch_array($d_profile,MYSQL_ASSOC);
*/
?>
