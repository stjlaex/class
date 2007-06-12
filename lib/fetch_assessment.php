<?php	
/**											fetch_assessment.php
 *
 *	Retrieves all assessment information about one student using only their sid.
 *	Returns the data in an array $Assessments.
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


function scoreToLevel($score,$scoretotal='',$levels){
	/*	Returns formated $percent, and floating point $cent*/
	list($out,$percent,$cent)=scoreToPercent($score,$scoretotal);
	if($cent==-100){$cent=$score;}
	$pairs=explode(";",$levels);
	for($c=0;$c<sizeof($pairs);$c++){
		list($level_grade, $level)=split(":",$pairs[$c]);
		if($cent>=$level){$grade=$level_grade;}
		}
	if(!isset($grade)){$grade='';$cent=-100;}
	return array($grade,$cent);
	}

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
			list($levelgrade, $level)=split(':',$pairs[$c]);
			if($score>=$level){
				$lowgrade=$levelgrade;
				$lowlevel=$level;
				$high=$c+1;
				}
			}
		$grade=$lowgrade;
		if($high<$c){
			list($highgrade, $highlevel)=split(':',$pairs[$high]);
   			if(($highlevel-$score)<=($score-$lowlevel)){$grade=$highgrade;}
			}
		}
	else{$grade='';}
	return $grade;
	}


function gradeToScore($grade,$grading_grades){
	/*
	Looks up the numerical equivalent of a grade. 
	If the grade is an empty string then empty score is returned. 
	*/
    $score='';
	$pairs=explode (';', $grading_grades);
	if($grade!=''){
		for($c=0; $c<sizeof($pairs); $c++){
			list($levelgrade, $level)=split(':',$pairs[$c]);
			if($grade==$levelgrade){$score=$level;}	
			}
		}
	return $score;
	}

function fetchAssessmentDefinition($eid){
   	$AssDef=array();
  	$AssDef['id_db']=$eid;
   	$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid' ORDER
							BY creation");
	if(mysql_numrows($d_ass)==0){$AssDef['exists']='false';}
	else{$AssDef['exists']='true';}
	$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
	$ass=nullCorrect($ass);

	$d_mid=mysql_query("SELECT mark_id FROM eidmid WHERE assessment_id='$eid'");
	$markcount=mysql_numrows($d_mid);
	$d_score=mysql_query("SELECT student_id FROM score
		JOIN eidmid ON eidmid.mark_id=score.mark_id WHERE eidmid.assessment_id='$eid'");
	$scorecount=mysql_numrows($d_score);
	$d_eidsid=mysql_query("SELECT student_id FROM eidsid
									WHERE assessment_id='$eid'");
	$archivecount=mysql_numrows($d_eidsid);

   	$AssDef['Subject']=array('label' => 'Subject','table_db' =>
					'assessment', 'field_db' => 'subject_id',
					'type_db'=>'varchar(10)', 'value' => $ass['subject_id']);
   	$AssDef['Component']=array('label' => 'Component','table_db' =>
					'asssessment', 'field_db' => 'component_id',
					'type_db'=>'varchar(10)', 'value' => $ass['component_id']);
   	$AssDef['Stage']=array('label' => 'Stage','table_db' => 'assessment', 'field_db' => 'stage',
					'type_db'=>'char(3)', 'value' => $ass['stage']);
   	$AssDef['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'assessment',
					'type_db'=>'char(3)', 'value' => $ass['method']);

	$gena=$ass['grading_name'];
	if($gena!='' and $gena!=' '){
		$d_grading=mysql_query("SELECT grades FROM grading WHERE name='$gena'");
		$grading_grades=mysql_result($d_grading,0);
		}
	else{$grading_grades='';}
	$AssDef['GradingScheme']=array('label' => 'Grading Scheme','table_db' =>
								   'assessment', 'field_db' => 'grading_name',
								   'type_db'=>'varchar(20)', 
								   'value'=>$gena, 'grades' =>$grading_grades);
   	$AssDef['Element']=array('label' => 'Element','table_db' =>
					'assessment', 'field_db' => 'element',
					'type_db'=>'char(3)', 'value' => $ass['element']);
   	$AssDef['Description']=array('label' => 'Description','table_db'
					=> 'assessment', 'field_db' => 'description',
					'type_db'=>'varchar(60)', 'value' => $ass['description']);
   	$AssDef['PrintLabel']=array('label' => 'Print Label','table_db' =>
					'assessment', 'field_db' => 'label',
					'type_db'=>'varchar(12)', 'value' => $ass['label']);
   	$AssDef['ResultQualifier']=array('label' => 
					'Result Qualifier','table_db' => 'assessment', 
					'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
   	$AssDef['ResultStatus']=array('label' => 'Result Status','table_db' => 'assessment', 
					'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $ass['resultstatus']);
   	$AssDef['OutOfTotal']=array('label' => 'Out of Total','table_db'
					=> 'assessment', 'field_db' => 'outoftotal',
					'type_db'=>'smallint', 'value' => $ass['outoftotal']);
   	$AssDef['Derivation']=array('label' => 'Derivation','table_db' => 
					'assessment', 'field_db' => 'derivation',
					'type_db'=>'varchar(60)', 'value' => $ass['derivation']);
   	$AssDef['Course']=array('label' => 'Course','table_db' =>
					'assessment', 'field_db' => 'course_id',
					'type_db'=>'varchar(10)', 'value' => $ass['course_id']);
   	$AssDef['ComponentStatus']=array('label' => 'Component Status', 
					'table_db' => 'assessment', 'field_db' => 'component_status',
					'type_db'=>'enum', 'value' => $ass['component_status']);
   	$AssDef['Year']=array('label' => 'Year', 'table_db' => 'assessment', 'field_db' => 'year',
					'type_db'=>'year', 'value' => $ass['year']);
   	$AssDef['Season']=array('label' => 'Season', 'table_db' =>
					'assessment', 'field_db' => 'season',
					'type_db'=>'enum', 'value' => $ass['season']);
   	$AssDef['Deadline']=array('label' => 'Deadlineforentry', 'table_db' =>
					'assessment', 'field_db' => 'deadline',
					'type_db'=>'date', 'value' => $ass['deadline']);
   	$AssDef['Creation']=array('label' => 'Creation', 'table_db' =>
					'assessment', 'field_db' => 'creation',
					'type_db'=>'date', 'value' => $ass['creation']);
   	$AssDef['MarkCount']=array('label' => 'Markcolumns', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $markcount);
   	$AssDef['ScoreCount']=array('label' => 'Markscores', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $scorecount);
   	$AssDef['ArchiveCount']=array('label' => 'Archivescores', 'table_db' =>
					'', 'field_db' => '',
					'type_db'=>'', 'value' => $archivecount);
	return $AssDef;
   	}

function fetchAssessments($sid,$eid='%'){
	$Assessments=array();

/*
	Assessments is an xml compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Assessment['xmltag']=array('label' => 'Display label','table_db' => '', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type',
				'value' => from database);

	The table from which the values are pulled are generally
	identifiable by the array in which they are stored (eg. address,
	student etc.) but table_db is avaiable if needed.


   	$Assessment['']=array('label' => '','table_db' => '', 'field_db' => '',
					'type_db'=>'', 'value' => $['']);
*/

   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid' AND assessment_id LIKE '$eid'");
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid['assessment_id'];
		$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid'");
		$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass=nullCorrect($ass);

		$Assessment['id_db']=$ass['id'];
	   	$Assessment['Stage']=array('label' => 'Stage','table_db' =>
					'assessment', 'field_db' => 'stage',
					'type_db'=>'char(3)', 'value' => $ass['stage']);
	   	$Assessment['Course']=array('label' => 'Course','table_db' =>
					'assessment', 'field_db' => 'course_id',
					'type_db'=>'varchar(10)', 'value' => $ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('label' => 'Subject','table_db'
					=> 'assessment', 'field_db' => 'subject_id',
					'type_db'=>'varchar(10)', 'value' => $subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('label' => 'Subject Component','table_db'
					=> 'mark', 'field_db' => 'component_id',
					'type_db'=>'varchar(10)', 'value' => $component);
	   	$Assessment['Method']=array('label' => 'Method','table_db' =>
					'assessment', 'field_db' => 'method',
					'type_db'=>'char(3)', 'value' => $ass['method']);
	   	$Assessment['GradingScheme']=array('label' => 'Grading Scheme','table_db' =>
					'assessment', 'field_db' => 'grading_name',
					'type_db'=>'varchar(20)', 'value' => $ass['grading_name']);
	   	$Assessment['Element']=array('label' =>
					'Element','table_db' => 'assessment', 'field_db' => 'element',
					'type_db'=>'char(3)', 'value' => $ass['element']);
	   	$Assessment['Description']=array('label' =>
					'Description','table_db' => 'assessment', 'field_db' => 'description',
					'type_db'=>'varchar(60)', 'value' => $ass['description']);
	   	$Assessment['Year']=array('label' =>
					'Year', 'type_db' => 'year', 'field_db' => 'year', 'value' => $ass['year']);
	   	$Assessment['PrintLabel']=array('label' =>
					'Print Label','table_db' => 'assessment', 'field_db' => 'label',
					'type_db'=>'varchar(12)', 'value' => $ass['label']);
	   	$Assessment['ResultQualifier']=array('label' =>
					'Qualifier','table_db' => 'assessment', 'field_db' => 'resultqualifier',
					'type_db'=>'char(2)', 'value' => $ass['resultqualifier']);
	   	$Assessment['OutOfTotal']=array('label' => 'Total','table_db'
					=> 'assessment', 'field_db' => 'outoftotal',
					'type_db'=>'smallint(5)', 'value' => $ass['outoftotal']);
	   	$Assessment['Derivation']=array('label' =>
					'Derivation','table_db' => 'assessment', 
					'field_db' => 'derivation', 'type_db'=>'varchar(60)', 
					'value' => $ass['derivation']);
	   	$Assessment['Season']=array('label' => 'Season','table_db' =>
					'assessment', 'field_db' => 'season',
					'type_db'=>'enum', 'value' => $ass['season']);
	   	$Assessment['Date']=array('label' => 'Date','table_db' =>
					'date', 'field_db' => 'eidmid',
					'type_db'=>'date', 'value' => $eidsid['date']);
	   	$Assessment['Year']=array('label' => 'Year', 'table_db' =>
					'assessment', 'field_db' => 'year',
					'type_db'=>'year', 'value' => $ass['year']);
		if($eidsid['resultstatus']!=''){
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'eidsid', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $eidsid['resultstatus']);
	   		}
		else{
		   	$Assessment['ResultStatus']=array('label' => 'Status','table_db'
					=> 'assessment', 'field_db' => 'resultstatus',
					'type_db'=>'enum', 'value' => $ass['resultstatus']);
			}
	   	$Assessment['ExamBoard']=array('label' => 'Board','table_db'
					=> 'eidmid', 'field_db' => 'examboard',
					'type_db'=>'char(3)', 'value' => $eidsid['examboard']);
	   	$Assessment['ExamBoardSyllabusID']=array('label' =>
					'Syllabus','table_db' => 'eidmid', 
					'field_db' => 'examsyallabus',
					'type_db'=>'char(3)', 'value' => $eidsid['examsyllabus']);
	   	$Assessment['Result']=array('label' => 'Result','table_db'
					=> 'eidsid', 'field_db' => 'result', 
					'type_db'=>'', 'value' => $eidsid['result']);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}

	return $Assessments;
	}

function fetchAssessments_short($sid,$eid='%'){
	$Assessments=array();
   	$d_eidsid=mysql_query("SELECT * FROM eidsid WHERE
				student_id='$sid' AND assessment_id LIKE '$eid'");
  	while($eidsid=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)) {
		$eidsid=nullCorrect($eidsid);
		$eid=$eidsid{'assessment_id'};
		$d_ass=mysql_query("SELECT * FROM assessment WHERE id='$eid'");
		$ass=mysql_fetch_array($d_ass,MYSQL_ASSOC);
		$ass=nullCorrect($ass);
		$Assessment['id_db']=$ass['id'];
	   	$Assessment['Course']=array('value'=>$ass['course_id']);
		if($eidsid['subject_id']=='%'){$subject='';}
				else{$subject=$eidsid['subject_id'];}
	   	$Assessment['Subject']=array('value'=>$subject);
		if($eidsid['component_id']=='%'){$component='';}
				else{$component=$eidsid['component_id'];}
	   	$Assessment['SubjectComponent']=array('value'=>$component);
	   	$Assessment['PrintLabel']=array('value'=>$ass['label']);
	   	$Assessment['Result']=array('value'=>$eidsid['result']);
		$Assessment['Result']=nullCorrect($Assessment['Result']);
		$Assessment=nullCorrect($Assessment);
		$Assessments[]=$Assessment;
		}
	return $Assessments;
	}

/*Returns all assdefs of relevance to a cohort*/
function fetch_cohortAssessmentDefinitions($cohort){
	$crid=$cohort['course_id'];
	$stage=$cohort['stage'];
	$year=$cohort['year'];
	$season='S';
	$AssDefs=array();
	$d_assessment=mysql_query("SELECT id FROM assessment
			   WHERE (course_id LIKE '$crid' OR course_id='%') AND 
				(stage LIKE '$stage' OR stage='%') AND
				year LIKE '$year'
				ORDER BY year DESC, stage DESC, creation DESC");
   	while($ass=mysql_fetch_array($d_assessment, MYSQL_ASSOC)){
		$AssDefs[]=fetchAssessmentDefinition($ass['id']);
		}
	return $AssDefs;
	}

/**/
function update_derivation($eid,$der){
	$AssDef=fetchAssessmentDefinition($eid);
	$older=$AssDef['Derivation']['value'];
	$crid=$AssDef['Course']['value'];
	$assyear=$AssDef['Year']['value'];
	if($older!=$der){
		/*identify the assessments with elements and in store in derivation*/
		list($operation,$elements)=parse_derivation($der);
		while(list($index,$element)=each($elements)){
			$d_ass=mysql_query("SELECT id FROM assessment WHERE
						course_id='$crid' AND element='$element' AND year='$assyear'");
			while($ass=mysql_fetch_array($d_ass,MYSQL_ASSOC)){
				$elementeid=$ass['id'];
				/*must specify type=A for all assessments*/
				mysql_query("DELETE FROM derivation WHERE
							resultid='$eid' AND type='A'");
				mysql_query("INSERT INTO derivation (resultid,
					operandid, type, element) VALUES ('$eid','$elementeid','A','$element')");
				trigger_error('Element '.$element.' argument '.$elementeid ,E_USER_WARNING);
				}
			}

		mysql_query("UPDATE assessment SET derivation='$der' WHERE id='$eid';");
		$AssDef['Derivation']['value']=$der;
		$steps=(array)derive_algorithm_steps($der);
		$cohorts=(array)list_course_cohorts($crid);
		$students=array();
		if($older==''){
			while(list($index,$cohort)=each($cohorts)){
				$cohortstudents=(array)listin_cohort($cohort);
				$students=array_merge($students,$cohortstudents);
				}
			}
		else{
			$d_eidsid=mysql_query("SELECT DISTINCT student_id AS id FROM eidsid WHERE
				assessment_id LIKE '$eid'");
			while($student=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
				$students[]=$student;
				}
			}
		while(list($index,$student)=each($students)){
			derive_score($student['id'],$AssDef,$steps);
			}
		}
	}

/**/
function derive_algorithm_steps($der){
	list($operation,$elements)=parse_derivation($der);
	$steps=array();
	while(list($index,$element)=each($elements)){
		if($operation=='SUM' or $operation=='AVE'){
			$step=array('op'=>'+','val'=>$element);
			}
		elseif($operation=='DIF' and $index>0){
			$step=array('op'=>'-','val'=>$element);
			}
		elseif($operation=='DIF' and $index==0){
			$step=array('op'=>'+','val'=>$element);
			}
		$steps[]=$step;
		}
	if($operation=='AVE'){
		$steps[]=array('op'=>'/','val'=>$index);
		}
	return $steps;
	}

/* takes the string $der which is the derivation field of an */
/* assessment and returns the operation (the characters before the */
/* first open bracket) and the elements as an array which are the colon seperated */
/* contents of the brackets*/
function parse_derivation($der){
	$open=strpos($der,'(');
	$operation=substr($der,0,$open);
	$argument=substr($der,$open+1);
	$argument=trim($argument,' )');
	//trigger_error('Function '.$operation.' argument '.$argument,E_USER_WARNING);
	$elements=(array)explode(':',$argument);
	return ($operation,$elements);
	}

/**/
function derive_score($sid,$AssDef,$steps=''){
	$score=0;
	$resultid=$AssDef['id_db'];
	if($steps==''){
		$der=$AssDef['Derivation']['value'];
		$steps=(array)derive_algorithm_steps($der);
		}

	while(list($index,$step)=each($steps)){

		$Assessments=(array)fetchAssessments_short($sid,$operandid);
		}

	return $score;
	}

?>
