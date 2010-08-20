<?php
/**							   new_estimate_action.php
 */

$action='new_estimate.php';

include('scripts/sub_action.php');

$rcrid=$respons[$r]{'course_id'};
$statid=$_POST{'statid'};
$estimate_id=$_POST{'eid'};
$baseline_id=$_POST{'eid1'};

if($sub=='Submit'){
	if(mysql_query("UPDATE assessment SET
		resultstatus='E' WHERE id='$estimate_id';"))	
				{$result[]="Generated new estimate.";}
	else{$error[]="Assessment may not exist!".mysql_error();}

	$AssDef=fetchAssessmentDefinition($estimate_id);
	$resq=$AssDef['ResultQualifier']['value'];
	$grading_grades=$AssDef['GradingScheme']['grades'];
	$crid=$AssDef['Course']['value'];

	$entrydate=date('Y')."-".date('n')."-".date('j');

	$coursebids=array();
	$subjects=list_course_subjects($rcrid);
	foreach($subjects as $subject){
		$coursebids[]=$subject['subject_id'];
		}

   	$d_statvalues=mysql_query("SELECT * FROM statvalues WHERE stats_id='$statid'");
	$stats=array();
	while($statvalue=mysql_fetch_array($d_statvalues,MYSQL_ASSOC)){
		$bid=$statvalue['subject_id'];
		if(!isset($stats["$bid"])){$stats["$bid"]=array();}
		$pid=$statvalue['component_id'];
		if($pid=='%'){$pid='';}
		$stats["$bid"]["$pid"]=$statvalue;
		}

   	$d_eidsid=mysql_query("SELECT * FROM eidsid  WHERE assessment_id='$baseline_id'");
	while($baseline=mysql_fetch_array($d_eidsid,MYSQL_ASSOC)){
		$bid=$baseline['subject_id'];
		$pid=$baseline['component_id'];
		if($pid=='%'){$pid='';}
		$sid=$baseline['student_id'];
		$x=$baseline['result'];
		$dobids=array();
		if($bid=='G' or $bid=='%'){$dobids=$coursebids;}else{$dobids[]=$bid;}
		while(list($index,$bid)=each($dobids)){
   		  if(sizeof($stats["$bid"])>0){
	 		  reset($stats["$bid"]);
	  		  while(list($pid, $stat)=each($stats["$bid"])){
			    $value=$stat['m'] * $x + $stat['c'];
			    $estimate_value=round($value,2);
				$estimate='';
			  	if($grading_grades!=''){
					$estimate=scoreToGrade($estimate_value,$grading_grades);
					}
				if(mysql_query("INSERT INTO eidsid (assessment_id,
					student_id, subject_id, component_id, date, result, value) 
					VALUES ('$estimate_id', '$sid', '$bid', '$pid',
					'$entrydate', '$estimate', '$estimate_value');")){}
				else{$error[]='Entry may already exist!'.mysql_error();}
				}
			  }
			}
	   	}


/*	may need to calculate a result based on method for grades*/
/*	find the markdef_name based on the resultqualifier and method*/
/*	$d_method=mysql_query("SELECT DISTINCT markdef_name FROM method WHERE
		(resultqualifier='$resultq' OR resultqualifier='%') AND
		(method='$method' OR method='%') AND (course_id='$rcrid' OR course_id='%')");
	$markdef_name=mysql_result($d_method,0);*/

	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
